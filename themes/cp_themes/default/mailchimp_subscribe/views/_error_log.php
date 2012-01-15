<!-- Error log -->
<div class="clearfix content-block" id="error-log">
<h2><?php echo $lang->line('error_log_title'); ?></h2>
<?php if ($db_error_log->num_rows > 0): ?>

<table>
	<thead>
		<tr>
			<th><?php echo $lang->line('date_label'); ?></th>
			<th><?php echo $lang->line('api_method_label'); ?></th>
			<th><?php echo $lang->line('member_id_label'); ?></th>
			<th><?php echo $lang->line('list_id_label'); ?></th>
			<th><?php echo $lang->line('api_error_code_label'); ?></th>
			<th><?php echo $lang->line('api_error_message_label'); ?></th>
		</tr>
	</thead>
	
	<tbody>
	<?php foreach($db_error_log->result AS $db_error): ?>
		<tr>
			<td><?php echo date('Y-m-d', intval($db_error['error_date'])); ?></td>
			<td><?php echo $db_error['api_method']; ?></td>
			<td><?php echo $db_error['member_id']; ?></td>
			<td><?php echo $db_error['list_id']; ?></td>
			<td><?php echo $db_error['api_error_code']; ?></td>
			<td><?php echo $db_error['api_error_message']; ?></td>
		</tr>
	<?php endforeach; ?>
	</tbody>
</table>
	
<?php else: ?>

<p><?php echo $lang->line('no_errors'); ?></p>

<?php endif;?>
</div><!-- /#error-log -->