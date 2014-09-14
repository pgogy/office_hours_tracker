<?php

class oht_Settings_register_email_config{
	
	public function __construct() {	
		
		if ( is_admin() ) {			
			add_action('admin_init', array( $this, 'page_init' ) );
			add_action('admin_menu', array($this, 'menu_option'));	
		}
		
	}
	
	public function page_init(){        
			
		register_setting(
			'oht_register_email_settings_group', // Option group
			'oht_register_email_settings', // Option name
			array( $this, 'sanitize' ) // Sanitize
		);

		add_settings_section(
			'setting_section_id', // ID
			'Register Email Options', // Title
			array( $this, 'print_info' ), // Callback
			'oht_register_email' // Page
		);

		add_settings_field(
			'register_email_from', // ID
			'Register Email from field', // Title 
			array( $this, 'email_from' ), // Callback
			'oht_register_email', // Page
			'setting_section_id' // Section           
		);
		
		add_settings_field(
			'register_email_address', // ID
			'Register Email Address', // Title 
			array( $this, 'email_address' ), // Callback
			'oht_register_email', // Page
			'setting_section_id' // Section           
		);
		
		add_settings_field(
			'register_email_subject', // ID
			'Register Email Subject', // Title 
			array( $this, 'email_subject' ), // Callback
			'oht_register_email', // Page
			'setting_section_id' // Section           
		);
		
		add_settings_field(
			'register_email_body', // ID
			'Register Email Body', // Title 
			array( $this, 'email_body' ), // Callback
			'oht_register_email', // Page
			'setting_section_id' // Section           
		);
		
	}
	
	public function sanitize( $input ){
		return $input;
	}

	
	public function print_info(){
		?><p>Use this page to customise the email you receive post-registering on the site.</p><?PHP
	}
	
	public function email_from(){		
		
		$status = esc_attr($this->options['register_email_from']);
		
		?><input type="text" name="oht_register_email_settings[register_email_from]" value="<?PHP echo $status; ?>" />
		<p>Who should the email appear to be from?</p><?PHP
		
	}
	
	public function email_address(){		
		
		$status = esc_attr($this->options['register_email_address']);
		
		?><input type="text" name="oht_register_email_settings[register_email_address]" value="<?PHP echo $status; ?>" />
		<p>Which email address shall the email come from?</p><?PHP
		
	}
	
	public function email_subject(){		
		
		$status = esc_attr($this->options['register_email_subject']);
		
		?><input type="text" name="oht_register_email_settings[register_email_subject]" value="<?PHP echo $status; ?>" /><?PHP
		
	}
	
	public function email_body(){
	
		$status = $this->options['register_email_body'];
		
		?><p>Substitute %NAME% for the first name, and %USERNAME% for user name</p><?PHP
	
		wp_editor( $status, "register_email_body", $settings = array(
			"textarea_name" => "oht_register_email_settings[register_email_body]",
			"rows" => 10,
			) 
		);
	
	}
	
	function config_page() {
		// Set class property
		$this->options = get_option('oht_register_email_settings');
		
		?>
		<div class="wrap">
			<?php screen_icon(); ?>
			<h2>Office Hours Settings</h2>           
			<form method="post" action="options.php">
			<?php
				// This prints out all hidden setting fields
				settings_fields( 'oht_register_email_settings_group' );   
				do_settings_sections( 'oht_register_email' );
				submit_button(); 
			?>
			</form>
		</div>
		<?php
	}
	
	function menu_option() {
	
		add_submenu_page('oht', 'Register Email', 'Register Email', 'manage_options', 'oht_register_email', array($this, 'config_page'));
		
	}

} 

$oht_Settings_register_email_config = new oht_Settings_register_email_config();
