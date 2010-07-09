<?php
	
if ($error_log):
	
	$this->table->set_template($cp_pad_table_template);
	
	$this->table->set_heading(
		array('data' => lang('error_log_id'), 'style' => 'width : 3%'),
		array('data' => lang('error_log_date'), 'style' => 'width : 15%'),
		array('data' => lang('error_log_code'), 'style' => 'width : 15%'),
		lang('error_log_message')
	);
	
	foreach ($error_log AS $error):
	
		$this->table->add_row(array(
			$error['error_log_id'],
			date('Y-m-d H:i:s', intval($error['error_date'])),
			$error['error_code'],
			$error['error_message']
		));
	
	endforeach;

	echo $this->table->generate();
	$this->table->clear();
	
else:

	echo lang('error_log_empty');

endif;

/* End of file		: error_log.php */
/* File location	: /system/expressionengine/third_party/mailchimp_subscribe/views/error_log.php */