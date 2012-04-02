(function($) {
	
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
			alert(EE.mailChimp.lang.missingApiKey);
			$('#api_key').focus();
			return;
		}
		
		startLoading();
		
		$.get(EE.mailChimp.globals.ajaxUrl, {api_key : apiKey}, handleGetListsResponse, 'html');
	};
	
	
	/**
	 * Handles the getLists AJAX response.
	 *
	 * @param 	string		response		The AJAX response in JSON format.
	 * @return	void
	 */
	function handleGetListsResponse(response) {
		$('#mailchimp_lists').html(eval(response));
		iniTriggerFields();
		stopLoading();
	};
	
	
	/**
	 * Hijacks the 'Get Mailing Lists' link.
	 *
	 * @return	void
	 */
	function iniAjaxLinks() {
		$('#get_lists')
			.bind('click', function(e) {getLists();})
			.bind('keydown', function(e) {
				if (e.keyCode == '13' || e.keyCode == '32') {
					$(e.target).click();
				}
			});
	};
	
	
	/**
	 * Extracts any JSON objects for later use.
	 *
	 * @return	void
	 */
	function iniJson() {
		EE.mailChimp.memberFields = eval('(' + EE.mailChimp.memberFields + ')');
	}
	
	
	/**
	 * Initialises the 'loading' message.
	 *
	 * @return	void
	 */
	function iniLoadingMessage() {
		$('body').append('<div id="mailchimp_loading"><p></p></div>');
	}
	
	
	/**
	 * Adds a handler to any 'trigger field' drop-downs.
	 *
	 * @return 	void
	 */
	function iniTriggerFields() {
		
		/**
		 * No point doing any of this if we haven't got the member fields object.
		 */

		if ( ! EE.mailChimp.memberFields instanceof Object) {
			return;
		}
		
		$('select[id*=trigger_field]').bind('change', function(e) {
		
			/**
			 * General Note:
			 * jQuery chokes on the field ID, presumably because it contains square brackets.
			 * We go old school to retrieve the element, using document.getElementById as required.
			 */
		
			var triggerFieldId 		= this.value;
			var triggerValueHtml 	= '';
			var triggerValueFieldId = this.id.replace('[trigger_field]', '[trigger_value]');
			var triggerValueField	= document.getElementById(triggerValueFieldId);
		
			/**
			 * If the Member field is of type "select", construct a drop-down of the
			 * available options. Otherwise stick with a text input field.
			 */

			if (EE.mailChimp.memberFields[triggerFieldId] instanceof Object &&
				EE.mailChimp.memberFields[triggerFieldId].type == 'select') {

				var options = EE.mailChimp.memberFields[triggerFieldId].options;
			
				triggerValueHtml += '<select name="' +triggerValueFieldId +'" '
					+'id="' +triggerValueFieldId +'" '
					+'style="display:none;" '
					+'tabindex="' +triggerValueField.tabIndex +'">';

				for (var opt in options) {
					triggerValueHtml += '<option value="' +options[opt] +'">' +options[opt] +'</option>';
				}

				triggerValueHtml += '</select>';

			} else if (document.getElementById(triggerValueFieldId).type != 'text') {
			
				triggerValueHtml += '<input type="text"' 
					+'name="' +triggerValueFieldId +'" '
					+'id="' +triggerValueFieldId +'" '
					+'class="fullfield" '
					+'style="display:none;" '
					+'tabindex="' +triggerValueField.tabIndex +'">';
			}
		
			if (triggerValueHtml) {
				$(triggerValueField).fadeOut('normal').replaceWith(triggerValueHtml);
				$(document.getElementById(triggerValueFieldId)).fadeIn('normal');
			}
		});
		
	};
	
	
	/**
	 * Starts the loading animation.
	 *
	 * @return 	void
	 */
	function startLoading() {
		loading = true;
		
		$('#mailchimp_loading').css({
			'top'		: $(window).scrollTop(),
			'left'		: $(window).scrollLeft(),
			'width'		: $(window).width(),
			'height'	: $(window).height()
		});

		$(window).bind('scroll', function() {
			$('#mailchimp_loading').css({'top' : $(window).scrollTop(), 'left' : $(window).scrollLeft()});
		}).bind('resize', function() {
			$('#mailchimp_loading').css({'width' : $(window).width(), 'height' : $(window).height()});
		});
		
		$('#mailchimp_loading').fadeIn('fast');
	};
	
	
	/**
	 * Stops the loading animation.
	 *
	 * @return 	void
	 */
	function stopLoading() {
		loading = false;
		
		$(window).unbind('scroll').unbind('resize');
		$('#mailchimp_loading').fadeOut('fast');
	};
	
	
	// Start the ball rolling.
	$(document).ready(function() {
		iniLoadingMessage();
		iniJson();
		iniAjaxLinks();
		iniTriggerFields();
	});
	
})(jQuery);