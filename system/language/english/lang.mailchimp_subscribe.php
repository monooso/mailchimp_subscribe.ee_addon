<?php

/**
 * @package		MailChimp Subscribe
 * @version 	2.0.0
 * @author 		Stephen Lewis <stephen@experienceinternet.co.uk>
 * @copyright 	Copyright (c) 2008-2010, Stephen Lewis
 * @link 		http://experienceinternet.co.uk/software/mailchimp-subscribe/
 */

$L = array(
	
/* The basics */
'extension_name'		=> 'MailChimp Subscribe',
'extension_description'	=> 'Effortlessly subscribe members of your ExpressionEngine site to one or more MailChimp mailing lists.',

/* Navigation */
'extension_settings'	=> 'Extension Settings',
'utilities'				=> 'Utilities',
'extensions_manager'	=> 'Extensions Manager',
'disable_extension'		=> 'Disable?',

'docs_link_title'		=> 'Comprehensive MailChimp Subscribe documentation. Read it.',
'docs_link'				=> 'Docs',

'unsubscribe_link_title' => 'Mailing list unsubscribe URLs',
'unsubscribe_link'		=> 'Unsubscribe URLs',

'error_log_link_title'	=> 'A log of API errors',
'error_log_link'		=> 'Error Log',

'settings_link_title'	=> 'MailChimp Subscribe settings',
'settings_link'			=> 'Settings',

'support_link_title'	=> 'Get help. Seriously.',
'support_link'			=> 'Support',
                        
/* API key */
'api_key_title'		=> 'API Key',
'api_key_info'		=> '<p>If you just want to add a MailChimp signup form to your site, there&rsquo;s no need for all this Extension shenanigans. Read <a href="http://server.iad.liveperson.net/hc/s-31286565/cmd/kbresource/kb-3478445807619622561/view_question!PAGETYPE?sq=embed%2bcode&sf=101113&st=300709&documentid=210891&action=view" title="How to create a MailChimp signup form">this knowledgebase article</a>, and you&rsquo;ll be up and running in no time.</p>',
'api_key_label'		=> 'Enter your MailChimp API Key',
'api_key_hint'		=> 'Not sure what your API key is? <a href="http://admin.mailchimp.com/account/api-key-popup" title="Get your MailChimp API key">Get it here</a>',
'get_lists'			=> 'Get Mailing Lists',

/* Mailing lists */
'list_title'	=> 'Mailing Lists',
'list_info'		=> '<p>Select the mailing list(s) that new members may join. You may set a distinct "trigger" field and value for each mailing list.</p>',

'list_name_label'	=> 'Mailing List Name',

'list_trigger_field_label'	=> '"Trigger" Field',
'list_trigger_value_label'	=> '"Trigger" Value',
'list_trigger_dd_hint'		=> '[Select a member field]',

'no_lists'					=> 'There are no mailing lists associated with this account.',

'list_merge_vars_label'		=> 'Merge Variables',
'list_merge_vars_dd_hint'	=> '[Select a member field]',

'list_interest_groups_label'		=> 'Interest Group',
'list_interest_groups_dd_hint'	=> '[Select a member field]',

/* Unsubscribe */
'unsubscribe_title'		=> 'Unsubscribe URLs',
'unsubscribe_hint'		=> 'Need to include an unsubscribe link on your site? Here are the unsubscribe URLs for your mailing lists.',
'unsubscribe_list_label' => 'Mailing List Name',
'unsubscribe_url_label' => 'Unsubscribe URL',

'unsubscribe_no_mailing_lists' => 'There are no mailing lists saved in the database. Once you&rsquo;ve retrieve your mailing lists, <em>and saved the extension settings</em>, your unsubscribe URLs will appear here.',

'member_username'       => 'Username',
'member_screen_name'    => 'Screen Name',
'member_url'            => 'URL',
'member_location'       => 'Location',

/* Save settings */
'save_settings'			=> 'Save Settings',

/* Error messages */
'missing_api_key'		=> 'API key not supplied.',
'js_missing_api_key'	=> 'Please enter a valid API key.',

'unknown_error'     	=> 'An unknown problem occurred when contacting the MailChimp API.',
'error_preamble'    	=> 'The MailChimp API reported the following error: ',

/* Error log */
'api_error_code_label'	=> 'API Error Code',
'api_error_message_label' => 'API Error Message',
'api_method_label'		=> 'API Method',
'date_label'			=> 'Date',
'error_log_title'		=> 'Error Log',
'list_id_label'			=> 'List ID',
'member_id_label'		=> 'Member ID',
'no_errors'				=> 'No errors have been recorded.',

/* End */
'' => ''

);

/* End of file 		: lang.mailchimp_subscribe.php */
/* File location	: /system/language/english/lang.mailchimp_subscribe.php */