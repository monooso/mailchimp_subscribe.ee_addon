<?php

/**
 * Mailing list settings.
 */

$tabindex = 50;

$this->table->set_template($cp_pad_table_template);
$this->table->set_heading(
	array('data' => '&nbsp;', 'style' => 'width : 4%'),
	array('data' => lang('list_name'), 'style' => 'width : 21%'),
	lang('trigger_field'),
	lang('trigger_value'),
	lang('interest_groups'),
	array('data' => lang('merge_variables'), 'style' => 'width : 30%;')
);

if ($view_settings->mailing_lists):
	
	foreach ($view_settings->mailing_lists AS $list):
		
		// Text or select.
		$member_field_type = array_key_exists($list->trigger_field, $member_fields)
			? $member_fields[$list->trigger_field]['type']
			: 'text';
			
		$member_field_options = $member_field_type == 'select'
			? $member_field_options = $member_fields[$list->trigger_field]['options']
			: array();
		
		
		// Create the table rows.
		$row = array(
			form_checkbox(array(
				'checked'	=> ($list->active == 'y'),
				'id' 		=> "mailing_lists[{$list->id}][checked]",
				'name' 		=> "mailing_lists[{$list->id}][checked]",
				'tabindex'	=> $tabindex += 10,
				'value' 	=> $list->id
			)),
		
			form_label($list->name, "mailing_lists[{$list->id}][checked]"),
		
			form_dropdown(
				"mailing_lists[{$list->id}][trigger_field]",
				$cleaned_member_fields,
				$list->trigger_field,
				"id='mailing_lists[{$list->id}][trigger_field]' tabindex='" .($tabindex += 10) ."'"
			)
		);
	
		// Trigger value can be a text field or a drop-down.
		if ($member_field_type == 'select'):
		
			$row[] = form_dropdown(
				"mailing_lists[{$list->id}][trigger_value]",
				$member_field_options,
				$list->trigger_value,
				"id='mailing_lists[{$list->id}][trigger_value]' tabindex='" .($tabindex += 10) ."'"
			);
		
		else:
	
			$row[] = form_input(array(
				'id'	=> "mailing_lists[{$list->id}][trigger_value]",
				'class'	=> 'fullfield',
				'name'	=> "mailing_lists[{$list->id}][trigger_value]",
				'tabindex'	=> $tabindex += 10,
				'value'	=> $list->trigger_value
			));
	
		endif;
	
		// Interest groups.
		if ($list->interest_groups):
		
			$cell = '';
			
			foreach ($list->interest_groups AS $group):
			
				$cell .= '<label style="display : block; margin-bottom : 10px;">'
					.$group->name
					.form_dropdown(
						"mailing_lists[{$list->id}][interest_groups][{$group->id}]",
						$cleaned_member_fields,
						$group->member_field_id,
						"id='mailing_lists[{$list->id}][interest_groups][{$group->id}]' tabindex='" .($tabindex += 10) ."'"
					)
					.'</label>';
			
			endforeach;
				
		else:
			
			$cell = lang('no_interest_groups');
	
		endif;
		
		$row[] = $cell;
	
		// Merge variables.
		if ($list->merge_variables):
		
			$cell = '';
			
			foreach ($list->merge_variables AS $var):
				
				// Can't use email as a merge variable. Honestly, I forget why right now.
				if (strtolower($var->tag) == 'email'):
					continue;
				endif;
				
				$cell .= '<label style="display : block; margin-bottom : 10px;">'
					.$var->name
					.form_dropdown(
						"mailing_lists[{$list->id}][merge_variables][{$var->tag}]",
						$cleaned_member_fields,
						$var->member_field_id,
						"id='mailing_lists[{$list->id}][merge_variables][{$var->id}]' tabindex='" .($tabindex += 10) ."'"
					)
					.'</label>';
			
			endforeach;
				
		else:
			
			$cell = lang('no_merge_variables');
	
		endif;
		
		$row[] = $cell;
	
		// Add the row to the table.
		$this->table->add_row($row);
	
	endforeach;
	
else:

	$this->table->add_row(array('colspan' => '6', 'data' => lang('no_mailing_lists')));

endif;

// Write out the table.
echo $this->table->generate();

// Tidy up.
$this->table->clear();

/* End of file		: _mailing_lists.php */
/* File location	: /system/expressionengine/third_party/mailchimp_subscribe/views/_mailing_lists.php */