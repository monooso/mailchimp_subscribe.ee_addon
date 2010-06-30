<?php
	
if ($view_settings->mailing_lists):
	
	$this->table->set_template($cp_pad_table_template);
	
	$this->table->set_heading(
		array('data' => lang('unsubscribe_list'), 'style' => 'width : 25%'),
		lang('unsubscribe_url')
	);
	
	foreach ($view_settings->mailing_lists AS $list):
	
		$this->table->add_row(array($list->name, $list->unsubscribe_url));
	
	endforeach;

	echo $this->table->generate();
	$this->table->clear();
	
else:

	echo lang('unsubscribe_no_mailing_lists');

endif;

/* End of file		: unsubscribe_urls.php */
/* File location	: /system/expressionengine/third_party/mailchimp_subscribe/views/unsubscribe_urls.php */