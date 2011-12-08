<?php

/**
 * MailChimp Merge Variable data class. Very basic implementation.
 *
 * @author		Stephen Lewis <addons@experienceinternet.co.uk>
 * @package		MailChimp Subscribe
 */

class MCS_Merge_variable extends MCS_Base {
	
	/* --------------------------------------------------------------
	 * PROTECTED PROPERTIES
	 * ------------------------------------------------------------ */
	
	/**
	 * The MailChimp merge variable "tag". The ID, essentially.
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_tag = '';
	
	/**
	 * The MailChimp merge variable name.
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_name = '';
	
	/**
	 * The ID of the member field "mapped" to this merge variable.
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_member_field_id;
	
	
	
	/* --------------------------------------------------------------
	 * PUBLIC METHODS
	 * ------------------------------------------------------------ */
	
	/**
	 * Returns the class instance as an array.
	 *
	 * @access	public
	 * @return	array
	 */
	public function to_array() {
		return array(
			'tag'				=> $this->tag,
			'name'				=> $this->name,
			'member_field_id'	=> $this->member_field_id
		);
	}
	
}

/* End of file		: MCS_Merge_variable.php */