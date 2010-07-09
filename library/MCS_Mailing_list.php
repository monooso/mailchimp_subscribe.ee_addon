<?php

/**
 * Mailing List data class. Very basic implementation.
 *
 * @author		Stephen Lewis <addons@experienceinternet.co.uk>
 * @package		MailChimp Subscribe
 */

class MCS_Mailing_list extends MCS_Base {
	
	/* --------------------------------------------------------------
	 * PROTECTED PROPERTIES
	 * ------------------------------------------------------------ */
	
	/**
	 * Is this an 'active' (i.e. selected) mailing list?
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_active = 'n';
	
	/**
	 * ID.
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_id = '';
	
	/**
	 * Interest groups.
	 *
	 * @access	protected
	 * @var		array
	 */
	protected $_interest_groups = array();
	
	/**
	 * Merge variables
	 *
	 * @access	protected
	 * @var		array
	 */
	protected $_merge_variables = array();
	
	/**
	 * Name.
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_name = '';
	
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
	
	/**
	 * Unsubscribe URL.
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_unsubscribe_url = '';
	
	
	
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
		if ($prop_name != 'interest_groups' && $prop_name != 'merge_variables')
		{
			parent::__set($prop_name, $prop_value);
			return;
		}
		
		if ( ! is_array($prop_value))
		{
			return;
		}
		
		// Interest Groups.
		$this->_interest_groups = array();
		
		foreach ($prop_value AS $group)
		{
			$this->add_interest_group($group);
		}
		
		// Merge Variables.
		$this->_merge_variables = array();
		
		foreach ($prop_value AS $var)
		{
			$this->add_merge_variable($var);
		}
	}
	
	
	/**
	 * Adds an interest group.
	 *
	 * @access	public
	 * @param	MCS_Interest_group		$group		The interest group.
	 * @return	void
	 */
	public function add_interest_group(MCS_Interest_group $group)
	{
		$this->_interest_groups[$group->id] = $group;
	}
	
	
	/**
	 * Adds a merge variable.
	 *
	 * @access	public
	 * @param	MCS_Merge_variable		$var		The merge variable.
	 * @return	void
	 */
	public function add_merge_variable(MCS_Merge_variable $var)
	{
		$this->_merge_variables[$var->tag] = $var;
	}
	
	
	/**
	 * Returns the specified interest group, if it exists.
	 *
	 * @access	public
	 * @param	string		$group_id		The group ID.
	 * @return	mixed
	 */
	public function get_interest_group($group_id = '')
	{
		return array_key_exists($group_id, $this->_interest_groups)
			? $this->_interest_groups[$group_id]
			: FALSE;
	}
	
	
	/**
	 * Returns the specified merge variable, if it exists.
	 *
	 * @access	public
	 * @param	string		$var_tag	The merge variable tag.
	 * @return	mixed
	 */
	public function get_merge_variable($var_tag = '')
	{
		return array_key_exists($var_tag, $this->_merge_variables)
			? $this->_merge_variables[$var_tag]
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
			switch ($key)
			{
				case 'interest_groups':
					foreach ($val AS $group)
					{
						if ($group instanceof MCS_Interest_group)
						{
							$this->add_interest_group($group);
						}
						elseif (is_array($group))
						{
							$this->add_interest_group(new MCS_Interest_group($group));
						}
					}
					
					break;
					
				case 'merge_variables':
					foreach ($val AS $var)
					{
						if ($var instanceof MCS_Merge_variable)
						{
							$this->add_merge_variable($var);
						}
						elseif (is_array($var))
						{
							$this->add_merge_variable(new MCS_Merge_variable($var));
						}
					}
					break;
					
				default:
					$this->$key = $val;
					break;
			}
		}
		
		return $this;
	}
	
	
	/**
	 * Removes an interest group.
	 *
	 * @access	public
	 * @param	string	$group_id	The interest group ID.
	 * @return	mixed
	 */
	public function remove_interest_group($group_id = '')
	{
		if (array_key_exists($group_id, $this->_interest_groups))
		{
			$return = $this->_interest_groups[$group_id];
			unset($this->_interest_groups[$group_id]);
		}
		else
		{
			$return = FALSE;
		}
		
		return $return;
	}
	
	
	/**
	 * Removes a merge variable.
	 *
	 * @access	public
	 * @param	string	$var_tag	The merge variable "tag".
	 * @return	mixed
	 */
	public function remove_merge_variable($var_tag = '')
	{
		if (array_key_exists($var_tag, $this->_merge_variables))
		{
			$return = $this->_merge_variables[$var_tag];
			unset($this->_merge_variables[$var_tag]);
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
			'active'			=> $this->active,
			'id'				=> $this->id,
			'interest_groups'	=> array(),
			'merge_variables'	=> array(),
			'name'				=> $this->name,
			'trigger_field'		=> $this->trigger_field,
			'trigger_value'		=> $this->trigger_value,
			'unsubscribe_url'	=> $this->unsubscribe_url
		);
		
		foreach ($this->interest_groups AS $key => $val)
		{
			$return['interest_groups'][$key] = $val->to_array();
		}
		
		foreach ($this->merge_variables AS $key => $val)
		{
			$return['merge_variables'][$key] = $val->to_array();
		}
		
		return $return;
	}
	
}

/* End of file		: MCS_Mailing_list.php */