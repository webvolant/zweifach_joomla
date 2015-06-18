<?php defined("_JEXEC") or die(file_get_contents("index.html"));

// Program: Fox Contact for Joomla
// Copyright (C): 2011 Demis Palma
// Documentation: http://www.fox.ra.it/forum/2-documentation.html
// License: Distributed under the terms of the GNU General Public License GNU/GPL v3 http://www.gnu.org/licenses/gpl-3.0.html

jimport("joomla.filesystem.file");
jimport("joomla.filesystem.folder");
$inc_dir = realpath(dirname(__FILE__));
require_once($inc_dir . '/fdatapump.php');
require_once($inc_dir . '/fmimetype.php');

define('KB', 1024);

class FUploader extends FDataPump
{

	public function __construct(&$params, FoxMessageBoard &$messageboard)
	{
		parent::__construct($params, $messageboard);

		$this->Name = "FFilePump";
		$this->isvalid = intval($this->DoUpload());
	}


	protected function LoadFields()
	{
		// Loads parameters and $_POST data
		$this->LoadField("upload", NULL);
	}


	protected function DoUpload()
	{
		//Retrieve file details from uploaded file, sent from upload form
		$file = JFactory::getApplication()->input->files->get("foxstdupload", array());

		// $file is null when a browser with javascipt didn't send $_FILES at all
		// $file['error'] is UPLOAD_ERR_NO_FILE when a browser without javascipt sent $_FILES empty
		if (!$this->Submitted || !$file || $file['error'] == UPLOAD_ERR_NO_FILE) return true;

		// Security issue: when upload is disabled, stop here
		if (!(bool)$this->Params->get("uploaddisplay", 0)) return true;

		$uploadDirectory = JPATH_SITE . "/components/" . $GLOBALS["com_name"] . "/uploads/";

		if (!is_writable($uploadDirectory))
		{
			$this->MessageBoard->Add(JText::_($GLOBALS["COM_NAME"] . '_ERR_DIR_NOT_WRITABLE'), FoxMessageBoard::error);
			return false;
		}

		// Check for http $_FILES upload errors
		if ($file['error'])
		{
			// case 1 UPLOAD_ERR_INI_SIZE: 'The uploaded file exceeds the upload_max_filesize directive in php.ini';
			// case 2 UPLOAD_ERR_FORM_SIZE: 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form';
			// case 3 UPLOAD_ERR_PARTIAL: 'The uploaded file was only partially uploaded';
			// case 4 UPLOAD_ERR_NO_FILE: 'No file was uploaded';
			// case 6 UPLOAD_ERR_NO_TMP_DIR: 'Missing a temporary folder';
			// case 7 UPLOAD_ERR_CANT_WRITE: 'Failed to write file to disk';
			// case 8 UPLOAD_ERR_EXTENSION: 'File upload stopped by extension';
			$this->MessageBoard->Add(JText::sprintf($GLOBALS["COM_NAME"] . '_ERR_UPLOAD', $file['error']), FoxMessageBoard::error);

			return false;
		}

		// Check file size
		$size = $file['size'];
		if ($size == 0) // It must be > 0
		{
			$this->MessageBoard->Add(JText::_($GLOBALS["COM_NAME"] . '_ERR_FILE_EMPTY'), FoxMessageBoard::error);
			return false;
		}
		$max_filesize = intval($this->Params->get("uploadmax_file_size", "0")) * KB;
		if ($size > $max_filesize) // and < max limit
		{
			$this->MessageBoard->Add(JText::_($GLOBALS["COM_NAME"] . '_ERR_FILE_TOO_LARGE'), FoxMessageBoard::error);
			return false;
		}

		$mimetype = new FMimeType();
		if (!$mimetype->Check($file["tmp_name"], $this->Params))
		{
			// No need to delete the file uploaded
			$this->MessageBoard->Add(JText::_($GLOBALS["COM_NAME"] . '_ERR_MIME') . " [" . $mimetype->Mimetype . "]", FoxMessageBoard::error);
			return false;
		}

		// Security issue: block scripts based on their content
		$content = file_get_contents($file["tmp_name"]);
		if (strpos($content, '<?php') !== false)
		{
			// contains php directive
			$this->MessageBoard->Add(JText::_($GLOBALS["COM_NAME"] . '_ERR_MIME') . " [forbidden content]", FoxMessageBoard::error);
			return false;
		}

		// Security issue: block scripts based on their extension
		$forbidden_extensions = '/^ph(p[345st]?|t|tml|ar)$/'; // php|php3|php4|php5|phps|phpt|pht|phtml|phar
		$extension = pathinfo($file["name"], PATHINFO_EXTENSION);
		if (preg_match($forbidden_extensions, $extension))
		{
			// dangerous file extension
			$this->MessageBoard->Add("[forbidden extension]", FoxMessageBoard::error);
			return false;
		}

		//Import filesystem libraries. Perhaps not necessary, but does not hurt
		jimport('joomla.filesystem.file');

		//Clean up filename to get rid of strange characters like spaces and others
		// Clean file name. Can't use JFile::makeSafe because it accepts spaces which husts the attach list in the email footer
		$filename = preg_replace("/[^\w\.-_]/", "_", $file["name"]);

		// Assign a random unique id to the file name, to avoid that lamers can force the server to execute their uploaded shit
		$filename = uniqid() . "-" . $filename;
		$full_filename = $uploadDirectory . $filename;

		if (!JFile::upload($file["tmp_name"], $full_filename)) return false;

		// Security issue: wrap the uploaded file in a zip shell to avoid script to be executed
		if (class_exists("ZipArchive"))
		{
			// Zip library is callable
			$zip = new ZipArchive();
			// Replace the extension with .zip
			$parts = pathinfo($full_filename);
			$zipname = $parts["dirname"] . "/" . $parts["filename"] . ".zip";
			// Create the zip archive
			if ($zip->open($zipname, ZIPARCHIVE::CREATE) && $zip->addFromString($filename, $content) && $zip->close())
		{
				unlink($full_filename);
				// Replace the file name used in the session list
				$filename = $parts["filename"] . ".zip";
		}
		}

		// Upload successful. Add the element to the uploads list
		$jsession = JFactory::getSession();
		$namespace = "foxcontact_" . $this->application->owner . "_" . $this->application->oid;

		// Read the list from the session
		$filelist = $jsession->get("filelist", array(), $namespace);
		// Append this file to the list
		$filelist[] = array(
			"filename" => $filename,
			"realname" => $file["name"],
			"size" => $size
		);
		// Save the new list on the session
		$jsession->set("filelist", $filelist, $namespace);

		return true;
	}

}
