<?php

/**
 * MailChimp Interest Group data class. Very basic implementation.
 *
 * @abstract
 * @author		Stephen Lewis <addons@experienceinternet.co.uk>
 * @package		MailChimp Subscribe
 */

class MCS_Interest_group extends MCS_Base {
	
	/* --------------------------------------------------------------
	 * PROTECTED PROPERTIES
	 * ------------------------------------------------------------ */
	
	/**
	 * The MailChimp interest group ID.
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_id = '';
	
	/**
	 * The MailChimp interest group name.
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_name = '';
	
	/**
	 * The ID of the member field "mapped" to this interest group.
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
			'id'				=> $this->id,
			'name'				=> $this->name,
			'member_field_id'	=> $this->member_field_id
		);
	}
	
}

/* End of file		: MCS_Interest_group.php */