<?php

/**
 * Settings data class.
 *
 * @abstract
 * @author		Stephen Lewis <addons@experienceinternet.co.uk>
 * @package		MailChimp Subscribe
 */

class MCS_Settings extends MCS_Base {
	
	/* --------------------------------------------------------------
	 * PROTECTED PROPERTIES
	 * ------------------------------------------------------------ */
	
	/**
	 * API key.
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_api_key = '';
	
	/**
	 * Mailing lists.
	 *
	 * @access	protected
	 * @var		array
	 */
	protected $_mailing_lists = array();
	
	
	
	/* --------------------------------------------------------------
	 * PUBLIC METHODS
	 * ------------------------------------------------------------ */
	
	/**
	 * Overrides the basic __set method, to handle mailing lists.
	 *
	 * @access	public
	 * @param	string		$prop_name		The property name.
	 * @param	string		$prop_value		The property value.
	 * @return	void
	 */
	public function __set($prop_name = '', $prop_value = '')
	{
		if ($prop_name != 'mailing_lists')
		{
			parent::__set($prop_name, $prop_value);
			return;
		}
		
		/**
		 * Handle the mailing lists.
		 */
		
		if ( ! is_array($prop_value))
		{
			return;
		}
		
		$this->_mailing_lists = array();
		
		foreach ($prop_value AS $list)
		{
			$this->add_mailing_list($list);
		}
	}
	
	/**
	 * Adds a mailing list.
	 *
	 * @access	public
	 * @param	MCS_Mailing_list	$list		The mailing list.
	 * @return	void
	 */
	public function add_mailing_list(MCS_Mailing_list $list)
	{
		$this->_mailing_lists[$list->id] = $list;
	}
	
	
	/**
	 * Returns the specified mailing list, if it exists.
	 *
	 * @access	public
	 * @param	string		$list_id	The mailing list ID.
	 * @return	mixed
	 */
	public function get_mailing_list($list_id = '')
	{
		return array_key_exists($list_id, $this->_mailing_lists)
			? $this->_mailing_lists[$list_id]
			: FALSE;
	}
	
	
	/**
	 * Overrides the parent method. Sets the object properties using
	 * an associative array.
	 *
	 * @access	public
	 * @param	array		$props		An associative array of properties.
	 * @return	Base_entity
	 */
	public function populate_from_array(Array $props = array())
	{
		foreach ($props AS $key => $val)
		{
			if ($key == 'mailing_lists')
			{
				foreach ($val AS $list)
				{
					if ($list instanceof MCS_Mailing_list)
					{
						$this->add_mailing_list($list);
					}
					elseif (is_array($list))
					{
						$this->add_mailing_list(new MCS_Mailing_list($list));
					}
				}
			}
			else
			{
				$this->$key = $val;
			}
		}
		
		return $this;
	}
	
	
	/**
	 * Removes a mailing list.
	 *
	 * @access	public
	 * @param	string		$list_id		The mailing list ID.
	 * @return	mixed
	 */
	public function remove_mailing_list($list_id = '')
	{
		if (array_key_exists($list_id, $this->_mailing_lists))
		{
			$return = $this->_mailing_lists[$list_id];
			unset($this->_mailing_lists[$list_id]);
		}
		else
		{
			$return = FALSE;
		}
		
		return $return;
	}
	
	
	/**
	 * Returns the class instance as an array.
	 *
	 * @access	public
	 * @return	array
	 */
	public function to_array() {
		
		$return = array(
			'api_key'		=> $this->api_key,
			'mailing_lists'	=> array()
		);
		
		foreach ($this->mailing_lists AS $key => $val)
		{
			$return['mailing_lists'][$key] = $val->to_array();
		}
		
		return $return;
	}
	
}

/* End of file		: MCS_Settings.php */