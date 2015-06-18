<?php defined("_JEXEC") or die(file_get_contents("index.html"));

// Program: Fox Contact for Joomla
// Copyright (C): 2011 Demis Palma
// Documentation: http://www.fox.ra.it/forum/2-documentation.html
// License: Distributed under the terms of the GNU General Public License GNU/GPL v3 http://www.gnu.org/licenses/gpl-3.0.html

class FLangHandler
	{
	protected $lang;
	protected $messages = array();

	function __construct()
		{
		$this->lang = JFactory::getLanguage();

		$this->check_partial();
		$this->check_missing();
		}


	public function HasMessages()
		{
		return (bool)count($this->messages);
		}


	public function GetMessages()
		{
		return $this->messages;
		}


	protected function check_partial()
		{
		if (intval(JText::_($GLOBALS["COM_NAME"] . '_PARTIAL')))
			{
			// Translation string is 1
			$this->messages[] = $this->lang->get("name") . " translation is still incomplete. Please consider to contribute by completing and sharing your own translation. <a href=\"http://www.fox.ra.it/forum/19-languages-and-translations/1265-how-to-write-your-own-translation.html\">Learn more</a>.";
			}
		}


	protected function check_missing()
		{
		$filename = JPATH_SITE . "/language/" . $this->lang->get("tag") . "/" . $this->lang->get("tag") . "." . $GLOBALS["com_name"] . ".ini";
		if (!file_exists($filename))
			{
			$this->messages[] = $this->lang->get("name") . " translation is still missing. Please consider to contribute by writing and sharing your own translation. <a href=\"http://www.fox.ra.it/forum/19-languages-and-translations/1265-how-to-write-your-own-translation.html\">Learn more</a>.";
			// Ok, it is missing. Maybe it is available but hasn't been installed?
			$this->check_availability();
			}
		}


	private function check_availability()
		{
		$filename = JPATH_ADMINISTRATOR . '/components/' . $GLOBALS["com_name"] . "/" . $GLOBALS["ext_name"] . '.xml';
		$xml = JFactory::getXML($filename);

		if (!$xml)
			{
			// Todo: log this event
			//$this->messages[] = "Can't load extension xml file";
			}
		else
			{
			foreach ($xml->languages->language as $l)
				{
				if (strpos((string)$l, $this->lang->get("tag")) === 0)
					{
					$this->messages = array();
					$this->messages[] = $this->lang->get("name") . " translation has not been installed, but <strong>is available</strong>. To fix this problem simply install this extension once again, without uninstalling it. <a href=\"http://www.fox.ra.it/forum/19-languages-and-translations/2886-my-language-is-available-but-it-hasnt-been-installed.html\">Learn more</a>.";
					break;
					}
				}
			}

		}

	}
