<?php if ( ! defined('BASEPATH')) exit('Invalid file request');

/**
 * MailChimp Subscribe exception classes.
 *
 * @author		Stephen Lewis <addons@experienceinternet.co.uk>
 * @package		MailChimp Subscribe
 */

class MCS_Exception extends Exception {}
class MCS_Api_exception extends MCS_Exception {}
class MCS_Data_exception extends MCS_Exception {}

/* End of file			: MCS_Exceptions.php */
/* File location		: /system/expressionengine/third_party/mailchimp_subscribe/classes/MCS_Exceptions.php */