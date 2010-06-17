<?php if ( ! defined('BASEPATH')) exit('Invalid file request');

/**
 * MailChimp Subscribe settings.
 *
 * @author		Stephen Lewis <addons@experienceinternet.co.uk>
 * @license		??
 * @link 		http://experienceinternet.co.uk/software/mailchimp-subscribe/
 * @package		MailChimp Subscribe
 * @version		2.0.0b1
 */

class MCS_Settings extends MCS_Base {
	
	/**
	 * API key.
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_api_key = '';
	
	/**
	 * 'Active' mailing lists.
	 *
	 * @access	protected
	 * @var		array
	 */
	protected $_mailing_lists = array();
	
	
	
	/* --------------------------------------------------------------
	 * PUBLIC METHODS
	 * ------------------------------------------------------------ */
	
	/**
	 * Extends the base class __set method.
	 *
	 * @access	public
	 * @param	string		$prop_name		The property name.
	 * @param	string		$prop_value		The property value.
	 * @return	void
	 */
	public function __set($prop_name = '', $prop_value = '')
	{
		if ($prop_name == 'mailing_lists')
		{
			if (is_array($prop_value))
			{
				$this->_mailing_lists = array();
				foreach ($prop_value AS $val)
				{
					$this->add_mailing_list($val);
				}
			}
		}
		else
		{
			parent::__set($prop_name, $prop_value);
		}
	}
	
	
	/**
	 * Adds a new mailing list to the mailing lists array.
	 *
	 * @access	public
	 * @param	MCS_Mailchimp_subscribe_mailing_list	$list	The mailing list to add.
	 * @return	array
	 */
	public function add_mailing_list(MCS_Mailchimp_subscribe_mailing_list $list)
	{
		$this->_mailing_lists[] = $list;
		return $this->_mailing_lists;
	}
	
}

/* End of file		: MCS_Mailchimp_subscribe_mailing_list.php */
/* File location	: /system/expressionengine/third_party/mailchimp_subscribe/classes/MCS_Mailchimp_subscribe_mailing_list.php */