<?php

class oht_Settings_reminder_email_config{
	
	public function __construct() {	
		
		if ( is_admin() ) {			
			add_action('admin_init', array( $this, 'page_init' ) );
			add_action('admin_menu', array($this, 'menu_option'));	
		}
		
	}
	
	public function page_init(){        
			
		register_setting(
			'oht_reminder_email_settings_group', // Option group
			'oht_reminder_email_settings', // Option name
			array( $this, 'sanitize' ) // Sanitize
		);

		add_settings_section(
			'setting_section_id', // ID
			'Reminder Email Options', // Title
			array( $this, 'print_info' ), // Callback
			'oht_reminder_email' // Page
		);

		add_settings_field(
			'reminder_email_on', // ID
			'Reminder email being sent?', // Title 
			array( $this, 'email_send' ), // Callback
			'oht_reminder_email', // Page
			'setting_section_id' // Section           
		);
		
		add_settings_field(
			'reminder_email_from', // ID
			'Reminder email from field', // Title 
			array( $this, 'email_from' ), // Callback
			'oht_reminder_email', // Page
			'setting_section_id' // Section           
		);
		
		add_settings_field(
			'reminder_email_address', // ID
			'Reminder email address', // Title 
			array( $this, 'email_address' ), // Callback
			'oht_reminder_email', // Page
			'setting_section_id' // Section           
		);
		
		add_settings_field(
			'reminder_email_subject', // ID
			'Reminder email subject', // Title 
			array( $this, 'email_subject' ), // Callback
			'oht_reminder_email', // Page
			'setting_section_id' // Section           
		);
		
		add_settings_field(
			'reminder_email_body', // ID
			'Body of the reminder email', // Title 
			array( $this, 'email_body' ), // Callback
			'oht_reminder_email', // Page
			'setting_section_id' // Section           
		);
		
		add_settings_field(
			'reminder_email_report', // ID
			'Email to send the report to when the job has run', // Title 
			array( $this, 'email_report' ), // Callback
			'oht_reminder_email', // Page
			'setting_section_id' // Section           
		);
		
	}
	
	public function sanitize( $input ){
		return $input;
	}

	
	public function print_info(){
		?><p>
			Use this page to set up the reminder email to be used.
		</p><?PHP
	}
	
	public function email_from(){		
		
		$status = esc_attr($this->options['reminder_email_from']);
		
		?><input type="text" name="oht_reminder_email_settings[reminder_email_from]" value="<?PHP echo $status; ?>" /><?PHP
		
	}
	
	public function email_address(){		
		
		$status = esc_attr($this->options['reminder_email_address']);
		
		?><input type="text" name="oht_reminder_email_settings[reminder_email_address]" value="<?PHP echo $status; ?>" /><?PHP
		
	}
	
	public function email_report(){		
		
		$status = esc_attr($this->options['reminder_email_report']);
		
		?><input type="text" name="oht_reminder_email_settings[reminder_email_report]" value="<?PHP echo $status; ?>" /><?PHP
		
	}
	
	public function email_subject(){		
		
		$status = esc_attr($this->options['reminder_email_subject']);
		
		?><input type="text" name="oht_reminder_email_settings[reminder_email_subject]" value="<?PHP echo $status; ?>" /><?PHP
		
	}
	
	public function email_send(){		
		
		$status = $this->options['reminder_email_on'];
		
		if($status=="on"){
			$checked = " checked ";
		}else{
			$checked = "";
		}
		
		?><input type="checkbox" name="oht_reminder_email_settings[reminder_email_on]" <?PHP echo $checked; ?> /><?PHP
		
	}
	
	public function email_body(){
	
		$status = $this->options['reminder_email_body'];
		
		?><p>user %NAME% as a placeholder for the user's name</p><?PHP
	
		wp_editor( $status, "reminder_email_body", $settings = array(
			"textarea_name" => "oht_reminder_email_settings[reminder_email_body]",
			"rows" => 10,
			) 
		);
	
	}
	
	function config_page() {
		// Set class property
		$this->options = get_option('oht_reminder_email_settings');
		
		?>
		<div class="wrap">
			<?php screen_icon(); ?>
			<h2>Office Hours Settings</h2>           
			<form method="post" action="options.php">
			<?php
				// This prints out all hidden setting fields
				settings_fields( 'oht_reminder_email_settings_group' );   
				do_settings_sections( 'oht_reminder_email' );
				submit_button(); 
			?>
			</form>
		</div>
		<?php
	}
	
	function menu_option() {
	
		add_submenu_page('oht', 'Reminder Email', 'Reminder Email', 'manage_options', 'oht_reminder_email', array($this, 'config_page'));
		
	}

} 

$oht_Settings_reminder_email_config = new oht_Settings_reminder_email_config();
