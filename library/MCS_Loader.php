<?php if ( ! defined('BASEPATH')) exit('Invalid file request');

/**
 * Loads all required classes.
 *
 * @author		Stephen Lewis <addons@experienceinternet.co.uk>
 * @license		??
 * @link 		http://experienceinternet.co.uk/software/mailchimp-subscribe/
 * @package		MailChimp Subscribe
 * @version		2.0.0b1
 */

require_once dirname(__FILE__) .'/MCS_Base' .EXT;
require_once dirname(__FILE__) .'/MCS_Exceptions' .EXT;
require_once dirname(__FILE__) .'/MCS_Mailing_list' .EXT;
require_once dirname(__FILE__) .'/MCAPI.class' .EXT;