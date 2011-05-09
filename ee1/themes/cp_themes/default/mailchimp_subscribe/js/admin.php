<script type="text/javascript">

/**
 * Effortlessly subscribe members of your ExpressionEngine site to one or more MailChimp mailing lists.
 *
 * @package		MailChimp Subscribe
 * @author 		Stephen Lewis <stephen@experienceinternet.co.uk>
 * @copyright 	Copyright (c) 2008-2010, Stephen Lewis
 * @link 		http://experienceinternet.co.uk/software/mailchimp-subscribe/
 */

(function($) {
	
	var ajaxUrl = '<?php echo $js_vars['ajaxUrl']; ?>';
	var addonId = '<?php echo $js_vars['addonId']; ?>';
	
	var languageStrings = {
	<?php
		$language_strings = '';
		foreach ($js_vars['languageStrings'] AS $id => $val)
		{
			$language_strings .= $id .' : "' .$val ."\",\n";
		}
		echo rtrim($language_strings, ",\n");
	?>
	};
	
	var memberFields = {
	<?php
		$member_fields = '';
		foreach ($js_vars['memberFields'] AS $id => $val)
		{
			$field_options = array();
			foreach ($val['options'] AS $option)
			{
				$field_options[] = '"' .addslashes($option) .'"';
			}
			
			$member_fields .= $id .' : {'
				.'id : "' .$id .'",'
				.'label : "' .addslashes($val['label']) .'",'
				.'type : "' .addslashes($val['type']) .'",'
				.'options : [' .implode(', ', $field_options) .']'
				."},\n";
		}
		echo rtrim($member_fields, ",\n");
	?>
	};
	
	var loading = false;
	
	
	/**
	 * Retrieves the 'mailing lists' HTML via AJAX.
	 *
	 * @return	void
	 */
	function getLists() {
		if (loading) return;
		
		apiKey = $('#api_key').val();
		
		if ( ! apiKey) {
			alert(languageStrings.missingApiKey);
			$('#api_key').focus();
			return;
		}
		
		startLoading();

		$.get(
			ajaxUrl,
			{ajax_request : 'y', addon_id : addonId, api_key : apiKey, action : 'get_lists'},
			handleGetListsResponse,
			'html'
		);
	};
	
	
	/**
	 * Handles the getLists AJAX response.
	 *
	 * @param 	string		html		The HTML to insert into the page.
	 * @return	void
	 */
	function handleGetListsResponse(html) {
		$('#lists').html(html);
		
		iniTriggerField();
		stopLoading();
	};
	
	
	/**
	 * Hijacks the 'Get Mailing Lists' link.
	 *
	 * @return	void
	 */
	function iniAjaxLinks() {
		$('#sjl #get-lists')
			.bind('click', function(e) {getLists();})
			.bind('keydown', function(e) {
				if (e.keyCode == '13' || e.keyCode == '32') {
					$(e.target).click();
				}
			});
	};
	
	
	/**
	 * Initialises the top-level navigation.
	 *
	 * @return 	void
	 */
	function iniNav() {
		$('#masthead li a[href^=#]').bind('click', function(e) {
			
			$link = $(e.target);
			if ( ! $link.hasClass('active')) {
				$('#sjl .content-block:not(' +$link.attr('href') +')').fadeOut('fast');
				$('#sjl ' +$link.attr('href')).fadeIn('normal');
				
				$link
					.parent('li').addClass('active')
					.siblings('li').removeClass('active');
			}
			return false;
		});
	};
	
	
	/**
	 * Adds a handler to any 'trigger field' drop-downs.
	 *
	 * @return 	void
	 */
	function iniTriggerField() {
		$('select[id*=trigger_field]').bind('change', function(e) {
			
			/**
			 * General Note:
			 * jQuery chokes on the field ID, presumably because it contains square brackets.
			 * We go old school to retrieve the element, using document.getElementById as required.
			 */
			
			var triggerFieldId 		= this.value;
			var triggerValueHtml 	= '';
			var triggerValueFieldId = this.id.replace('[trigger_field]', '[trigger_value]');
			
			/**
			 * If the Member field is of type "select", construct a drop-down of the
			 * available options. Otherwise stick with a text input field.
			 */

			if (memberFields instanceof Object &&
				memberFields[triggerFieldId] instanceof Object &&
				memberFields[triggerFieldId].type == 'select') {

				var options = memberFields[triggerFieldId].options;
				triggerValueHtml += '<select name="' + triggerValueFieldId +'" id="' +triggerValueFieldId +'" style="display:none;">';

				for (var opt in options) {
					triggerValueHtml += '<option value="' +options[opt] +'">' +options[opt] +'</option>';
				}

				triggerValueHtml += '</select>';

			} else if (document.getElementById(triggerValueFieldId).type != 'text') {
				triggerValueHtml += '<input type="text"' 
					+'name="' +triggerValueFieldId +'"'
					+'id="' +triggerValueFieldId +'"'
					+'style="display:none;">';
			}
			
			if (triggerValueHtml) {
				$(document.getElementById(triggerValueFieldId)).fadeOut('normal').replaceWith(triggerValueHtml);
				$(document.getElementById(triggerValueFieldId)).fadeIn('normal');
			}
		});
	};
	
	
	/**
	 * Adds a class to the UI wrapper, to indicate that JS is enabled and working.
	 *
	 * @return	void
	 */
	function setJSFlag() {
		$('#sjl').addClass('enhanced');
	};
	
	
	/**
	 * Starts the loading animation.
	 *
	 * @return 	void
	 */
	function startLoading() {
		loading = true;
		
		$('#sjl #loading').css({
			'top'		: $(window).scrollTop(),
			'left'		: $(window).scrollLeft(),
			'width'		: $(window).width(),
			'height'	: $(window).height()
		});

		$(window).bind('scroll', function() {
			$('#sjl #loading').css({'top' : $(window).scrollTop(), 'left' : $(window).scrollLeft()});
		}).bind('resize', function() {
			$('#sjl #loading').css({'width' : $(window).width(), 'height' : $(window).height()});
		});

		$('#sjl #loading').fadeIn('fast');
	};
	
	
	/**
	 * Stops the loading animation.
	 *
	 * @return 	void
	 */
	function stopLoading() {
		loading = false;
		
		$(window).unbind('scroll').unbind('resize');
		$('#sjl #loading').fadeOut('fast');
	};
	
	
	// Start the ball rolling.
	$(document).ready(function() {
		setJSFlag();
		iniNav();
		iniAjaxLinks();
		iniTriggerField();
	});
	
})(jQuery);

</script>