<?php

/**
 * Mailing list settings.
 */

$this->table->set_template($cp_pad_table_template);
$this->table->set_heading(
	array('data' => '&nbsp;', 'style' => 'width : 5%'),
	array('data' => lang('list_name'), 'style' => 'width : 20%'),
	lang('trigger_field'),
	lang('trigger_value'),
	lang('interest_groups'),
	array('data' => lang('merge_variables'), 'style' => 'width : 30%;')
);

foreach ($mailing_lists AS $list)
{
	$this->table->add_row(array(
		form_checkbox(array(
			'checked'	=> $list->selected,
			'id' 		=> "mailing_lists[{$list->list_id}][checked]",
			'name' 		=> "mailing_lists[{$list->list_id}][checked]",
			'value' 	=> $list->list_id
		)),
		$list->list_name,
		form_dropdown(
			"mailing_lists[{$list->list_id}][trigger_field]",
			$member_field_options,
			$list->trigger_field,
			"id='mailing_lists[{$list->list_id}][trigger_field]'"
		),
		form_input(array(
			'id'	=> "mailing_lists[{$list->list_id}][trigger_value]",
			'class'	=> 'fullfield',
			'name'	=> "mailing_lists[{$list->list_id}][trigger_value]",
			'value'	=> $list->trigger_value
		)),
		'interest_groups',
		'merge_variables'
	));
}

echo $this->table->generate();

/* End of file		: _mailing_lists.php */
/* File location	: /system/expressionengine/third_party/mailchimp_subscribe/views/_mailing_lists.php */