/*
if (typeof Joomla == 'undefined')
{
	Joomla = {};
	Joomla.JText =
	{
		strings:{},
		'_':function (key, def)
		{
			return typeof this.strings[key.toUpperCase()] !== 'undefined' ? this.strings[key.toUpperCase()] : def;
		},
		load:function (object)
		{
			for (var key in object)
			{
				this.strings[key.toUpperCase()] = object[key];
			}
			return this;
		}
	};
}
*/
/*
(function ()
{
	Joomla.JText.load(
		{
			"COM_FOXCONTACT_BROWSE_FILES":'<?php echo JText::_("COM_FOXCONTACT_BROWSE_FILES"); ?>',
			"JCANCEL":'<?php echo JText::_("JCANCEL"); ?>',
			"COM_FOXCONTACT_FAILED":'<?php echo JText::_("COM_FOXCONTACT_FAILED"); ?>',
			"COM_FOXCONTACT_SUCCESS":'<?php echo JText::_("COM_FOXCONTACT_SUCCESS"); ?>',
			"COM_FOXCONTACT_NO_RESULTS_MATCH":'<?php echo JText::_("COM_FOXCONTACT_NO_RESULTS_MATCH"); ?>'
		}
	);
})();
*/

jQuery(document).ready(function ($)
{
/*
	Joomla.JText.load(
		{
			"COM_FOXCONTACT_BROWSE_FILES":'<?php echo JText::_("COM_FOXCONTACT_BROWSE_FILES"); ?>',
			"JCANCEL":'<?php echo JText::_("JCANCEL"); ?>',
			"COM_FOXCONTACT_FAILED":'<?php echo JText::_("COM_FOXCONTACT_FAILED"); ?>',
			"COM_FOXCONTACT_SUCCESS":'<?php echo JText::_("COM_FOXCONTACT_SUCCESS"); ?>',
			"COM_FOXCONTACT_NO_RESULTS_MATCH":'<?php echo JText::_("COM_FOXCONTACT_NO_RESULTS_MATCH"); ?>'
		}
	);
*/
	jQuery('.fox_select').chosen(
		{
			disable_search_threshold:10,
			allow_single_deselect:true,
			no_results_text:'<?php echo JText::_("COM_FOXCONTACT_NO_RESULTS_MATCH"); ?>'
		});
});

/*
jQuery(document).ready(function($)
{
	if (document.getElementsByTagName("base").length)
	{
		alert('<?php echo JText::_("COM_FOXCONTACT_ERR_CONFLICTING_EXTENSION"); ?>');
		window.location = "http://www.fox.ra.it/forum/24-troubleshooting/8840-form-doesn-t-send-email-and-redirects-to-a-different-page.html";
	}
});
*/
