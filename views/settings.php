<?php

echo form_open($action_url, '', $hidden_fields);
$this->table->set_template($cp_pad_table_template);

/**
 * API settings.
 */

$this->table->set_heading(array('colspan' => '3', 'data' => lang('api_title')));

$this->table->add_row(array(
	array('data' => lang('api_key', 'api_key'), 'style' => 'width : 25%;'),
	form_input(array(
		'id'	=> 'api_key',
		'class'	=> 'fullfield',
		'name'	=> 'api_key',
		'value'	=> $settings['api_key']
	)),
	array(
		'data' => form_button(array(
			'id'	=> 'get_lists',
			'class'	=> 'submit',
			'content' => lang('get_lists'),
			'name'	=> 'get_lists'
		)),
		'style' => 'width : 30%;'
	)
));

echo $this->table->generate();
$this->table->clear();

?>

<!-- Mailing Lists -->
<div id="mc_lists"></div>

<!-- Loading Message : Is this required? Does EE have something built-in -->
<div id="mc_loading"></div>

<!-- Submit Button -->
<div class="tableFooter"><div class="tableSubmit">
<?=form_submit(array('name' => 'submit', 'value' => lang('save_settings'), 'class' => 'submit')); ?>
</div></div>

</form>

<!-- MailChimp JavaScript -->
<script type="text/javascript">

(function($) {
	
	var ajaxUrl = '<?=str_replace(AMP, "&", BASE); ?>&C=addons_extensions&M=extension_settings&file=mailchimp_subscribe';
	var loading = false;
	
	var languageStrings = {
	<?php
		$output = '';
		foreach ($js_language_strings AS $id => $val)
		{
			$output .= "{$id} : \"{$val}\",\n";
		}
		$output = rtrim($output, ",\n");
		echo $output;
	?>
	};
	
	
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

		/*
		$.get(
			ajaxUrl,
			{ajax_request : 'y', addon_id : addonId, api_key : apiKey, action : 'get_lists'},
			handleGetListsResponse,
			'html'
		);
		*/
		$.get(ajaxUrl, {api_key : apiKey}, handleGetListsResponse, 'html');
	};
	
	
	/**
	 * Handles the getLists AJAX response.
	 *
	 * @param 	string		response		The AJAX response in JSON format.
	 * @return	void
	 */
	function handleGetListsResponse(response) {
		
		$('#mc_lists').html(eval(response));
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
		
		$('#mc_loading').css({
			'top'		: $(window).scrollTop(),
			'left'		: $(window).scrollLeft(),
			'width'		: $(window).width(),
			'height'	: $(window).height()
		});

		$(window).bind('scroll', function() {
			$('#mc_loading').css({'top' : $(window).scrollTop(), 'left' : $(window).scrollLeft()});
		}).bind('resize', function() {
			$('#mc_loading').css({'width' : $(window).width(), 'height' : $(window).height()});
		});

		$('#mc_loading').fadeIn('fast');
	};
	
	
	/**
	 * Stops the loading animation.
	 *
	 * @return 	void
	 */
	function stopLoading() {
		loading = false;
		
		$(window).unbind('scroll').unbind('resize');
		$('#mc_loading').fadeOut('fast');
	};
	
	
	// Start the ball rolling.
	$(document).ready(function() {
		iniAjaxLinks();
	});
	
})(jQuery);

</script>