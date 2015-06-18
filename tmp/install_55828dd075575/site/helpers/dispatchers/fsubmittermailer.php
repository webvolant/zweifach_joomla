<?php defined("_JEXEC") or die(file_get_contents("index.html"));

// Program: Fox Contact for Joomla
// Copyright (C): 2011 Demis Palma
// Documentation: http://www.fox.ra.it/forum/2-documentation.html
// License: Distributed under the terms of the GNU General Public License GNU/GPL v3 http://www.gnu.org/licenses/gpl-3.0.html

$inc_dir = realpath(dirname(__FILE__));
require_once($inc_dir . '/fdispatcher.php');

class FSubmitterMailer extends FDispatcher
{
	public function Process()
	{
		$application = JFactory::getApplication();
		$copy_to_submitter =
			$application->input->post->get($this->SafeName("copy_to_submitter" . $this->GetId()), false, "bool") || // Checkbox is enabled
			($this->Params->get("copy_to_submitter", null) == 1); // always send a copy parameter

		if (!$copy_to_submitter || !isset($this->FieldsBuilder->Fields['sender1']) || empty($this->FieldsBuilder->Fields['sender1']['Value']))
		{
			$jsession = JFactory::getSession();
			$namespace = "foxcontact_" . $application->owner . "_" . $application->oid;
			$jsession->clear("filelist", $namespace);

			//JLog::add("Copy email for the submitter skipped.", JLog::INFO, get_class($this));
			return true;
		}

		$mail = JFactory::getMailer();

		$this->set_from($mail);
		$this->set_to($mail);
		$mail->setSubject(JMailHelper::cleanSubject($this->Params->get("email_copy_subject", "")));

		// Body
		$body = $this->Params->get("email_copy_text", "") . PHP_EOL;
		// a blank line
		$body .= PHP_EOL;

		if ($this->Params->get("email_copy_summary", null))
		{
			$body .= $this->body();
			$body .= $this->attachments();
			$body .= PHP_EOL;
		}

		// A null body will raise a mail error, so always add at least a signature.
		$body .= "------" . PHP_EOL . JFactory::getConfig()->get("sitename") . PHP_EOL;

		$body = JMailHelper::cleanBody($body);
		$mail->setBody($body);

		// Clear file list for the next submission of the same users
		$jsession = JFactory::getSession();
		$namespace = "foxcontact_" . $application->owner . "_" . $application->oid;
		$jsession->clear("filelist", $namespace);

		$sent = $this->send($mail);
		if ($sent)
		{
			// Notify email send success
			$this->Logger->Write("Copy email sent.");
		}

		return $sent;
	}


	private function set_from(&$mail)
	{
		$emailhelper = new FoxEmailHelper($this->Params);
		$config = JComponentHelper::getParams("com_foxcontact");

		$submitteremailfrom = $config->get("submitteremailfrom");
		$from = $emailhelper->convert($submitteremailfrom);
		$mail->setSender($from);

		$submitteremailreplyto = $config->get("submitteremailreplyto");
		$replyto = $emailhelper->convert($submitteremailreplyto);
		// In Joomla 1.7 From and Reply-to fields is set by default to the Global admin email
		// but a call to setSender() won't change the Reply-to field
		$mail->ClearReplyTos();
		$mail->addReplyTo($replyto[0], $replyto[1]);
	}


	private function set_to(&$mail)
	{
		$addr = $this->FieldsBuilder->Fields['sender1']['Value'];
		$mail->addRecipient(JMailHelper::cleanAddress($addr));
	}


	protected function attachments()
	{
		$result = "";

		// a further separation from signature above
		if (count($this->FileList))
		{
			$result .= JText::_($GLOBALS["COM_NAME"] . "_ATTACHMENTS") . PHP_EOL;
		}

		foreach ($this->FileList as $file)
		{
			$result .= $file["realname"] . PHP_EOL;
		}

		return $result;
	}

}
