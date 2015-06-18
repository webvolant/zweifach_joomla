<?php defined("_JEXEC") or die(file_get_contents("index.html"));

$inc_dir = realpath(__DIR__ . "/..");
require_once($inc_dir . "/fdatapump.php");
require_once($inc_dir . "/flogger.php");
require_once($inc_dir . "/emailhelper.php");

// JMailHelper provides security functions
jimport("joomla.mail.helper");

// Todo: Dispatchers don't need FDataPump, except for FSubmitterMailer::Process() which uses SafeName() and GetId()
abstract class FDispatcher extends FDataPump
{
	protected $FieldsBuilder;
	protected $FileList;

	/**
	 * Parameters of the running component or module
	 *
	 * @var JRegistry
	 */
	//protected $Params;

	/**
	 * @var FoxMessageBoard
	 */
	//protected $MessageBoard;

	abstract public function Process();


	protected function LoadFields()
	{
	}


	public function __construct(&$params, FoxMessageBoard &$messageboard, &$fieldsbuilder)
	{
		//$this->Params = & $params;
		//$this->MessageBoard = & $messageboard;

		parent::__construct($params, $messageboard);

		/*
		JLog::addLogger(array(
		'text_file' => 'foxcontact.log.php',
		"text_entry_format" => "{DATE}\t{TIME}\t{PRIORITY}\t{CATEGORY}\t{MESSAGE}"
		));
		*/
		$this->FieldsBuilder = $fieldsbuilder;
		$this->Logger = new FLogger();

		// Read attachments file list from the session
		$jsession = JFactory::getSession();
		$namespace = "foxcontact_" . JFactory::getApplication()->owner . "_" . JFactory::getApplication()->oid;
		$this->FileList = $jsession->get("filelist", array(), $namespace);
	}


	protected function submittername()
	{
		// Uses the user sender name. If the field is disabled, uses Joomla admin name
		return
			isset($this->FieldsBuilder->Fields['sender0']) ?
				$this->FieldsBuilder->Fields['sender0']['Value'] :
				JFactory::getConfig()->get("fromname");
	}


	protected function submitteraddress()
	{
		// Bug: http://www.fox.ra.it/forum/3-bugs/2399-error-when-email-is-optional-and-field-is-left-empty.html
		// $from = isset($this->FieldsBuilder->Fields['sender1']['Value']) ? $this->FieldsBuilder->Fields['sender1']['Value'] : JFactory::getApplication()->getCfg("mailfrom");

		// If submitter address is present and not empty, we can use it
		// otherwise system global address will be used
		$addr =
			isset($this->FieldsBuilder->Fields['sender1']['Value']) &&
				!empty($this->FieldsBuilder->Fields['sender1']['Value']) ?
				$this->FieldsBuilder->Fields['sender1']['Value'] :
				JFactory::getConfig()->get("mailfrom");

		return JMailHelper::cleanAddress($addr);
	}


	protected function body()
	{
		$result = "";
		foreach ($this->FieldsBuilder->Fields as $key => $field)
		{
			switch ($field['Type'])
			{
				case 'sender':
				case 'text':
				case 'textarea':
				case 'dropdown':
				case 'checkbox':
					$result .= $this->AddToBody($field);
				// default:
				// do nothing;
			}
		}

		// a blank line
		$result .= PHP_EOL;
		return $result;
	}


	protected function AddToBody(&$field)
	{
		if (!$field['Display']) return "";
		//return $field["Name"] . ": " . $field["Value"] . PHP_EOL;
		return "*" . JFilterInput::getInstance()->clean($field["Name"], "") . "*" . PHP_EOL . JFilterInput::getInstance()->clean($field["Value"], "") . PHP_EOL . PHP_EOL;
	}


	protected function CurrentURL()
	{
		$url = 'http';
		if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") $url .= "s";
		$url .= "://";
		$url .= $_SERVER["SERVER_NAME"];
		if ($_SERVER["SERVER_PORT"] != "80") $url .= ":" . $_SERVER["SERVER_PORT"];
		$url .= $_SERVER["REQUEST_URI"];
		return $url;
	}


	protected function ClientIPaddress()
	{
		if (isset($_SERVER["REMOTE_ADDR"])) return $_SERVER["REMOTE_ADDR"];
		if (isset($_SERVER["HTTP_X_FORWARDED_FOR"])) return $_SERVER["HTTP_X_FORWARDED_FOR"];
		if (isset($_SERVER["HTTP_CLIENT_IP"])) return $_SERVER["HTTP_CLIENT_IP"];
		return "?";
	}


	/**
	 * Send the email
	 *
	 * @param JMail $mail Joomla mailer
	 * @return bool true on success
	 */
	protected function send(&$mail)
	{
		if (($error = $mail->Send()) !== true)
		{
			//$info = empty($mail->ErrorInfo) ? $error->getMessage() : $mail->ErrorInfo;
			// Obtaining the problem information from Joomla mailer is a nightmare
			if (is_object($error))
			{
				// It is an instance of JError. Calls the getMessage() method
				$info = $error->getMessage();
			}
			else if (!empty($mail->ErrorInfo))
			{
				// Send() returned false. If a $mail->ErrorInfo property is set, this is the cause
				$info = $mail->ErrorInfo;
			}
			else
			{
				// Send() returned false, but $mail->ErrorInfo is empty. The only reasonable cause can be $mailonline = 0
				$info= JText::_("JLIB_MAIL_FUNCTION_OFFLINE");
			}

			$msg = JText::_($GLOBALS["COM_NAME"] . "_ERR_SENDING_MAIL") . ". " . $info;
			$this->MessageBoard->Add($msg, FoxMessageBoard::error);
			$this->Logger->Write($msg);
			//JLog::add($msg, JLog::ERROR, get_class($this));
			return false;
		}

		//JLog::add("Email sent.", JLog::INFO, get_class($this));
		return true;
	}
}
