<?php if ( ! defined('BASEPATH')) exit('Invalid file request');

/**
 * MailChimp Subscribe mailing list object.
 *
 * @author		Stephen Lewis <addons@experienceinternet.co.uk>
 * @license		??
 * @link 		http://experienceinternet.co.uk/software/mailchimp-subscribe/
 * @package		MailChimp Subscribe
 * @version		2.0.0b1
 */

class MCS_Mailing_list extends MCS_Base {
	
	/**
	 * List ID.
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_list_id = '';
	
	/**
	 * List name.
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_list_name = '';
	
	/**
	 * Selected?
	 *
	 * @access	protected
	 * @var		bool
	 */
	protected $_selected = FALSE;
	
	
	/**
	 * Trigger field.
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_trigger_field = '';
	
	/**
	 * Trigger value.
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_trigger_value = '';
	
}

/* End of file		: MCS_Mailing_list.php */
/* File location	: /system/expressionengine/third_party/mailchimp_subscribe/library/MCS_Mailing_list.php */