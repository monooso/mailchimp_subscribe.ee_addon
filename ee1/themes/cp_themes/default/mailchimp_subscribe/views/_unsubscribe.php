<!-- Error log -->
<div class="clearfix content-block" id="unsubscribe">
<h2><?php echo $lang->line('unsubscribe_title'); ?></h2>
<?php if ($all_mailing_lists): ?>

<table>
	<thead>
		<tr>
			<th><?php echo $lang->line('unsubscribe_list_label'); ?></th>
			<th><?php echo $lang->line('unsubscribe_url_label'); ?></th>
		</tr>
	</thead>
	
	<tbody>
	<?php foreach($all_mailing_lists AS $list): ?>
		<tr>
			<td><?php echo $list['list_name']; ?></td>
			<td><?php echo $list['unsubscribe_url']; ?></td>
		</tr>
	<?php endforeach; ?>
	</tbody>
</table>
	
<?php else: ?>

<p><?php echo $lang->line('unsubscribe_no_mailing_lists'); ?></p>

<?php endif;?>
</div><!-- /#error-log -->