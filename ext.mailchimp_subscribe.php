<?php if ( ! defined('BASEPATH')) exit('Invalid file request');

/**
 * Effortlessly add members of your ExpressionEngine site to your MailChimp mailing lists.
 *
 * @author		Stephen Lewis <addons@experienceinternet.co.uk>
 * @license		??
 * @link 		http://experienceinternet.co.uk/software/mailchimp-subscribe/
 * @package		MailChimp Subscribe
 * @version		2.0.0b1
 */

class Mailchimp_subscribe_ext {
	
	/* --------------------------------------------------------------
	 * PUBLIC PROPERTIES
	 * ------------------------------------------------------------ */
	
	/**
	 * Description.
	 *
	 * @access	public
	 * @var		string
	 */
	public $description = 'Effortlessly add members of your ExpressionEngine site to your MailChimp mailing lists.';
	
	/**
	 * Documentation URL.
	 *
	 * @access	public
	 * @var		string
	 */
	public $docs_url = 'http://experienceinternet.co.uk/software/mailchimp-subscribe/';
	
	/**
	 * Extension name.
	 *
	 * @access	public
	 * @var		string
	 */
	public $name = 'MailChimp Subscribe';
	
	/**
	 * Settings.
	 *
	 * @access	public
	 * @var		array
	 */
	public $settings = array();
	
	/**
	 * Does this extension have a settings screen?
	 *
	 * @access	public
	 * @var		string
	 */
	public $settings_exist = 'y';
	
	/**
	 * Version. Set in the constructor, so we can reference the static class variable.
	 *
	 * @access	public
	 * @var		string
	 */
	public $version = '';
	
	
	
	/* --------------------------------------------------------------
	 * PRIVATE PROPERTIES
	 * ------------------------------------------------------------ */
	
	/**
	 * Instance of the ExpressionEngine object.
	 *
	 * @access	private
	 * @var		object
	 */
	private $_ee = NULL;
	
	
	
	/* --------------------------------------------------------------
	 * PUBLIC METHODS
	 * ------------------------------------------------------------ */

	/**
	 * Class constructor.
	 *
	 * @access	public
	 * @param	array 		$settings		Previously-saved extension settings.
	 * @return	void
	 */
	public function __construct($settings = array())
	{
		$this->_ee =& get_instance();
		
		// Load our glamorous assistants.
		$this->_ee->load->helper('form');
		$this->_ee->load->library('table');
		
		// Need to explicitly set the package path, annoyingly.
		$this->_ee->load->add_package_path(PATH_THIRD .'mailchimp_subscribe/');
		$this->_ee->load->model('mailchimp_model');
		
		// Retrieve the version.
		$this->version = $this->_ee->mailchimp_model->get_version();
		
		// Define the navigation.
		$this->_ee->cp->set_right_nav(array(
			'nav_settings'		=> '#settings',
			'nav_unsubscribe'	=> '#unsubscribe',
			'nav_error_log'		=> '#errors'
		));
	}
	
	
	/**
	 * Activates the extension.
	 *
	 * @access	public
	 * @return	void
	 */
	public function activate_extension()
	{
		$this->_ee->mailchimp_model->activate_extension();
	}
	
	
	/**
	 * Disables the extension.
	 *
	 * @access	public
	 * @return	void
	 */
	public function disable_extension()
	{
		$this->_ee->mailchimp_model->disable_extension();
	}
	
	
	/**
	 * Saves the extension settings.
	 *
	 * @access	public
	 * @return	void
	 */
	public function save_settings()
	{
		// Need to explicitly load the language file.
		$this->_ee->lang->loadfile('mailchimp_subscribe');
		
		if ($this->_ee->mailchimp_model->save_settings())
		{
			$this->_ee->session->set_flashdata('message_success', $this->_ee->lang->line('settings_saved'));
		}
		else
		{
			$this->_ee->session->set_flashdata('message_failure', $this->_ee->lang->line('settings_not_saved'));
		}
	}
	
	
	/**
	 * Displays the extension settings form.
	 *
	 * @access	public
	 * @return	string
	 */
	public function settings_form()
	{
		// Load the member fields.
		$member_fields = $this->_ee->mailchimp_model->get_member_fields();
		$member_field_options = array();

		foreach ($member_fields AS $key => $data)
		{
			$member_field_options[$key] = $data['label'];
		}
		
		// Collate the view variables.
		$vars = array(
			'action_url' 			=> 'C=addons_extensions' .AMP .'M=save_extension_settings',
			'cp_page_title'			=> $this->_ee->lang->line('extension_name'),
			'error_log'				=> $this->_ee->mailchimp_model->get_error_log(),
			'hidden_fields'			=> array('file' => strtolower(substr(get_class($this), 0, -4))),
			'mailing_lists'			=> $this->_ee->mailchimp_model->get_mailing_lists(),
			'member_fields'			=> $member_fields,
			'member_field_options' 	=> $member_field_options,
			'settings'				=> $this->_ee->mailchimp_model->get_settings()
		);
		
		// Is this an AJAX request?
		if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')
		{
			$output = $this->_ee->load->view('_mailing_lists', $vars, TRUE);
			$this->_ee->output->send_ajax_response($output);
		}
		else
		{
			// Include the JavaScript.
			$this->_ee->load->library('javascript');
			
			$this->_ee->cp->add_js_script(array('package' => 'mailchimp_subscribe'));
			
			$this->_ee->javascript->set_global('mailChimp.lang', array(
				'missingApiKey' => $this->_ee->lang->line('missing_api_key')
			));
			
			$this->_ee->javascript->set_global('mailChimp.globals.ajaxUrl', str_replace(AMP, '&', BASE) .'&C=addons_extensions&M=extension_settings&file=mailchimp_subscribe');
			$this->_ee->javascript->compile();
			
			// Load the view.
			return $this->_ee->load->view('settings', $vars, TRUE);
		}
	}
	
	
	/**
	 * Updates the extension.
	 *
	 * @access	public
	 * @param	string		$current_version	The current version.
	 * @return	bool
	 */
	public function update_extension($current_version = '')
	{
		return $this->_ee->mailchimp_model->update_extension($current_version);
	}
	
	
	
	/* --------------------------------------------------------------
	 * HOOK HANDLERS
	 * ------------------------------------------------------------ */

	/**
	 * Handlers to cp_members_member_create hook.
	 *
	 * @see		http://expressionengine.com/developers/extension_hooks/cp_members_member_create/
	 * @access	public
	 * @param	string		$member_id		The ID of the newly-created member.
	 * @param	array 		$member_data	Information about the newly-created member.
	 * @return	void
	 */
	public function cp_members_member_create($member_id = '', $member_data = array())
	{
		$this->_ee->mailchimp_model->subscribe_member($member_id);
	}
	
	
	/**
	 * Handlers to cp_members_validate_members hook.
	 *
	 * @see		http://expressionengine.com/developers/extension_hooks/cp_members_validate_members/
	 * @access	public
	 * @return	void
	 */
	public function cp_members_validate_members()
	{
		if ( ! isset($_POST['action']) OR $_POST['action'] != 'activate')
		{
			return;
		}
		
		$member_ids = array();
		foreach ($_POST AS $key => $val)
		{
			if (strpos($key, 'toggle') === 0 && ! is_array($val))
			{
				$member_ids[] = $val;
			}
		}
		
		if ($member_ids)
		{
			foreach ($member_ids AS $member_id)
			{
				$this->_ee->mailchimp_model->subscribe_member($member_id);
			}
		}
	}
	
	
	/**
	 * Handles the member_member_register hook.
	 *
	 * @access	public
	 * @param 	array 	$data 	An array of data about the new member.
	 * @return 	void
	 */	
	public function member_member_register($data = array())
	{
		if ((strtolower($this->_ee->config->item('req_mbr_activation')) !== 'none')
			OR ( ! isset($data['username']))
			OR ( ! isset($data['email']))
			OR ( ! isset($data['join_date'])))
		{
			return FALSE;
		}
		
		$db_member = $this->_ee->db->select('member_id')->get_where('members', array(
			'username'	=> $data['username'],
			'email'		=> $data['email'],
			'join_date'	=> $data['join_date']
		));
		
		if ($db_member->num_rows() === 1)
		{
			$this->_ee->mailchimp_model->subscribe_member($db_member->row()->member_id);
		}
	}
	
	
	/**
	 * Handles the member_register_validate_members hook.
	 *
	 * @access	public
	 * @param	string		$member_id		The ID of the member that has just confirmed his registration.
	 * @return 	void
	 */
	public function member_register_validate_members($member_id = '')
	{
		if (strtolower($this->_ee->config->item('req_mbr_activation')) === 'email')
		{
			$this->_ee->mailchimp_model->subscribe_member($member_id);
		}
	}
	
	
	/**
	 * Handles the User module user_edit_end hook.
	 *
	 * @access	public
	 * @param 	string		$member_id			The member ID.
	 * @param 	array 		$member_data		Information about the member.
	 * @param 	array 		$custom_fields		Custom member fields.
	 * @return 	bool
	 */
	public function user_edit_end($member_id = '', $member_data = array(), $custom_fields = array())
	{
		$this->_ee->mailchimp_model->update_member_subscriptions($member_id);
		return TRUE;
	}
	
	
	/**
	 * Handles the User module user_register_end hook.
	 *
	 * @access  public
	 * @param   array   $userdata   An array of data about the new member.
	 * @param   int     $member_id  The ID of the new member.
	 * @return  array
	 */
	public function user_register_end($userdata = array(), $member_id = '')
	{
		if (strtolower($this->_ee->config->item('req_mbr_activation')) === 'none')
		{
			$this->_ee->mailchimp_model->subscribe_member($member_id);
		}
	  
		return $userdata;
	}
	
}

/* End of file		: ext.mailchimp_subscribe.php */
/* File location	: /system/expressionengine/third_party/mailchimp_subscribe/ext.mailchimp_subscribe.php */