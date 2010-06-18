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

		/*
		$.get(
			ajaxUrl,
			{ajax_request : 'y', addon_id : addonId, api_key : apiKey, action : 'get_lists'},
			handleGetListsResponse,
			'html'
		);
		*/
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
		// iniTriggerField();
		
		// $.ee_notice('FTW!', {'type' : 'success'}); // Unlikely to use this, but here so I remember about its existence.
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
		iniAjaxLinks();
	});
	
})(jQuery);