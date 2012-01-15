<div id="sjl">
	<div id="masthead" class="clearfix">
		<h1>
			<?=$lang->line('extension_name') ." <em>v{$version}</em>"; ?>
			<img alt="Monkey!" src="<?=$themes_url .'img/chimp.png' ;?>" title="Monkey!" />
		</h1>
		<ul>
			<li class="active"><a href="#settings" title="<?=$lang->line('settings_link_title'); ?>"><?=$lang->line('settings_link'); ?></a></li>
			<li><a href="#unsubscribe" title="<?=$lang->line('unsubscribe_link_title'); ?>"><?=$lang->line('unsubscribe_link'); ?></a></li>
			<li><a href="#error-log" title="<?=$lang->line('error_log_link_title'); ?>"><?=$lang->line('error_log_link'); ?></a></li>
			<li><a href="<?=$docs_url; ?>" title="<?=$lang->line('docs_link_title'); ?>" target="_blank"><?=$lang->line('docs_link'); ?></a></li>
			<li><a href="<?=$support_url; ?>" title="<?=$lang->line('support_link_title'); ?>" target="_blank"><?=$lang->line('support_link'); ?></a></li>
		</ul>
	</div><!-- #masthead -->

	<!-- Settings -->
	<div class="content-block" id="settings">
		<?=$form_open; ?>
		
			<!-- API Key -->
			<fieldset>
				<h2><?=$lang->line('api_key_title'); ?></h2>
				<div class="info"><?=$lang->line('api_key_info'); ?></div>
				
				<table cellpadding="0" cellspacing="0">
					<tbody>
						<tr class="odd">
							<th style="width : 25%;">
								<label for="api_key"><?=$lang->line('api_key_label') .'<span>' .$lang->line('api_key_hint'). '</span>'; ?></label>
							</th>
							<td style="vertical-align : middle;"><input class="text" id="api_key" name="api_key" tabindex="10" type="text" value="<?=$settings['api_key']; ?>" /></td>
							<td style="vertical-align : middle; width : 25%;"><span class="ajax button" id="get-lists" tabindex="20"><?=$lang->line('get_lists'); ?></span></td>
						</tr>
					</tbody>
				</table>
			</fieldset>
			
			<!-- Lists -->
			<fieldset id="lists">
				<?php if ($all_mailing_lists) include($themes_path .'views/_lists.php'); ?>
			</fieldset>

			<!-- Save -->
			<fieldset class="submit">
				<input type="submit" value="<?=$lang->line('save_settings'); ?>" />
			</fieldset>
		</form>
	</div><!-- /#settings -->
	
	<?php include($themes_path .'views/_unsubscribe.php'); ?>
	<?php include($themes_path .'views/_error_log.php'); ?>
	
	<div id="loading"><p><img src="<?=$themes_url .'img/loading.gif' ;?>" /></p></div>
	<?php include($themes_path .'js/admin.php'); ?>
</div><!-- /#sjl -->