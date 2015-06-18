<?php defined("_JEXEC") or die(file_get_contents("index.html"));

// Program: Fox Contact for Joomla
// Copyright (C): 2011 Demis Palma
// Documentation: http://www.fox.ra.it/forum/2-documentation.html
// License: Distributed under the terms of the GNU General Public License GNU/GPL v3 http://www.gnu.org/licenses/gpl-3.0.html

class FoxEmailHelper
{
	protected $Params;

	public function __construct(&$params)
	{
		$this->Params = $params;
	}

	public function convert($data)
	{
		return $this->{$data->select}($data);
	}

	public function submitter($data)
	{
		$application = JFactory::getApplication();
		$name = "_" . md5($this->Params->get("sender0") . $application->cid . $application->mid);
		$name = JRequest::getVar($name, NULL, "POST");
		$address = "_" . md5($this->Params->get("sender1") . $application->cid . $application->mid);
		$address = JRequest::getVar($address, NULL, "POST");
		return array($address, $name);
	}

	public function admin($data)
	{
		$application = JFactory::getApplication();
		$name = $application->getCfg("fromname");
		$address = $application->getCfg("mailfrom");
		return array($address, $name);
	}

	public function custom($data)
	{
		return array($data->email, $data->name);
	}
}


