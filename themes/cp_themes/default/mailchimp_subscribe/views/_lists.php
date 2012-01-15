<h2><?php echo $lang->line('list_title'); ?></h2>

<?php

$tabindex = 50;
if ($all_mailing_lists): ?>

<div class="info"><?php echo $lang->line('list_info'); ?></div>
<table cellpadding="0" cellspacing="0">
	<thead>
		<th style="width : 5%;">&nbsp;</th>
		<th style="width : 20%;"><?php echo $lang->line('list_name_label'); ?></th>
		<th><?php echo $lang->line('list_trigger_field_label'); ?></th>
		<th><?php echo $lang->line('list_trigger_value_label'); ?></th>
		<th><?php echo $lang->line('list_interest_groups_label'); ?></th>
		<th style="width : 25%;"><?php echo $lang->line('list_merge_vars_label'); ?></th>
	</thead>
	
	<tbody>
		<?php
		
		$count = 0;
		foreach($all_mailing_lists AS $mailing_list):
			$count++;
			$class = $count % 2 ? 'odd' : 'even';
		
			// Start with some defaults.
			$member_field_type 		= 'text';
			$member_field_options	= array();
			$trigger_field			= '';
			$trigger_value			= '';
			
			$interest_groups_field	= '';
			
			// Is this mailing list 'active'.
			$active_list = array_key_exists($mailing_list['list_id'], $settings['mailing_lists'])
				? $settings['mailing_lists'][$mailing_list['list_id']]
				: array();
		
			if ($active_list)
			{
				$trigger_field 			= $active_list['trigger_field'];
				$trigger_value 			= $active_list['trigger_value'];
				$interest_groups_field	= $active_list['interest_groups'];
				
				$member_field_type	= array_key_exists($trigger_field, $member_fields)
					? $member_fields[$trigger_field]['type']
					: '';
				
				if ($member_field_type == 'select')
				{
					$member_field_options = $member_fields[$trigger_field]['options'];
				}
			}
		?>
		<tr class="<?php echo $class; ?>" valign="top">
			<td>
				<input id="mailing_lists[<?php echo $mailing_list['list_id']; ?>][checked]" 
					name="mailing_lists[<?php echo $mailing_list['list_id']; ?>][checked]"
					tabindex="<?php echo $tabindex += 10; ?>"
					type="checkbox"
					<?php echo ($active_list ? 'checked="checked"' : ''); ?>
					value="<?php echo $mailing_list['list_id']; ?>" />
			</td>
			<td><label for="mailing_lists[<?php echo $mailing_list['list_id']; ?>][checked]"><?php echo $mailing_list['list_name']; ?></label></td>
			<td>
				<select id="mailing_lists[<?php echo $mailing_list['list_id']; ?>][trigger_field]"
					name="mailing_lists[<?php echo $mailing_list['list_id']; ?>][trigger_field]"
					tabindex="<?php echo $tabindex += 10; ?>">
					<option value=""><?php echo $lang->line('list_trigger_dd_hint')?></option>
					<?php
					foreach ($member_fields AS $member_field)
					{
						echo '<option value="' .$member_field['id'] .'"';
						echo $member_field['id'] == $trigger_field ? 'selected="selected"' : '';
						echo '>' .$member_field['label'] .'</option>';
					}
					?>
				</select>
			</td>
			<td>
				<?php if ($member_field_type == 'select'): ?>
				
				<select id="mailing_lists[<?php echo $mailing_list['list_id']; ?>][trigger_value]"
					name="mailing_lists[<?php echo $mailing_list['list_id']; ?>][trigger_value]"
					tabindex="<?php echo $tabindex += 10; ?>">
					<?php
					foreach ($member_field_options AS $member_field_option)
					{
						echo '<option value="' .$member_field_option .'"';
						echo $member_field_option == $trigger_value ? 'selected="selected"' : '';
						echo '>' .$member_field_option .'</option>';
					}
					?>
				</select>
				
				<?php else: ?>
				
				<input id="mailing_lists[<?php echo $mailing_list['list_id']; ?>][trigger_value]"
					name="mailing_lists[<?php echo $mailing_list['list_id']; ?>][trigger_value]"
					tabindex="<?php echo $tabindex += 10; ?>"
					type="text"
					value="<?php echo $trigger_value; ?>" />
				
				<?php endif; ?>
			</td>
			
			<td>
				<?php if ($mailing_list['interest_groups']): ?>
				<label>
					<?php echo $mailing_list['interest_groups']; ?>
					<select id="mailing_lists[<?php echo $mailing_list['list_id']; ?>][interest_groups]"
						name="mailing_lists[<?php echo $mailing_list['list_id']; ?>][interest_groups]"
						tabindex="<?php echo $tabindex += 10; ?>">
						<option value=""><?php echo $lang->line('list_interest_groups_dd_hint')?></option>
						<?php
						foreach ($member_fields AS $member_field)
						{
							echo '<option value="' .$member_field['id'] .'"';
							echo $member_field['id'] == $interest_groups_field ? 'selected="selected"' : '';
							echo '>' .$member_field['label'] .'</option>';
						}
						?>
					</select>
				</label>
				<?php endif; ?>
			</td>
			
			<td class="nested">
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
								<label for="mailing_lists[<?php echo $mailing_list['list_id']; ?>][merge_vars][<?php echo $merge_var['tag']; ?>]"><?php echo $merge_var['name']; ?></label>
							</td>
							<td>
								<select
									id="mailing_lists[<?php echo $mailing_list['list_id']; ?>][merge_vars][<?php echo $merge_var['tag']; ?>]"
									name="mailing_lists[<?php echo $mailing_list['list_id']; ?>][merge_vars][<?php echo $merge_var['tag']; ?>]"
									tabindex="<?php echo $tabindex += 10; ?>">
								
									<option value=""><?php echo $lang->line('list_merge_vars_dd_hint')?></option>
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
			</td>
		</tr>
		<?php endforeach; ?>
	</tbody>
</table>

<?php
else:

	echo '<div class="info">';
	
	if ($errors)
	{
		$message = '<p>';
		foreach($errors AS $error)
		{
			$message .= $error['error_message'] .'<br />';
		}
		$message .= '</p>';
	}
	else
	{
		$message = '<p>' .$lang->line('no_lists') .'</p>';
	}
	
	echo $message;	
	echo '</div>';

endif;
?>