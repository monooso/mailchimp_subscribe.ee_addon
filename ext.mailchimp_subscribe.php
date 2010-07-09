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
	 * Version.
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
		
		// Update the settings with any input data.
		$this->_ee->mailchimp_model->update_settings_from_input();
		
		// Save the settings.
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
		// Define the navigation.
		$base_url = BASE .AMP .'C=addons_extensions' .AMP .'M=extension_settings' .AMP .'file=mailchimp_subscribe' .AMP .'tab=';
		
		$this->_ee->cp->set_right_nav(array(
			'nav_settings'		=> $base_url .'settings',
			'nav_unsubscribe'	=> $base_url .'unsubscribe_urls',
			'nav_error_log'		=> $base_url .'error_log'
		));
		
		switch ($this->_ee->input->get('tab'))
		{
			case 'error_log':
				return $this->_display_error_log();
				break;
				
			case 'unsubscribe_urls':
				return $this->_display_unsubscribe_urls();
				break;
				
			default:
				return $this->_display_settings_form();
				break;
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
	 * Handles the cp_members_member_create hook.
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
	 * Handles the cp_members_validate_members hook.
	 *
	 * @see		http://expressionengine.com/developers/extension_hooks/cp_members_validate_members/
	 * @access	public
	 * @return	void
	 */
	public function cp_members_validate_members()
	{
		if ($this->_ee->input->post('action') != 'activate'
			OR ! is_array($this->_ee->input->post('toggle')))
		{
			return;
		}
		
		$member_ids = $this->_ee->input->post('toggle');
		
		foreach ($member_ids AS $member_id)
		{
			$this->_ee->mailchimp_model->subscribe_member($member_id);
		}
	}
	
	
	/**
	 * Handles the member_member_register hook.
	 *
	 * @access	public
	 * @param 	array 	$data 	An array of data about the new member.
	 * @return 	void
	 */	
	public function member_member_register(Array $data = array())
	{
		if ((strtolower($this->_ee->config->item('req_mbr_activation')) !== 'none')
			OR ( ! isset($data['username']))
			OR ( ! isset($data['email']))
			OR ( ! isset($data['join_date'])))
		{
			return FALSE;
		}
		
		$members = $this->_ee->mailchimp_model->get_members(array(
			'username'	=> $data['username'],
			'email'		=> $data['email'],
			'join_date'	=> $data['join_date']
		));
		
		if (count($members) === 1)
		{
			$this->_ee->mailchimp_model->subscribe_member($members[0]['member_id']);
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
	 * @param   array   	$userdata   	User class object.
	 * @param   int     	$member_id		The ID of the new member.
	 * @return  array
	 */
	public function user_register_end($userdata = NULL, $member_id = '')
	{
		if ($member_id && strtolower($this->_ee->config->item('req_mbr_activation')) === 'none')
		{
			$this->_ee->mailchimp_model->subscribe_member($member_id);
		}
	  
		return $userdata;
	}
	
	
	
	/* --------------------------------------------------------------
	 * PRIVATE METHODS
	 * ------------------------------------------------------------ */
	
	/**
	 * Displays the error log.
	 *
	 * @access	private
	 * @return	string
	 */
	private function _display_error_log()
	{
		// Collate the view variables.
		$vars = array(
			'cp_page_title'	=> $this->_ee->lang->line('extension_name'),
			'error_log'		=> $this->_ee->mailchimp_model->get_error_log(),
		);
			
		// Load the view.
		return $this->_ee->load->view('error_log', $vars, TRUE);
	}
	
	
	/**
	 * Displays the settings form.
	 *
	 * @access	private
	 * @return	string
	 */
	private function _display_settings_form()
	{
		// Load the member fields.
		$member_fields = $this->_ee->mailchimp_model->get_member_fields();
		$cleaned_member_fields = array('' => $this->_ee->lang->line('trigger_field_hint'));

		foreach ($member_fields AS $key => $data)
		{
			$cleaned_member_fields[$key] = $data['label'];
		}
		
		// Collate the view variables.
		$vars = array(
			'action_url' 	=> 'C=addons_extensions' .AMP .'M=save_extension_settings',
			'cleaned_member_fields' => $cleaned_member_fields,
			'cp_page_title'	=> $this->_ee->lang->line('extension_name'),
			'hidden_fields'	=> array('file' => strtolower(substr(get_class($this), 0, -4))),
			'member_fields'	=> $member_fields,
			'view_settings'	=> $this->_ee->mailchimp_model->get_view_settings()
		);
		
		// Is this an AJAX request?
		if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')
		{
			// Update the settings with any input data.
			$this->_ee->mailchimp_model->update_settings_from_input();
			
			// Update the view settings.
			$vars['view_settings'] = $this->_ee->mailchimp_model->get_view_settings();
			
			$output = $this->_ee->load->view('_mailing_lists', $vars, TRUE);
			$this->_ee->output->send_ajax_response($output);
		}
		else
		{
			// Retrieve the theme folder URL.
			$theme_url = $this->_ee->mailchimp_model->get_theme_url();
			
			// Include the JavaScript.
			$this->_ee->load->library('javascript');
			
			// Set the global variables.
			$this->_ee->javascript->set_global('mailChimp.lang', array(
				'missingApiKey' => $this->_ee->lang->line('missing_api_key')
			));
			
			$this->_ee->javascript->set_global('mailChimp.memberFields', $this->_ee->javascript->generate_json($member_fields));
			$this->_ee->javascript->set_global('mailChimp.globals.ajaxUrl', str_replace(AMP, '&', BASE) .'&C=addons_extensions&M=extension_settings&file=mailchimp_subscribe');
			
			// Include the main JS file.
			$this->_ee->cp->add_to_foot('<script type="text/javascript" src="' .$theme_url .'js/cp.js"></script>');
			
			$this->_ee->javascript->compile();
			
			// Include the CSS.
			$this->_ee->cp->add_to_foot('<link media="screen, projection" rel="stylesheet" type="text/css" href="' .$theme_url .'css/cp.css" />');
			
			// Load the view.
			return $this->_ee->load->view('settings', $vars, TRUE);
		}
	}
	
	
	/**
	 * Displays the unsubscribe URLs.
	 *
	 * @access	private
	 * @return	string
	 */
	private function _display_unsubscribe_urls()
	{
		// Collate the view variables.
		$vars = array(
			'cp_page_title' => $this->_ee->lang->line('extension_name'),
			'view_settings'	=> $this->_ee->mailchimp_model->get_view_settings()
		);
			
		// Load the view.
		return $this->_ee->load->view('unsubscribe_urls', $vars, TRUE);
	}
	
}

/* End of file		: ext.mailchimp_subscribe.php */
/* File location	: /system/expressionengine/third_party/mailchimp_subscribe/ext.mailchimp_subscribe.php */