if (typeof Fox == 'undefined')
{
	Fox = {};
	Fox.Text =
	{
		strings: {},
		get: function (key)
		{
			return this.strings[key];
		},
		add: function (object)
		{
			for (var key in object)
			{
				this.strings[key] = object[key];
			}
			return this;
		}
	};
}


(function ()
{
	Fox.Text.add(
		{
			"JCANCEL": '<?php echo JText::_("JCANCEL"); ?>',
			"COM_FOXCONTACT_BROWSE_FILES": '<?php echo JText::_("COM_FOXCONTACT_BROWSE_FILES"); ?>',
			"COM_FOXCONTACT_FAILED": '<?php echo JText::_("COM_FOXCONTACT_FAILED"); ?>',
			"COM_FOXCONTACT_SUCCESS": '<?php echo JText::_("COM_FOXCONTACT_SUCCESS"); ?>',
			"COM_FOXCONTACT_NO_RESULTS_MATCH": '<?php echo JText::_("COM_FOXCONTACT_NO_RESULTS_MATCH"); ?>',
			"COM_FOXCONTACT_REMOVE_ALT": '<?php echo JText::_("COM_FOXCONTACT_REMOVE_ALT"); ?>',
			"COM_FOXCONTACT_REMOVE_TITLE": '<?php echo JText::_("COM_FOXCONTACT_REMOVE_TITLE"); ?>'
		}
	);
})();


jQuery(document).ready(function ($)
{
	jQuery('.fox_select').chosen(
		{
			disable_search_threshold: 10,
			allow_single_deselect: true,
			no_results_text: '<?php echo JText::_("COM_FOXCONTACT_NO_RESULTS_MATCH"); ?>'
		});
});

// Called by the Reset button
function ResetFoxControls()
{
	// Reset each dropdown to its first value
	jQuery('select.fox_select').each(
		function (index, value)
		{
			// Search for the first option, select it and force a refresh
			jQuery(value).find('option:first-child').prop('selected', true).end().trigger('liszt:updated');
		});
}

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
