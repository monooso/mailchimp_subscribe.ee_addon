<?php

/**
 * Mailing list settings.
 */

$tabindex = 50;

$this->table->set_template($cp_pad_table_template);
$this->table->set_heading(
	array('data' => '&nbsp;', 'style' => 'width : 5%'),
	array('data' => lang('list_name'), 'style' => 'width : 20%'),
	lang('trigger_field'),
	lang('trigger_value'),
	lang('interest_groups'),
	array('data' => lang('merge_variables'), 'style' => 'width : 30%;')
);

if ($mailing_lists):

	foreach ($mailing_lists AS $list):

		// Start with some defaults.
		$member_field_type 		= 'text';
		$member_field_options	= array();
		$trigger_field			= '';
		$trigger_value			= '';
		$interest_groups_field	= '';
	
		// Does this mailing list exist in the settings?
		$active_list = array_key_exists($list['list_id'], $settings['mailing_lists'])
			? $settings['mailing_lists'][$list['list_id']]
			: FALSE;
		
		if ($active_list):
	
			$trigger_field			= $active_list['trigger_field'];
			$trigger_value			= $active_list['trigger_value'];
			$interest_groups_field	= $active_list['interest_groups'];
		
			$member_field_type = array_key_exists($trigger_field, $member_fields)
				? $member_fields[$trigger_field]['type']
				: 'text';
			
			if ($member_field_type == 'select')
			{
				$member_field_options = $member_fields[$trigger_field]['options'];
			}
	
		endif;
	
		// Create the table rows.
		$row = array(
			form_checkbox(array(
				'checked'	=> (bool)$active_list,
				'id' 		=> "mailing_lists[{$list['list_id']}][checked]",
				'name' 		=> "mailing_lists[{$list['list_id']}][checked]",
				'tabindex'	=> $tabindex += 10,
				'value' 	=> $list['list_id']
			)),
		
			form_label($list['list_name'], "mailing_lists[{$list['list_id']}][checked]"),
		
			form_dropdown(
				"mailing_lists[{$list['list_id']}][trigger_field]",
				$member_fields,
				$trigger_field,
				"id='mailing_lists[{$list['list_id']}][trigger_field]' tabindex='" .($tabindex += 10) ."'"
			)
		);
	
		// Trigger value can be a text field or a drop-down.
		if ($member_field_type == 'select'):
		
			$row[] = form_dropdown(
				"mailing_lists[{$list['list_id']}][trigger_value]",
				$member_field_options,
				$trigger_value,
				"id='mailing_lists[{$list['list_id']}][trigger_value]' tabindex='" .($tabindex += 10) ."'"
			);
		
		else:
	
			$row[] = form_input(array(
				'id'	=> "mailing_lists[{$list['list_id']}][trigger_value]",
				'class'	=> 'fullfield',
				'name'	=> "mailing_lists[{$list['list_id']}][trigger_value]",
				'tabindex'	=> $tabindex += 10,
				'value'	=> $trigger_value
			));
	
		endif;
	
		// Interest groups.
		if ($list['interest_groups']):
		
			$row[] = '<label>'
				.$list['interest_groups']
				.form_dropdown(
					"mailing_lists[{$list['list_id']}][interest_groups]",
					$member_fields,
					$interest_groups_field,
					"id='mailing_lists[{$list['list_id']}][interest_groups]' tabindex='" .($tabindex += 10) ."'"
				)
				.'</label>';
	
		endif;
	
		// Merge variables.
		$row[] = 'Merge variables...';
	
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
	
/*
<table cellpadding="0" cellspacing="0">
	<tbody>
		<?php
		
		if ($mailing_list['merge_vars']):
			foreach ($mailing_list['merge_vars'] AS $merge_var):
				if (strtolower($merge_var['tag']) == 'email')
				{
					continue;
				}
			
				$active_merge_var_field = array_key_exists('merge_vars', $active_list)
					&& array_key_exists($merge_var['tag'], $active_list['merge_vars'])
					? $active_list['merge_vars'][$merge_var['tag']]['member_field_id']
					: FALSE;
		?>
		<tr>
			<td style="width : 50%;">
				<label for="mailing_lists[<?=$mailing_list['list_id']; ?>][merge_vars][<?=$merge_var['tag']; ?>]"><?=$merge_var['name']; ?></label>
			</td>
			<td>
				<select
					id="mailing_lists[<?=$mailing_list['list_id']; ?>][merge_vars][<?=$merge_var['tag']; ?>]"
					name="mailing_lists[<?=$mailing_list['list_id']; ?>][merge_vars][<?=$merge_var['tag']; ?>]"
					tabindex="<?=$tabindex += 10; ?>">
				
					<option value=""><?=$lang->line('list_merge_vars_dd_hint')?></option>
					<?php
					foreach ($member_fields AS $member_field)
					{
						echo '<option value="' .$member_field['id'] .'"';
						echo $member_field['id'] == $active_merge_var_field ? 'selected="selected"' : '';
						echo '>' .$member_field['label'] .'</option>';
					}
					?>
				</select>
			</td>
		</tr>
		<?php
			endforeach;
		else:
		?>
		<tr><td>&nbsp;</td></tr>
		<?php endif; ?>
	</tbody>
</table>
*/


/* End of file		: _mailing_lists.php */
/* File location	: /system/expressionengine/third_party/mailchimp_subscribe/views/_mailing_lists.php */