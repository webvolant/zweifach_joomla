<?php defined("_JEXEC") or die(file_get_contents("index.html"));

// Program: Fox Contact for Joomla
// Copyright (C): 2011 Demis Palma
// Documentation: http://www.fox.ra.it/forum/2-documentation.html
// License: Distributed under the terms of the GNU General Public License GNU/GPL v3 http://www.gnu.org/licenses/gpl-3.0.html

$inc_dir = realpath(dirname(__FILE__));
require_once($inc_dir . '/fdatapump.php');
require_once($inc_dir . '/flanghandler.php');
require_once($inc_dir . '/conflicting.php');
require_once($inc_dir . '/flogger.php');

class FieldsBuilder extends FDataPump
{
	public function __construct(&$params, FoxMessageBoard &$messageboard)
	{
		parent::__construct($params, $messageboard);

		$this->ValidateEmail(); // email can have text without being valid
		// Load required js only once
		if (!isset($GLOBALS[$GLOBALS["ext_name"] . '_js_loaded']))
		{
			// The easy way to include chosen. It taunts the page source with tons of embedded javascripts.
			// JHtml::_("jquery.framework"); // jquery
			// JHtml::_('behavior.framework', true); // mootools
			// JHtml::_('formbehavior.chosen', 'select'); // chosen

			// We need jQuery to be loaded before our scripts
			JHtml::_("jquery.framework");

			$foxDocument = FoxDocument::getInstance();

			$min = JFactory::getConfig()->get("debug") ? "" : ".min";
			$foxDocument->addResource(array("root" => "components", "filename" => "foxtext", "type" => "js"));
			$foxDocument->addScript(JUri::base(true) . "/components/" . $GLOBALS["com_name"] . "/js/fileuploader" . $min . ".js");

			$document = JFactory::getDocument();

			// chosen.jquery requires core for Joomla.JText
			$uncompressed = JFactory::getConfig()->get("debug") ? "-uncompressed" : "";
			$document->addScript(JUri::base(true) . "/media/system/js/core" . $uncompressed . ".js");
			$document->addScript(JUri::base(true) . "/media/jui/js/chosen.jquery" . $min . ".js");

			$GLOBALS[$GLOBALS["ext_name"] . '_js_loaded'] = true;
		}

		$this->isvalid = intval($this->ValidateForm()); // Are all fields valid?

		$lang_handler = new FLangHandler();
		if ($lang_handler->HasMessages())
		{
			$messageboard->Append($lang_handler->GetMessages(), FoxMessageBoard::warning);
		}

		$conflict = new FoxConflicting();
		if ($conflict->HasMessages())
		{
			$messageboard->Append($conflict->GetMessages(), FoxMessageBoard::warning);
		}

	}


	public function count_fields(&$fields, $type)
	{
		// Todo: if $type is "text", it count every field starting with "text", so textarea fields are considered "text" field
		// but this is not really a problem
		$result = 0;
		$type_len = strlen($type);
		foreach ($fields as $fname => $fvalue)
		{
			if (
				substr($fname, 0, $type_len) == $type && // item starts with $type
				substr($fname, strlen($fname) - 7) == "display" // item ends with "display"
			)
				++$result;
		}
		return $result;
	}


	public function Show()
	{
		$result = "";
		uasort($this->Fields, "sort_fields");

		foreach ($this->Fields as $key => $field)
		{
			switch ($field['Type'])
			{
				case 'customhtml':
					$result .= $this->BuildCustomHtmlField($key, $field);
					break;
				case 'sender':
				case 'text':
					$result .= $this->BuildTextField($key, $field); //Example: $this->BuildTextField('sender0', $field)
					break;
				case 'dropdown':
					$result .= $this->BuildDropdownField($key, $field); //Example: $this->BuildTextField('dropdown0', $field)
					break;
				case 'textarea':
					$result .= $this->BuildTextareaField($key, $field); //Example: $this->BuildTextField('textarea0', $field)
					break;
				case 'checkbox':
					$result .= $this->BuildCheckboxField($key, $field); //Example: $this->BuildTextField('checkbox0', $field)
					break;
			}

			if (!$field["IsValid"]) $this->MessageBoard->Add(JText::sprintf($GLOBALS["COM_NAME"] . '_ERR_INVALID_VALUE', $field["Name"]), FoxMessageBoard::error);
		}

		return $result;
	}


	protected function LoadFields()
	{
		$fields = $this->Params->toArray();
		$text_count = $this->count_fields($fields, "text");
		$dropdown_count = $this->count_fields($fields, "dropdown");
		$textarea_count = $this->count_fields($fields, "textarea");
		$checkbox_count = $this->count_fields($fields, "checkbox");

		// Loads parameters and $_POST data
		$this->LoadField("labels", "");
		$this->LoadField("customhtml", 0);
		for ($n = 0; $n < 2; ++$n) $this->LoadField("sender", $n);
		for ($n = 0; $n < $text_count; ++$n) $this->LoadField("text", $n);
		for ($n = 0; $n < $dropdown_count; ++$n) $this->LoadField("dropdown", $n);
		for ($n = 0; $n < $textarea_count; ++$n) $this->LoadField("textarea", $n);
		for ($n = 0; $n < $checkbox_count; ++$n) $this->LoadField("checkbox", $n);
		$this->LoadField("customhtml", 1);
	}


	protected function LoadField($type, $number) // Example: 'text', '0'
	{
		// Load component parameters
		$name = $type . (string)$number; // Example: 'text0'
		// If not to be displayed, it's useless to continue reading other values
		if (!parent::LoadField($type, $name)) return false;
		// Load data
		$this->Fields[$name]['Value'] = $this->JInput->post->get($this->Fields[$name]['PostName'], NULL, "string");

		// Additional manipulations
		if ($this->Fields[$name]['Value'] == $this->Fields[$name]['Name']) // Example: Field='Your name' Value='Your name'
		{
			// Seems like a submission from the module without filling the field, so let's invalidate the value!
			$this->Fields[$name]['Value'] = "";
		}

		// Validation after *all* fields are loaded and manipulated
		$this->Fields[$name]['IsValid'] = intval($this->ValidateField($this->Fields[$name]['Value'], $this->Fields[$name]['Display']));

		// Checkboxes need to be manipulated after validation, otherwise a JNO value will be considered valid
		// Checkboxes have only JYES or empty values. Translate empty to JNO
		if ($type == "checkbox" && $this->Fields[$name]['Value'] == "") $this->Fields[$name]['Value'] = JText::_('JNO');

		return true;
	}


	private function BuildCustomHtmlField($key, &$field)
	{
		// When the field has an empty text to display, do not insert the container neither
		if (empty($field['Name'])) return "";

		$result = '<div class="control-group">' .
			'<div class="controls">' .
			'<div>' . PHP_EOL .
			$field['Name'] . PHP_EOL .
			"</div>" .
			"</div>" .
			"</div>" . PHP_EOL .
			PHP_EOL;

		return $result;
	}


	// Build a single Text field
	private function BuildTextField($key, &$field)
	{
		//$myownclass = preg_replace("/[^a-z0-9]/", "", strtolower($field["Name"]));

		$this->CreateStandardLabel($field);

		$result = '<div class="control-group' . $this->TextStyleByValidation($field) . '">' . PHP_EOL .
			$this->LabelHtmlCode . PHP_EOL .
			'<div class="controls">' .
			'<input ' .
			// 'class="' . $this->TextStyleByValidation($field) . ' ' . $myownclass . '" ' .
			//'class="' . $this->TextStyleByValidation($field) . '" ' .
			'type="text" ' .
			'value="' . $this->FieldValue . '" ' .
			'title="' . $field['Name'] . '" ' .
			'name="' . $field['PostName'] . '" ' .
			$this->JSCode .
			'/>' .
			$this->DescriptionByValidation($field) . // Example: *
			'</div>' . PHP_EOL . // controls
			'</div>' . PHP_EOL . // control-group
			PHP_EOL;

		return $result;
	}


	// Build a single Dropdown box field
	private function BuildDropdownField($key, &$field)
	{
		$this->CreateStandardLabel($field);

		$placeholder = $this->Params->get("labelsdisplay") ? " " : $field['Name'];
		$result = '<div class="control-group' . $this->TextStyleByValidation($field) . '">' . PHP_EOL .
			$this->LabelHtmlCode . PHP_EOL .
			'<div class="controls">' .
			'<select ' .
			'class="fox_select" ' .
			'data-placeholder="' . $placeholder . '"' .
			'name="' . $field['PostName'] . '" ' .
			'>' . PHP_EOL;

		// Insert an empty option
		$result .= '<option value=""></option>';

		// and the actual options
		$options = explode(",", $field['Values']);
		foreach ($options as $option)
		{
			$result .= "<option value=\"" . $option . "\"";
			if ($field['Value'] === $option && !empty($option))
			{
				$result .= " selected ";
			}
			$result .= ">" . $option . "</option>";
		}
		$result .= PHP_EOL . "</select>" .
			$this->DescriptionByValidation($field) .
			'</div>' . PHP_EOL . // controls
			"</div>" . PHP_EOL . // control-group
			PHP_EOL;

		return $result;
	}


	// Build a single Check Box field
	private function BuildCheckboxField($key, &$field)
	{
		// Here, validation will be successful, because there aren't post data, but it isn't a good right to activate che checkbox with the check
		// if (intval($this->FieldsBuilder->Fields[$index]['Value'])) $this->msg .= "checked=\"\"";
		if ($field['Value'] == JText::_('JYES')) $checked = 'checked=""';
		else $checked = "";

		$this->CreateSpacerLabel();

		$result = '<div class="control-group' . $this->TextStyleByValidation($field) . '">' . PHP_EOL .
			$this->LabelHtmlCode . PHP_EOL .
			'<div class="controls">' .
			'<label class="checkbox">' .
			'<input ' .
			'type="checkbox" ' .
			"value=\"" . JText::_('JYES') . "\" " .
			$checked .
			'name="' . $field['PostName'] . '" ' .
			'id="c' . $field['PostName'] . '" ' .
			'/>' .
			$this->AdditionalDescription($field['Display']) . // Asterisk
			$field['Name'] .
			$this->DescriptionByValidation($field) . // Nested span with validation red asterisk
			'</label>' .
			'</div>' . PHP_EOL .
			'</div>' . PHP_EOL .
			PHP_EOL;

		return $result;
	}


	// Build a Textarea field
	private function BuildTextareaField($key, &$field)
	{
		$this->CreateStandardLabel($field);

		$result = '<div class="control-group' . $this->TextStyleByValidation($field) . '">' . PHP_EOL .
			$this->LabelHtmlCode . PHP_EOL .
			'<div class="controls">' .
			"<textarea " .
			'rows="10" ' .
			'cols="30" ' .
			'name="' . $field['PostName'] . '" ' .
			'title="' . $field['Name'] . '" ' .
			$this->JSCode .
			">" .
			$this->FieldValue . // Inner Text
			"</textarea>" .
			$this->DescriptionByValidation($field) .
			'</div>' . PHP_EOL . // controls
			'</div>' . PHP_EOL . // control-group
			PHP_EOL;

		return $result;

	}


	// Check a single field and return a string good for html output
	function DescriptionByValidation(&$field)
	{
		return $field['IsValid'] ? "" : (" <span class=\"asterisk\"></span>");
	}


	// Check a single field and return a string good for html output
	function CheckboxStyleByValidation(&$field)
	{
		if (!$this->Submitted) return "foxcheckbox";
		// Return a green or red border
		return $field['IsValid'] ? "validcheckbox" : "invalidcheckbox";
	}


	// Check a single field and return a string good for html output
	protected function TextStyleByValidation(&$field)
	{
		// No post data = first time here. return a grey border
		if (!$this->Submitted) return "";
		// Return a green or red border
		return $field['IsValid'] ? " success" : " error";
	}


	function ValidateForm()
	{
		$result = true;

		// Validate default fields
		$result &= $this->ValidateGroup("sender");
		// Validate Text fields
		$result &= $this->ValidateGroup("text");
		// Validate Dropdown fields
		$result &= $this->ValidateGroup("dropdown");
		// Validate Check Boxes
		$result &= $this->ValidateGroup("checkbox");
		// Validate text areas
		$result &= $this->ValidateGroup("textarea");

		return $result;
	}


	// $family can be 'text', 'dropdown', 'textarea' or 'checkbox'
	function ValidateGroup($family)
	{
		$result = true;

		for ($l = 0; $l < 10; ++$l)
		{
			// isset($this->Fields[$family . $l]) is needed to fix following error displayed when running on wamp server
			// Notice: Undefined index: sender[...] in C:\wamp\[...]\helpers\fieldsbuilder.php
			if (isset($this->Fields[$family . $l]) && $this->Fields[$family . $l]['Display'])
			{
				$result &= $this->Fields[$family . $l]['IsValid'];
			}
		}

		return $result;
	}


	// Check a single field and return a boolean value
	function ValidateField($fieldvalue, $fieldtype)
	{
		// Params:
		// $fieldvalue is a string with the text filled by user
		// $fieldtype can be 0 = unused, 1 = optional, 2 = required
		// S | R | F | V   (Submitted | Required | Filled | Valid)
		// 0 | 0 | 0 | 1
		// 0 | 0 | 1 | 1
		// 0 | 1 | 0 | 1
		// 0 | 1 | 1 | 1
		// 1 | 0 | 0 | 1
		// 1 | 0 | 1 | 1
		// 1 | 1 | 0 | 0
		// 1 | 1 | 1 | 1
		return !($this->Submitted && ($fieldtype == 2) && empty($fieldvalue));
	}


	function ValidateEmail()
	{
		// data aren't destinated to this form
		if (!isset($_POST[$this->GetId()])) return;

		// email field is disabled
		if (!isset($this->Fields['sender1'])) return;

		// email field is empty and optional
		if (empty($this->Fields['sender1']['Value']) && $this->Fields['sender1']['Display'] == 1) return;

		if (!isset($this->Fields['sender1']['Value'])) return;

		// Some nowadays servers doesn't have the function filter_var(). We need to use a regular expression.
		// $this->Fields['sender1']['IsValid'] &= (bool)strlen(filter_var($this->Fields['sender1']['Value'], FILTER_VALIDATE_EMAIL));

		// Check the email syntax (http://www.regular-expressions.info/email.html)
		$this->Fields['sender1']['IsValid'] &= (preg_match('/^[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,4}$/', strtolower($this->Fields['sender1']['Value'])) == 1);

		// Check mx record
		$config = JComponentHelper::getParams("com_foxcontact");
		$mode = $config->get("use_dns", "disabled");
		if (method_exists("FieldsBuilder", $mode))
		{
			$this->$mode();
		}
	}


	function dns_check()
	{
		// Check mx record
		if (empty($this->Fields['sender1']['Value'])) return;

		$parts = explode("@", $this->Fields['sender1']['Value']);
		$domain = array_pop($parts);
		if (!empty($domain))
			$this->Fields['sender1']['IsValid'] &= checkdnsrr($domain, "MX");
	}
}


function sort_fields($a, $b)
{
	return $a["Order"] - $b["Order"];
}


class fieldsbuilderCheckEnvironment
{
	protected $InstallLog;


	public function __construct()
	{
		$this->InstallLog = new FLogger("fieldsbuilder", "install");

		// Load the global configuration
		$params = JComponentHelper::getParams("com_foxcontact")->toObject();

		// Test environment and update the configuration
		$this->test_addresses($params);
		$this->test_dns($params);

		// Save the configuration
		// Improve the performance by referencing the record using the index "element + client_id"
		// UPDATE #__extensions SET params = json_encode($params) WHERE element = com_foxcontact AND client_id = 1
		$table = JTable::getInstance("extension");
		$table->load(array("element" => "com_foxcontact", "client_id" => 1));
		$table->bind(array("params" => json_encode($params)));
		$result = $table->check() && $table->store();

		return $result;
	}


	private function test_dns($params)
	{
		$this->InstallLog->Write("--- Determining if this system is able to query DNS records ---");

		if (!function_exists("checkdnsrr"))
		{
			$this->InstallLog->Write("checkdnsrr function doesn't exist.");
			$params->use_dns = "0";
			return;
		}
		$this->InstallLog->Write("checkdnsrr function found. Let's see if it works.");

		// Check mx record
		$record_found = checkdnsrr("fox.ra.it", "MX");
		$this->InstallLog->Write("testing function [checkdnsrr]... [" . intval($record_found) . "]");
		$result = $record_found ? "dns_check" : "0";
		$this->InstallLog->Write("--- Method choosen to query DNS records is [$result] ---");

		$params->use_dns = $result;
	}


	private function test_addresses(&$params)
	{
		isset($params->adminemailfrom) or $params->adminemailfrom = new stdClass();
		isset($params->adminemailreplyto) or $params->adminemailreplyto = new stdClass();
		isset($params->submitteremailfrom) or $params->submitteremailfrom = new stdClass();
		isset($params->submitteremailreplyto) or $params->submitteremailreplyto = new stdClass();

		$params->adminemailfrom->select = "admin";
		$params->adminemailreplyto->select = "submitter";

		$params->submitteremailfrom->select = "admin";
		$params->submitteremailreplyto->select = "admin";

		$application = JFactory::getApplication();
		// SMTP authentication may require that the sender address is the same that the auth username
		if ($application->getCfg("mailer") == "smtp" && (bool)$application->getCfg("smtpauth") && strpos($application->getCfg("smtpuser"), "@") !== false)
		{
			$params->adminemailfrom->select = "custom";
			$params->adminemailfrom->name = $application->getCfg("fromname");
			$params->adminemailfrom->email = $application->getCfg("smtpuser");

			$params->submitteremailfrom->select = "custom";
			$params->submitteremailfrom->name = $application->getCfg("fromname");
			$params->submitteremailfrom->email = $application->getCfg("smtpuser");
		}
	}

}
