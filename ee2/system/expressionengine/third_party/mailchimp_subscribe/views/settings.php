<div id="sjl">
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
		'value'	=> $view_settings->api_key
	)),
	array(
		'data' => form_button(array(
			'id'		=> 'get_lists',
			'class'		=> 'submit',
			'content'	=> lang('get_lists'),
			'name'		=> 'get_lists'
		)),
		'style' => 'width : 25%;'
	)
));

echo $this->table->generate();
$this->table->clear();

?>

<!-- Mailing Lists -->
<div id="mailchimp_lists">
<?php
	if ($view_settings->mailing_lists):
	
		$this->load->view('_mailing_lists');
	
	endif;
?>
</div>

<!-- Submit Button -->
<div class="tableFooter"><div class="tableSubmit">
<?=form_submit(array('name' => 'submit', 'value' => lang('save_settings'), 'class' => 'submit')); ?>
</div></div>

</form>
</div><!-- /#sjl -->