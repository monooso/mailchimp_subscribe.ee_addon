<?php if ( ! defined('BASEPATH')) exit('Invalid file request');

/**
 * MailChimp base class.
 *
 * @author		Stephen Lewis <addons@experienceinternet.co.uk>
 * @license		??
 * @link 		http://experienceinternet.co.uk/software/mailchimp-subscribe/
 * @package		MailChimp Subscribe
 * @version		2.0.0b1
 */

class MCS_Base {
	
	/* --------------------------------------------------------------
	 * PUBLIC METHODS
	 * ------------------------------------------------------------ */
	
	/**
	 * Class constructor.
	 *
	 * @access	public
	 * @param	array		$properties		An associative array of properties.
	 * @return	void
	 */
	public function __construct(Array $properties = array())
	{
		foreach ($properties AS $key => $val)
		{
			$this->$key = $val;
		}
	}
	
	
	/**
	 * "Magic" method to retrieve a property.
	 *
	 * @access	public
	 * @param	string		$prop_name		The property name.
	 * @return	mixed
	 */
	public function __get($prop_name = '')
	{
		$private_prop_name = '_' .$prop_name;
		
		return property_exists($this, $private_prop_name)
			? $this->$private_prop_name
			: NULL;
	}
	
	
	/**
	 * "Magic" method to set the properties.
	 *
	 * @access	public
	 * @param	string		$prop_name		The property name.
	 * @param	string		$prop_value		The property value.
	 * @return	void
	 */
	public function __set($prop_name = '', $prop_value = '')
	{
		$private_prop_name = '_' .$prop_name;
		
		if (property_exists($this, $private_prop_name))
		{
			$this->$private_prop_name = $prop_value;
		}
	}
	
}

/* End of file		: EI_Base.php */