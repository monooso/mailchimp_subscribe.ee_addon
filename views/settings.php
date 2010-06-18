<div id="sjl">
<div class="clearfix content_block" id="settings">
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
<div id="mailchimp_lists"></div>

<!-- Submit Button -->
<div class="tableFooter"><div class="tableSubmit">
<?=form_submit(array('name' => 'submit', 'value' => lang('save_settings'), 'class' => 'submit')); ?>
</div></div>

</form>
</div><!-- /#settings -->


<!-- Unsubscribe URLs -->
<div class="clearfix content_block" id="unsubscribe">
<h2><?=lang('unsubscribe_title'); ?></h2>
<?php
	
if ($mailing_lists):
	
	$this->table->set_template($cp_pad_table_template);
	
	$this->table->set_heading(
		array('data' => lang('unsusbscribe_list'), 'style' => 'width : 25%'),
		lang('unsusbscribe_url')
	);
	
	foreach ($mailing_lists AS $list):
	
		$this->table->add_row(array($list['list_name'], $list['unsubscribe_url']));
	
	endforeach;

	echo $this->table->generate();
	$this->table->clear();
	
else:

	echo lang('unsubscribe_no_mailing_lists');

endif;

?>
</div><!-- /#unsubscribe -->


<!-- Error Log -->
<div class="clearfix content_block" id="errors">
<h2><?=lang('error_log_title'); ?></h2>
<?php
	
if ($error_log):
	
	$this->table->set_template($cp_pad_table_template);
	
	$this->table->set_heading(
		array('data' => lang('error_log_date'), 'style' => 'width : 15%'),
		array('data' => lang('error_log_code'), 'style' => 'width : 15%'),
		lang('error_log_message')
	);
	
	foreach ($mailing_lists AS $list):
	
		$this->table->add_row(array(
			date('Y-m-d', intval($error['error_date'])),
			$error['error_code'],
			$error['error_message']
		));
	
	endforeach;

	echo $this->table->generate();
	$this->table->clear();
	
else:

	echo lang('error_log_empty');

endif;

?>
</div><!-- /#error-log -->

<!-- Loading Message : Is this required? Does EE have something built-in -->
<div id="mailchimp_loading"></div>

</div><!-- /#sjl -->