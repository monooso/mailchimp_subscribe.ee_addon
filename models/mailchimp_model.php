<?php if ( ! defined('BASEPATH')) exit('Invalid file request');

/**
 * Handles all the API and database communication for MailChimp Subscribe.
 *
 * @author		Stephen Lewis <addons@experienceinternet.co.uk>
 * @license		??
 * @link 		http://experienceinternet.co.uk/software/mailchimp-subscribe/
 * @package		MailChimp Subscribe
 * @version		2.0.0b1
 */

require_once PATH_THIRD .'mailchimp_subscribe/library/MCS_Exceptions' .EXT;
require_once PATH_THIRD .'mailchimp_subscribe/library/MCAPI.class' .EXT;

class Mailchimp_model extends CI_Model {
	
	/* --------------------------------------------------------------
	 * PRIVATE PROPERTIES
	 * ------------------------------------------------------------ */
	
	/**
	 * The API user account.
	 *
	 * @access	private
	 * @var		array
	 */
	private $_api_account = array();
	
	/**
	 * The API connector.
	 *
	 * @access	private
	 * @var		object
	 */
	private $_connector = NULL;
	
	/**
	 * ExpressionEngine object.
	 *
	 * @access	private
	 * @var		object
	 */
	private $_ee;
	
	/**
	 * The extension class name.
	 *
	 * @access	private
	 * @var		string
	 */
	private $_extension_class = 'Mailchimp_subscribe_ext';
	
	/**
	 * The extension version.
	 *
	 * @access	private
	 * @var		string
	 */
	private $_version = '2.0.0b1';
	
	/**
	 * Mailing lists.
	 *
	 * @access	private
	 * @var		array
	 */
	private $_mailing_lists = array();
	
	
	/**
	 * Member fields.
	 *
	 * @access	private
	 * @var		array
	 */
	private $_member_fields = array();
	
	/**
	 * The extension settings.
	 *
	 * @access	private
	 * @var		array
	 */
	private $_settings = array();
	
	/**
	 * The site ID.
	 *
	 * @access	private
	 * @var		string
	 */
	private $_site_id = '1';
	
	
	
	/* --------------------------------------------------------------
	 * PUBLIC METHODS
	 * ------------------------------------------------------------ */
	
	/**
	 * Class constructor.
	 *
	 * @access	public
	 * @return	void
	 */
	public function __construct()
	{
		$this->_ee =& get_instance();
		$this->_site_id = $this->_ee->config->item('site_id');
		
		/**
		 * Annoying, this method is still called, even if the extension
		 * isn't installed. We need to check if such nonsense is afoot,
		 * and exit promptly if so.
		 */
		
		if ( ! isset($this->_ee->extensions->version_numbers[$this->_extension_class]))
		{
			return;
		}
		
		// Load and update the settings first.
		$this->_load_settings_from_db();
		$this->_update_settings_from_input();
		
		// Load the member fields from the database.
		$this->_load_member_fields_from_db();
		
		/**
		 * If the API key is set, do the following, in this order:
		 * 1. Create a new API connector.
		 * 2. Load the account details from the API.
		 * 3. Load the mailing lists from the API.
		 */
		
		if (isset($this->_settings['api_key']))
		{
			// Create a new connector object.
			$this->_connector = new MCAPI($this->_settings['api_key']);
			
			try
			{
				$this->_load_account_from_api();
				$this->_load_mailing_lists_from_api();
			}
			catch (MCS_exception $exception)
			{
				$this->log_error($exception->getMessage(), $exception->getCode());
			}
		}
	}
	
	
	/**
	 * Activates the extension.
	 *
	 * @access	public
	 * @return	void
	 */
	public function activate_extension()
	{
		$hooks = array(
			array(
				'hook'		=> 'cp_members_member_create',
				'method'	=> 'cp_members_member_create',
				'priority'	=> 10
			),
			array(
				'hook'		=> 'cp_members_validate_members',
				'method'	=> 'cp_members_validate_members',
				'priority'	=> 10
			),
			array(
				'hook'		=> 'member_member_register',
				'method'	=> 'member_member_register',
				'priority'	=> 10
			),
			array(
				'hook'		=> 'member_register_validate_members',
				'method'	=> 'member_register_validate_members',
				'priority'	=> 10
			),
			array(
				'hook'		=> 'user_edit_end',
				'method'	=> 'user_edit_end',
				'priority'	=> 10
			),
			array(
				'hook'		=> 'user_register_end',
				'method'	=> 'user_register_end',
				'priority'	=> 10
			)
		);
		
		foreach ($hooks AS $hook)
		{
			$this->_ee->db->insert(
				'extensions',
				array(
					'class'		=> $this->_extension_class,
					'enabled'	=> 'y',
					'hook'		=> $hook['hook'],
					'method'	=> $hook['method'],
					'priority'	=> $hook['priority'],
					'version'	=> $this->_version
				)
			);
		}
		
		// Create the settings table.
		$fields = array(
			'site_id' => array(
				'constraint'	=> 8,
				'null'			=> FALSE,
				'type'			=> 'int',
				'unsigned'		=> TRUE
			),
			'settings' => array(
				'null'			=> FALSE,
				'type'			=> 'text',
			)
		);
		
		$this->load->dbforge();
		$this->_ee->dbforge->add_field($fields);
		$this->_ee->dbforge->add_key('site_id', TRUE);
		$this->_ee->dbforge->create_table('mailchimp_subscribe_settings', TRUE);
		
		// Create the 'error log' table.
		$fields = array(
			'error_log_id' => array(
				'auto_increment' => TRUE,
				'constraint'	=> 10,
				'null'			=> FALSE,
				'type'			=> 'int',
				'unsigned'		=> TRUE
			),
			'site_id' => array(
				'constraint'	=> 5,
				'default'		=> 1,
				'null'			=> FALSE,
				'type'			=> 'int',
				'unsigned'		=> TRUE
			),
			'error_date' => array(
				'constraint'	=> 10,
				'null'			=> FALSE,
				'type'			=> 'int',
				'unsigned'		=> TRUE
			),
			'error_code' => array(
				'constraint'	=> 10,
				'null'			=> TRUE,
				'type'			=> 'varchar'
			),
			'error_message' => array(
				'constraint'	=> 255,
				'null'			=> TRUE,
				'type'			=> 'varchar'
			)
		);
		
		$this->_ee->dbforge->add_field($fields);
		$this->_ee->dbforge->add_key('error_log_id', TRUE);
		$this->_ee->dbforge->add_key('site_id', FALSE);
		$this->_ee->dbforge->create_table('mailchimp_subscribe_error_log', TRUE);
	}
	
	
	/**
	 * Disables the extension.
	 *
	 * @access	public
	 * @return	void
	 */
	public function disable_extension()
	{
		$this->_ee->db->delete('extensions', array('class' => $this->_extension_class));
		
		$this->load->dbforge();
		$this->_ee->dbforge->drop_table('mailchimp_subscribe_settings');
		$this->_ee->dbforge->drop_table('mailchimp_subscribe_error_log');
	}
	
	
	/**
	 * Returns the API account details.
	 *
	 * @access	public
	 * @return	array
	 */
	public function get_api_account()
	{
		return $this->_api_account;
	}
	
	
	/**
	 * Returns the error log.
	 *
	 * @access	public
	 * @return	array
	 */
	public function get_error_log()
	{
		$db_error_log = $this->_ee->db->get_where('mailchimp_subscribe_error_log', array('site_id' => $this->_site_id));
		return $db_error_log->result_array();
	}
	
	
	/**
	 * Retrieves the available mailing lists from the API.
	 *
	 * @access	public
	 * @return	array
	 */
	public function get_mailing_lists()
	{
		return $this->_mailing_lists;
	}
	
	
	/**
	 * Returns the available member fields.
	 *
	 * @access	public
	 * @return	array
	 */
	public function get_member_fields()
	{
		return $this->_member_fields;
	}
	
	
	/**
	 * Returns the site settings.
	 *
	 * @access	public
	 * @return	array
	 */
	public function get_settings()
	{
		return $this->_settings;
	}
	
	
	/**
	 * Returns the extension version.
	 *
	 * @access	public
	 * @return	string
	 */
	public function get_version()
	{
		return $this->_version;
	}
	
	
	/**
	 * Logs an error to the database.
	 *
	 * @access	public
	 * @param	string		$message	The error message.
	 * @param	string		$code		The error code.
	 * @return	void
	 */
	public function log_error($message = '', $code = '')
	{
		$this->_ee->db->insert(
			'mailchimp_subscribe_error_log',
			array(
				'site_id'		=> $this->_site_id,
				'error_date'	=> time(),
				'error_code'	=> $code,
				'error_message'	=> $message
			)
		);
	}
	
	
	/**
	 * Saves the site settings.
	 *
	 * @access	public
	 * @return	bool
	 */
	public function save_settings()
	{
		$settings = addslashes(serialize($this->_settings));
		
		$this->_ee->db->delete('mailchimp_subscribe_settings', array('site_id' => $this->_site_id));
		$this->_ee->db->insert('mailchimp_subscribe_settings', array('site_id' => $this->_site_id, 'settings' => $settings));
		
		return TRUE;
	}
	
	
	/**
	 * Subscribes a member to the active mailing list(s).
	 *
	 * @access	public
	 * @param 	string 		$member_id 		The member ID.
	 * @return 	void
	 */	
	public function subscribe_member($member_id)
	{
		try
		{
			$this->_update_member_subscriptions($member_id, FALSE);
		}
		catch (MCS_Exception $exception)
		{
			$this->log_error($exception->getMessage(), $exception->getCode());
		}
	}
	
	
	/**
	 * Updates a member's mailing list subscriptions.
	 *
	 * @access	public
	 * @param	string		$member_id		The member ID.
	 * @return	void
	 */
	public function update_member_subscriptions($member_id = '')
	{
		try
		{
			$this->_update_member_subscriptions($member_id, TRUE);
		}
		catch (MCS_Exception $exception)
		{
			$this->log_error($exception->getMessage(), $exception->getCode());
		}
	}
	
	
	/**
	 * Updates the extension.
	 *
	 * @access	public
	 * @param 	string		$current_version		The current version.
	 * @return	bool
	 */
	public function update_extension($current_version = '')
	{
		if ( ! $current_version OR $current_version == $this->_version)
		{
			return FALSE;
		}
		
		// Update the version number.
		if ($current_version < $this->_version)
		{
			$this->_ee->db->update(
				'extensions',
				array('version' => $this->_version),
				array('class' => $this->_extension_class)
			);
		}
		
		return TRUE;
	}
	
	
	
	/* --------------------------------------------------------------
	 * PRIVATE METHODS
	 * ------------------------------------------------------------ */
	
	/**
	 * Handles all API requests.
	 *
	 * @access	private
	 * @param	string	$method	The API method to call.
	 * @param	array	$params	An additional parameters to include in the API call.
	 * @return	void
	 */
	private function _call_api($method = '', Array $params = array())
	{
		// Do we have an API key?
		if ( ! isset($this->_settings['api_key']))
		{
			throw new MCS_Data_exception('Unable to call API method "' .$method .'" (missing API key).');
		}
		
		if ( ! method_exists($this->_connector, $method))
		{
			throw new MCS_Api_exception('Unknown API method "' .$method .'".');
		}
		
		$result = call_user_func_array(array($this->_connector, $method), $params);
		
		// Was the connector method called successfully?
		if ($result === FALSE)
		{
			throw new MCS_Api_exception('Unable to call API method "' .$method .'".');
		}
		
		// Was the API method called successfully.
		if ($this->_connector->errorCode)
		{
			throw new MCS_Api_exception($this->_connector->errorMessage, $this->_connector->errorCode);
		}
		
		return $result;
	}
	
	
	/**
	 * Builds the default API account array.
	 *
	 * @access	private
	 * @return	array
	 */
	private function _get_default_api_account()
	{
		return array(
			'username'			=> '',
			'user_id'			=> '',
			'is_trial'			=> FALSE,
			'timezone'			=> '',
			'plan_type'			=> '',
			'plan_low'			=> 0,
			'plan_high'			=> 0,
			'plan_start_date'	=> '',
			'emails_left'		=> 0,
			'pending_monthly'	=> 0,
			'first_payment'		=> '',
			'last_payment'		=> '',
			'times_logged_in'	=> 0,
			'last_login'		=> '',
			'affiliate_link'	=> '',
			'contact'			=> array(),
			'modules'			=> array(),
			'orders'			=> array(),
			'rewards'			=> array()
		);
	}
	
	
	/**
	 * Builds the default settings array.
	 *
	 * @access	private
	 * @return	array
	 */
	private function _get_default_settings()
	{
		return array(
			'api_key'		=> '',
			'mailing_lists'	=> array()
		);
	}
	
	
	/**
	 * Retrieves the user account details from the API.
	 *
	 * @access	private
	 * @return	void
	 */
	private function _load_account_from_api()
	{
		/**
		 * Reset the account details. We do this first in case
		 * the API call throws an exception.
		 */
		
		$this->_api_account = $this->_get_default_api_account();
		
		// Make the API call.
		$this->_api_account = $this->_call_api('getAccountDetails');
	}
	
	
	/**
	 * Retrieves the mailing lists from the API.
	 *
	 * @access	private
	 * @return	void
	 */
	private function _load_mailing_lists_from_api()
	{
		// Reset the mailing lists.
		$this->_mailing_lists = array();
		
		// Make the API call.
		$result = $this->_call_api('lists');
		
		// Parse the results.
		$lists = array();
		$unsubscribe_url = 'http://list-manage.com/unsubscribe?u=%uid&amp;id=%lid';
		
		foreach ($result AS $r)
		{
			/**
			 * Interest Groups:
			 * The API returns a 211 error if Interest Groups are not enabled
			 * for this list. We don't want to fail because of that, so we
			 * explicitly check for the error code.
			 */
			
			try
			{
				$interest_groups = $this->_call_api('listInterestGroups', array($r['id']));
			}
			catch (MCS_Exception $exception)
			{
				if ($exception->getCode() !== 211)
				{
					throw $exception;
				}
			}
			
			// Merge variables.
			$merge_vars = $this->_call_api('listMergeVars', array($r['id']));
			
			// Add the list.
			$lists[] = array(
				'interest_groups'	=> isset($interest_groups['name']) ? $interest_groups['name'] : '',
				'list_id'			=> $r['id'],
				'list_name'			=> $r['name'],
				'merge_vars'		=> $merge_vars,
				'unsubscribe_url'	=> isset($this->_api_account['user_id']) ? sprintf($unsubscribe_url, $this->_api_account['user_id'], $r['id']) : ''
			);
		}
		
		$this->_mailing_lists = $lists;
	}
	
	
	/**
	 * Loads the member fields from the database.
	 *
	 * @access	private
	 * @return	array
	 */
	private function _load_member_fields_from_db()
	{
		/**
		 * The default ExpressionEngine member fields are
		 * hard-coded. Not ideal, but we roll with it.
		 */
		
		$this->_ee->lang->loadfile('member');

		$member_fields = array(
			'location' => array(
				'id'		=> 'location',
				'label'		=> lang('mbr_location'),
				'options'	=> array(),
				'type'		=> 'text'
			),
			'screen_name' => array(
				'id'		=> 'screen_name',
				'label'		=> lang('mbr_screen_name'),
				'options'	=> array(),
				'type'		=> 'text'
			),
			'url' => array(
				'id'		=> 'url',
				'label'		=> lang('mbr_url'),
				'options'	=> array(),
				'type'		=> 'text'
			),
			'username' => array(
				'id'		=> 'username',
				'label'		=> lang('mbr_username'),
				'options'	=> array(),
				'type'		=> 'text'
			)
		);
		
		// Load the custom member fields.
		$db_member_fields = $this->_ee->db->select('m_field_id, m_field_label, m_field_type, m_field_list_items')->get('member_fields');
		
		if ($db_member_fields->num_rows() > 0)
		{
			foreach ($db_member_fields->result() AS $row)
			{
				$member_fields['m_field_id_' .$row->m_field_id] = array(
					'id'		=> 'm_field_id_' .$row->m_field_id,
					'label'		=> $row->m_field_label,
					'options'	=> $row->m_field_type == 'select' ? explode("\n", $row->m_field_list_items) : array(),
					'type'		=> $row->m_field_type == 'select' ? 'select' : 'text'
				);
			}
		}
		
		$this->_member_fields = $member_fields;
	}
	
	
	/**
	 * Loads the settings from the database.
	 *
	 * @access	private
	 * @return	void
	 */
	private function _load_settings_from_db()
	{
		$settings = $this->_get_default_settings();
		
		// Load the settings from the database.
		$db_settings = $this->_ee->db->select('settings')->get_where(
			'mailchimp_subscribe_settings',
			array('site_id' => $this->_site_id),
			1
		);
		
		// If we have saved settings, parse them.
		if ($db_settings->num_rows() > 0)
		{
			$this->_ee->load->helper('string');

			$site_settings = unserialize(strip_slashes($db_settings->row()->settings));

			foreach ($settings AS $key => $val)
			{
				$settings[$key] = isset($site_settings[$key])
					? $site_settings[$key]
					: $val;
			}
		}
		
		$this->_settings = $settings;
	}
	
	
	/**
	 * Subscribes a member to the active mailing lists, or updates
	 * a member's existing subscriptions.
	 *
	 * @access	private
	 * @param	string		$member_id		The member ID.
	 * @param	bool		$update			Are we updating existing subscriptions?
	 * @return	void
	 */
	private function _update_member_subscriptions($member_id = '', $update = FALSE)
	{
		// Check that we have a member ID.
		if ( ! $member_id)
		{
			throw new MCS_Data_exception('Unable to update member subscriptions (missing member ID).');
		}
		
		$active_lists 		= $this->_settings['mailing_lists'];
		$subscribe_to 		= array();
		$unsubscribe_from	= array();
		
		// Retrieve the member.
		$member = $this->get_member_by_id($member_id);
		
		// Is the member banned?
		if (in_array($member['group_id'], array('2', '4')))
		{
			throw new MCS_Data_exception('Unable to update subscriptions for banned member ' .$member['screen_name'] .' (' .$member_id .')');
		}
		
		/**
		 * Process the mailing lists.
		 */
		
		foreach ($active_lists AS $list)
		{
			/**
			 * If there is no trigger field, the member must be
			 * subscribed to the list.
			 */
			
			if ( ! $list['trigger_field'])
			{
				$subscribe_to[] = $list;
				continue;
			}
			
			/**
			 * If there is a trigger field, we need to check whether
			 * the member has opted-in to this list.
			 */
			
			if (isset($member[$list['trigger_field']]) && $member[$list['trigger_field']] === $list['trigger_value'])
			{
				$subscribe_to[] = $list;
			}
			else
			{
				$unsubscribe_from[] = $list;
			}
		}
		
		// Do we have an work to do?
		if (count($subscribe_to) == 0 && ($update == FALSE OR count($unsubscribe_from) == 0))
		{
			return;
		}
		
		// Let's get APIing. That's totally a word.
		foreach ($subscribe_to AS $list)
		{
			// Merge variables.
			$merge_vars = array();
			
			if (isset($list['merge_vars']) && is_array($list['merge_vars']))
			{
				foreach ($list['merge_vars'] AS $id => $val)
				{
					if (isset($val['mailchimp_field_id']) && isset($val['member_field_id']) && isset($member[$val['member_field_id']]))
					{
						$merge_vars[$val['mailchimp_field_id']] = $member[$val['member_field_id']];
					}
				}
			}
			
			// Interest groups.
			if (isset($list['interest_groups']) && isset($member[$list['interest_groups']]))
			{
				$merge_vars['INTERESTS'] = $member[$list['interest_groups']];
			}
			
			// Finally we can make the API call.
			$this->_call_api('listSubscribe', array(
				$list['list_id'],
				$member['email'],
				$merge_vars,
				'html',				// Email format.
				FALSE,				// Double opt-in?
				(bool)$update		// Update existing subscription?
			));
		}
		
		// Process the unsubscriptions.
		if ($update)
		{
			foreach ($unsubscribe_from AS $list)
			{
				$this->_call_api('listUnsubscribe', array($list['list_id'], $member['email']));
			}
		}
	}
	
	
	/**
	 * Updates the settings from the input.
	 *
	 * @access	private
	 * @param 	array 		$settings		The settings to update.
	 * @return	array
	 */
	private function _update_settings_from_input()
	{
		// Safety first.
		$settings = array_merge($this->_get_default_settings(), $this->_settings);
		
		// Update the API key. This is the easy bit.
		$settings['api_key'] = $this->_ee->input->get_post('api_key')
			? $this->_ee->input->get_post('api_key')
			: $settings['api_key'];
			
		// The mailing lists require rather more work.
		if (is_array($lists = $this->_ee->input->get_post('mailing_lists')))
		{
			$settings['mailing_lists'] = array();
			
			foreach ($lists AS $list_id => $list_settings)
			{
				if ( ! isset($list_settings['checked']))
				{
					continue;
				}
				
				// Basic list information.
				$list = array(
					'list_id'		=> $list_id,
					'trigger_field'	=> isset($list_settings['trigger_field']) ? $list_settings['trigger_field'] : '',
					'trigger_value'	=> isset($list_settings['trigger_value']) ? $list_settings['trigger_value'] : '',
					'interest_groups' => isset($list_settings['interest_groups']) ? $list_settings['interest_groups'] : '',
					'merge_vars'	=> array()
				);
				
				// Merge variables.
				if (isset($list_settings['merge_vars']) && is_array($list_settings['merge_vars']))
				{
					foreach ($list_settings['merge_vars'] AS $mailchimp_field_id => $member_field_id)
					{
						$list['merge_vars'][$mailchimp_field_id] = array(
							'mailchimp_field_id' => $mailchimp_field_id,
							'member_field_id'	=> $member_field_id
						);
					}
				}
				
				$settings['mailing_lists'][$list_id] = $list;
			}
		}
		
		$this->_settings = $settings;
	}
	
}

/* End of file		: mailchimp_model.php */
/* File location	: /system/expressionengine/third_party/mailchimp_subscribe/models/mailchimp_model.php */