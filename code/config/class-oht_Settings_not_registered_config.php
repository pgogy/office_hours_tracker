<?php

class oht_Settings_not_registered_config{
	
	public function __construct() {	
		
		if ( is_admin() ) {			
			add_action('admin_init', array( $this, 'page_init' ) );
			add_action('admin_menu', array($this, 'menu_option'));	
		}
		
	}
	
	public function page_init(){        
			
		register_setting(
			'oht_not_registered_settings_group', // Option group
			'oht_not_registered_settings', // Option name
			array( $this, 'sanitize' ) // Sanitize
		);

		add_settings_section(
			'setting_section_id', // ID
			'Not registered display option', // Title
			array( $this, 'print_info' ), // Callback
			'oht_not_registered' // Page
		);
		
		add_settings_field(
			'not_registered', // ID
			'Not Registered Message', // Title 
			array( $this, 'not_registered' ), // Callback
			'oht_not_registered', // Page
			'setting_section_id' // Section           
		);
		
	}
	
	public function sanitize( $input ){
		return $input;
	}

	
	public function print_info(){
		?><p>Use this page to set the message you see when visiting the data entry page and not being registered.</p><?PHP
	}
	
	public function not_registered(){
	
		$status = $this->options['not_registered'];
	
		wp_editor( $status, "not_registered", $settings = array(
			"textarea_name" => "oht_not_registered_settings[not_registered]",
			"rows" => 10,
			) 
		);
	
	}
	
	function config_page() {
		// Set class property
		$this->options = get_option('oht_not_registered_settings');
		
		?>
		<div class="wrap">
			<?php screen_icon(); ?>
			<h2>Office Hours Settings</h2>           
			<form method="post" action="options.php">
			<?php
				// This prints out all hidden setting fields
				settings_fields( 'oht_not_registered_settings_group' );   
				do_settings_sections( 'oht_not_registered' );
				submit_button(); 
			?>
			</form>
		</div>
		<?php
	}
	
	function menu_option() {
	
		add_submenu_page('oht', 'Not registered', 'Not registered', 'manage_options', 'oht_not_registered', array($this, 'config_page'));
		
	}

} 

$oht_Settings_not_registered_config = new oht_Settings_not_registered_config();
