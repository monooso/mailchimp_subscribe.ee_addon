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
	
	/**
	 * Version.
	 *
	 * Don't really want to store this in two separate files, and
	 * the model class (for example) can't instantiate an instance of
	 * the extension, because we get into an infinite instantiation
	 * loop. Nasty.
	 *
	 * @static
	 * @access	public
	 * @var		string
	 */
	public static $extension_version = '2.0.0b1';
	
	
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
	public $docs_url = 'http://experienceinternet.co.uk/software/mailchimp_subscribe/';
	
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
		$this->_ee 		=& get_instance();
		$this->version 	= Mailchimp_subscribe_ext::$extension_version;
		
		// Load our glamorous assistants.
		$this->_ee->load->helper('form');
		$this->_ee->load->library('table');
		
		// Need to explicitly set the package path, annoyingly.
		$this->_ee->load->add_package_path(PATH_THIRD .'mailchimp_subscribe/');
		$this->_ee->load->model('mailchimp_model');
		
		// Define the navigation.
		$this->_ee->cp->set_right_nav(array(
			'nav_items'		=> '#',
			'nav_settings'	=> '#'
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
		
		$this->_ee->mailchimp_model->update_settings_from_input();
		
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
		$member_fields = $this->_ee->mailchimp_model->load_member_fields();
		$member_field_options = array();

		foreach ($member_fields AS $key => $data)
		{
			$member_field_options[$key] = $data['label'];
		}
		
		// Collate the view variables.
		$vars = array(
			'action_url' 			=> 'C=addons_extensions' .AMP .'M=save_extension_settings',
			'cp_page_title'			=> $this->_ee->lang->line('extension_name'),
			'hidden_fields'			=> array('file' => strtolower(substr(get_class($this), 0, -4))),
			'js_language_strings'	=> array(
				'missingApiKey'	=> $this->_ee->lang->line('missing_api_key')
			),
			'member_fields'			=> $member_fields,
			'member_field_options' 	=> $member_field_options,
			'settings'				=> $this->_ee->mailchimp_model->load_settings()
		);
		
		// Is this an AJAX request?
		if (isset($_SERVER['HTTP_X_REQUESTED_WITH'])
			&& strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')
		{
			$vars['settings'] = $this->_ee->mailchimp_model->update_settings_from_input();
			
			try
			{
				$vars['mailing_lists'] = $this->_ee->mailchimp_model->load_mailing_lists();
				$output = $this->_ee->load->view('_mailing_lists', $vars, TRUE);
			}
			catch (MCS_Exception $exception)
			{
				$vars['error'] = array(
					'code'		=> $exception->getCode(),
					'message'	=> $exception->getMessage()
				);
				
				$output = $this->_ee->load->view('_mailing_lists_api_error', $vars, TRUE);
			}
			
			$this->_ee->output->send_ajax_response($output);
		}
		else
		{
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
		return TRUE;
	}
	
}

/* End of file		: ext.mailchimp_subscribe.php */
/* File location	: /system/expressionengine/third_party/mailchimp_subscribe/ext.mailchimp_subscribe.php */