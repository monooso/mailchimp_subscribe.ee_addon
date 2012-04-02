<?php

/**
 * MailChimp Subscribe base data class.
 *
 * @abstract
 * @author		Stephen Lewis <addons@experienceinternet.co.uk>
 * @package		MailChimp Subscribe
 */

abstract class MCS_Base {
	
	/* --------------------------------------------------------------
	 * PUBLIC METHODS
	 * ------------------------------------------------------------ */
	
	/**
	 * Class constructor.
	 *
	 * @access	public
	 * @param	Array		$settings		An associative array of settings.
	 * @return	void
	 */
	public function __construct(Array $settings = array())
	{
		$this->populate_from_array($settings);
	}
	
	
	/**
	 * Retrieves a private property, if it exists.
	 *
	 * @access	public
	 * @param	string		$prop_name		The property name.
	 * @return	void
	 */
	public function __get($prop_name = '')
	{
		$private_prop_name = '_' .$prop_name;
		
		return property_exists($this, $private_prop_name)
			? $this->$private_prop_name
			: NULL;
	}
	
	
	/**
	 * Sets a private property, if it exists.
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
			$this->$private_prop_name = $prop_value instanceof MCS_Base
				? $prop_value
				: (string)$prop_value;
		}
	}
	
	
	/**
	 * Sets the object properties using an associative array.
	 *
	 * @access	public
	 * @param	array		$props		An associative array of properties.
	 * @return	Base_entity
	 */
	public function populate_from_array(Array $props = array())
	{
		foreach ($props AS $key => $val)
		{
			$this->$key = $val;
		}
		
		return $this;
	}
	
	
	/**
	 * Returns the class instance as an array.
	 *
	 * @access	public
	 * @return	array
	 */
	public function to_array() {}
	
}

/* End of file		: MCS_Base.php */