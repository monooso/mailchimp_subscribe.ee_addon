<?php if ( ! defined('BASEPATH')) exit('Invalid file request');

/**
 * MailChimp mailing list object.
 *
 * @author		Stephen Lewis <addons@experienceinternet.co.uk>
 * @license		??
 * @link 		http://experienceinternet.co.uk/software/mailchimp-subscribe/
 * @package		MailChimp Subscribe
 * @version		2.0.0b1
 */

class MCS_Mailchimp_mailing_list extends MCS_Base {
	
	/**
	 * List ID.
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_id;
	
	/**
	 * Web ID. The list ID used in the web app.
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_web_id;
	
	/**
	 * List name.
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_name;
	
	/**
	 * Date created.
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_date_created;
	
	/**
	 * The number of active users.
	 *
	 * @access	protected
	 * @var		int
	 */
	protected $_member_count;
	
	/**
	 * The number of users who have unsubscribed.
	 *
	 * @access	protected
	 * @var		int
	 */
	protected $_unsubscribe_count;
	
	/**
	 * The number of users cleaned from the list.
	 *
	 * @access	protected
	 * @var		int
	 */
	protected $_cleaned_count;
	
	/**
	 * Multiple email formats, or just HTML?
	 *
	 * @access	protected
	 * @var		bool
	 */
	protected $_email_type_option;
	
	/**
	 * Default 'from' name.
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_default_from_name;
	
	/**
	 * Default 'from' email.
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_default_from_email;
	
	/**
	 * Default subject line.
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_default_subject;
	
	/**
	 * Default language.
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_default_language;
	
	/**
	 * Auto-generated activity score (0-5).
	 *
	 * @access	protected
	 * @var		double
	 */
	protected $_list_rating;
	
	/**
	 * The number of active members since the last campaign was sent.
	 *
	 * @access	protected
	 * @var		int
	 */
	protected $_member_count_since_send;
	
	/**
	 * The number of unsubscribes since the last campaign was sent.
	 *
	 * @access	protected
	 * @var		int
	 */
	protected $_unsubscribe_count_since_send;
	
	/**
	 * The number of 'cleaned' members since the last campaign was sent.
	 *
	 * @access	protected
	 * @var		int
	 */
	protected $_cleaned_count_since_send;	
	
}

/* End of file		: MCS_Mailchimp_mailing_list.php */
/* File location	: /system/expressionengine/third_party/mailchimp_subscribe/classes/MCS_Mailchimp_mailing_list.php */