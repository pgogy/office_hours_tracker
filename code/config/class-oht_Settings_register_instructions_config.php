<?php

class oht_Settings_register_instructions_config{
	
	public function __construct() {	
		
		if ( is_admin() ) {			
			add_action('admin_init', array( $this, 'page_init' ) );
			add_action('admin_menu', array($this, 'menu_option'));	
		}
		
	}
	
	public function page_init(){        
			
		register_setting(
			'oht_register_instruction_settings_group', // Option group
			'oht_register_instruction_settings', // Option name
			array( $this, 'sanitize' ) // Sanitize
		);

		add_settings_section(
			'setting_section_id', // ID
			'Post Registration Instructions', // Title
			array( $this, 'print_info' ), // Callback
			'oht_register_instruction' // Page
		);
		
		add_settings_field(
			'register_instruction', // ID
			'Instructions shown Post Registering', // Title 
			array( $this, 'email_body' ), // Callback
			'oht_register_instruction', // Page
			'setting_section_id' // Section           
		);
		
	}
	
	public function sanitize( $input ){
		return $input;
	}

	
	public function print_info(){
		?><p>
			Use this page to set the message shown after successfully registering.
		</p><?PHP
	}
	
	public function email_body(){
	
		$status = $this->options['register_instruction'];
	
		wp_editor( $status, "register_instruction", $settings = array(
			"textarea_name" => "oht_register_instruction_settings[register_instruction]",
			"rows" => 10,
			) 
		);
	
	}
	
	function config_page() {
		// Set class property
		$this->options = get_option('oht_register_instruction_settings');
		
		?>
		<div class="wrap">
			<?php screen_icon(); ?>
			<h2>Office Hours Settings</h2>           
			<form method="post" action="options.php">
			<?php
				// This prints out all hidden setting fields
				settings_fields( 'oht_register_instruction_settings_group' );   
				do_settings_sections( 'oht_register_instruction' );
				submit_button(); 
			?>
			</form>
		</div>
		<?php
	}
	
	function menu_option() {
	
		add_submenu_page('oht', 'Post Register Instructions', 'Post Register Instructions', 'manage_options', 'oht_register_instruction', array($this, 'config_page'));
		
	}

} 

$oht_Settings_register_instructions_config = new oht_Settings_register_instructions_config();
