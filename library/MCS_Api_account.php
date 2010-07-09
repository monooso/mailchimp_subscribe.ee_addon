<?php

/**
 * API Account data class. Very basic implementation.
 *
 * @author		Stephen Lewis <addons@experienceinternet.co.uk>
 * @package		MailChimp Subscribe
 */

class MCS_Api_account extends MCS_Base {
	
	/* --------------------------------------------------------------
	 * PROTECTED PROPERTIES
	 * ------------------------------------------------------------ */
	
	/**
	 * Username.
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_username = '';
	
	/**
	 * User ID.
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_user_id = '';
	
	
	
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
			'username'	=> $this->username,
			'user_id'	=> $this->user_id
		);
	}
	
}

/* End of file		: MCS_Api_account.php */