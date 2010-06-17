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

require_once PATH_THIRD .'mailchimp_subscribe/library/MCS_Loader' .EXT;

class Mailchimp_model extends CI_Model {
	
	/* --------------------------------------------------------------
	 * PRIVATE PROPERTIES
	 * ------------------------------------------------------------ */
	
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
	 * The extension version. Pulled from the extension static variable.
	 *
	 * @access	private
	 * @var		string
	 */
	private $_extension_version = '';
	
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
		$this->_extension_version = Mailchimp_subscribe_ext::$extension_version;
		$this->_site_id = $this->_ee->config->item('site_id');
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
			$this->db->insert(
				'extensions',
				array(
					'class'		=> $this->_extension_class,
					'enabled'	=> 'y',
					'hook'		=> $hook['hook'],
					'method'	=> $hook['method'],
					'priority'	=> $hook['priority'],
					'version'	=> $this->_extension_version
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
		$this->dbforge->add_field($fields);
		$this->dbforge->add_key('site_id', TRUE);
		$this->dbforge->create_table('mailchimp_subscribe_settings', TRUE);
	}
	
	
	/**
	 * Disables the extension.
	 *
	 * @access	public
	 * @return	void
	 */
	public function disable_extension()
	{
		$this->db->delete('extensions', array('class' => $this->_extension_class));
		
		$this->load->dbforge();
		$this->dbforge->drop_table('mailchimp_subscribe_settings');
	}
	
	
	/**
	 * Retrieves the available mailing lists from the API.
	 *
	 * @access	public
	 * @return	array
	 */
	public function load_mailing_lists()
	{
		// Do we have an API key?
		if ( ! isset($this->_settings['api_key']))
		{
			throw new MCS_Data_exception('Missing API key.');
		}
		
		// Create a new connector object.
		$connector = new MCAPI($this->_settings['api_key']);
		
		// Make the API call.
		$result = $connector->lists();
		
		// Was the call successful?
		if ($connector->errorCode OR $connector->errorMessage)
		{
			throw new MCS_Api_exception($connector->errorMessage, $connector->errorCode);
		}
		
		// Parse the results.
		$lists = array();
		
		foreach ($result AS $r)
		{
			if (isset($this->_settings['mailing_lists'][$r['id']]))
			{
				$selected		= TRUE;
				$trigger_field 	= $this->_settings['mailing_lists'][$r['id']]['trigger_field'];
				$trigger_value 	= $this->_settings['mailing_lists'][$r['id']]['trigger_value'];
			}
			else
			{
				$selected		= FALSE;
				$trigger_field 	= '';
				$trigger_value 	= '';
			}
			
			$list = new MCS_Mailing_list(array(
				'list_id'		=> $r['id'],
				'list_name'		=> $r['name'],
				'selected'		=> $selected,
				'trigger_field'	=> $trigger_field,
				'trigger_value'	=> $trigger_value
			));
			
			$lists[] = $list;
		}
		
		return $lists;
	}
	
	
	/**
	 * Loads the available member fields.
	 *
	 * @access	public
	 * @return	array
	 */
	public function load_member_fields()
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
		$db_member_fields = $this->db->select('m_field_id, m_field_label, m_field_type, m_field_list_items')->get('member_fields');
		
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
		
		return $member_fields;
	}
	
	
	/**
	 * Loads the site settings.
	 *
	 * @access	public
	 * @return	array
	 */
	public function load_settings()
	{
		$this->_settings = $this->_get_default_settings();
		
		// Load the settings from the database.
		$db_settings = $this->db->select('settings')->get_where(
			'mailchimp_subscribe_settings',
			array('site_id' => $this->_site_id),
			1
		);
		
		if ($db_settings->num_rows() == 0)
		{
			return $this->_settings;
		}
		
		$this->_ee->load->helper('string');
		
		$site_settings = unserialize(strip_slashes($db_settings->row()->settings));
		
		foreach ($this->_settings AS $key => $val)
		{
			$this->_settings[$key] = isset($site_settings[$key])
				? $site_settings[$key]
				: $val;
		}
		
		return $this->_settings;
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
		
		$this->db->delete('mailchimp_subscribe_settings', array('site_id' => $this->_site_id));
		$this->db->insert('mailchimp_subscribe_settings', array('site_id' => $this->_site_id, 'settings' => $settings));
		
		return TRUE;
	}
	
	
	/**
	 * Updates the settings from the input.
	 *
	 * @access	public
	 * @return	array
	 */
	public function update_settings_from_input()
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
			foreach ($lists AS $list_id => $list_settings)
			{
				// We're only interested in 'active' lists.
				if ( ! isset($list_settings['checked']))
				{
					continue;
				}
				
				// Basic list information.
				$list = new MCS_Mailing_list(array(
					'list_id'			=> $list_id,
					'trigger_field'		=> isset($list_settings['trigger_field']) ? $list_settings['trigger_field'] : '',
					'trigger_value'		=> isset($list_settings['trigger_value']) ? $list_settings['trigger_value'] : ''
				));
				
				/*
				// Merge variables.
				if (isset($list_settings['merge_vars']) && is_array($list_settings['merge_vars']))
				{
					foreach ($list_settings['merge_vars'] AS $mailchimp_field_id => $member_field_id)
					{
						$list['merge_vars'][$mailchimp_field_id] = array(
							'mailchimp_field_id'	=> $mailchimp_field_id,
							'member_field_id'		=> $member_field_id
						);
					}
				}
				*/
				
				// Add the mailing list to the settings.
				$settings['mailing_lists'][$list_id] = $list;
			}
		}
		
		$this->_settings = $settings;
		return $settings;
	}
	
	
	
	/* --------------------------------------------------------------
	 * PRIVATE METHODS
	 * ------------------------------------------------------------ */
	
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
	
}

/* End of file		: mailchimp_model.php */
/* File location	: /system/expressionengine/third_party/mailchimp_subscribe/models/mailchimp_model.php */
