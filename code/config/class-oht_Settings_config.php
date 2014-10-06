<?php

class oht_Settings_config{
	
	public function __construct() {	
		
		if ( is_admin() ) {			
			add_action('admin_init', array( $this, 'page_init' ) );
			add_action('admin_menu', array($this, 'menu_option'));	
		}
		
	}
	
	public function page_init(){        
			
		register_setting(
			'oht_config_settings_group', // Option group
			'oht_config_settings', // Option name
			array( $this, 'sanitize' ) // Sanitize
		);

		add_settings_section(
			'setting_section_id', // ID
			'Pages to use', // Title
			array( $this, 'print_info' ), // Callback
			'oht_config' // Page
		);

		add_settings_field(
			'register_page_id', // ID
			'Page to use for registration', // Title 
			array( $this, 'setting_register_page' ), // Callback
			'oht_config', // Page
			'setting_section_id' // Section           
		);
		
		add_settings_field(
			'data_page_id', // ID
			'Page to use for data entry (for users)', // Title 
			array( $this, 'setting_data_page' ), // Callback
			'oht_config', // Page
			'setting_section_id' // Section           
		);
		
	}
	
	public function sanitize( $input ){
		return $input;
	}

	
	public function print_info(){
		?><p>
			Use this page to set the pages used for 
			<ol>
				<li>Registration</li>
				<li>Entering the time sheet</li>	
			</ol>
		</p>
		<p>Create two pages using WordPress and then return here and set the page from the drop down lists below.</p>
		<?PHP
	}
	
	public function get_pages(){

		/*$args = array(
			'orderby'          => 'post_date',
			'order'            => 'DESC',
			'post_type'        => 'page',
			'post_status'      => 'publish',
			'suppress_filters' => true ); 
	
		return get_posts($args);*/

		global $wpdb;
		$posts = $wpdb->get_results( "SELECT * FROM " . $wpdb->prefix . "posts where post_type='page' and post_status='publish'" );
		return $posts;

	}
	
	public function setting_register_page(){		
		
		$pages = $this->get_pages();
	
		$status = esc_attr($this->options['register_page_id']);
		
		?><select id="register_page" name="oht_config_settings[register_page_id]"><?PHP
		echo "<option>Select a page</option>";

		while($post = array_pop($pages)){
			
			if($post->ID==$status){
				$checked = " selected ";
			}else{
				$checked = "";
			}
		
			echo "<option " . $checked . " value='" . $post->ID . "'>" . $post->post_title . "</option>";
		
		}
		
		echo "</select>";
		
	}
	
	public function setting_data_page(){		
		
		$pages = $this->get_pages();
	
		$status = esc_attr($this->options['data_page_id']);
		
		?><select id="data_page" name="oht_config_settings[data_page_id]"><?PHP
		echo "<option>Select a page</option>";

		while($post = array_pop($pages)){
			
			if($post->ID==$status){
				$checked = " selected ";
			}else{
				$checked = "";
			}
		
			echo "<option " . $checked . " value='" . $post->ID . "'>" . $post->post_title . "</option>";
		
		}
		
		echo "</select>";
		
	}
	
	function config_page() {
		// Set class property
		$this->options = get_option('oht_config_settings');
		
		?>
		<div class="wrap">
			<?php screen_icon(); ?>
			<h2>Office Hours Settings</h2>           
			<form method="post" action="options.php">
			<?php
				// This prints out all hidden setting fields
				settings_fields( 'oht_config_settings_group' );   
				do_settings_sections( 'oht_config' );
				submit_button(); 
			?>
			</form>
		</div>
		<?php
	}
		
	function main_oht_menu(){
		echo "<h1>Welcome to the 'Office Hours Tracker'</h1>";
		echo "<p><a href='" . admin_url("admin.php?page=oht_config") . "'><button>Configuration</button></a> - Use this to choose which pages are used for Registration and the time sheet.</p>";
		echo "<p><a href='" . admin_url("admin.php?page=oht_register_email") . "'><button>Register Email</button></a> - Use the to change settings for email sent on registration</p>";
		echo "<p><a href='" . admin_url("admin.php?page=oht_register_instruction") . "'><button>Post registration message</button></a> - Use the to set the message displayed post registration</p>";
		echo "<p><a href='" . admin_url("admin.php?page=oht_not_registered") . "'><button>Not registered page</button></a> - Use this page to set the message for when some one accesses the time sheet isn't logged in</p>";
		echo "<p><a href='" . admin_url("admin.php?page=oht_reminder_email") . "'><button>Reminder email page</button></a> - Use this page to set the email which is used to send reminders</p>";
		echo "<p><a href='" . admin_url("admin.php?page=oht_time_sheet") . "'><button>Add new time page</button></a> - Use the to manage the times displayed for each task</p>";
		echo "<p><a href='" . admin_url("admin.php?page=oht_tasks_sheet") . "'><button>Add new task</button></a> - Use this page to add new tasks and manage new ones</p>";		
		echo "<p><a href='" . site_url("?feed=oht_user_data") . "'><button>Download User Contact data</button></a></p>";
		echo "<p><a href='" . site_url("?feed=oht_sms_data") . "'><button>Download Mobile SMS data</button></a></p>";
		echo "<p><a href='" . site_url("?feed=oht_class_data") . "'><button>Download class data</button></a></p>";
		echo "<p><a href='" . site_url("?feed=oht_tasks_data") . "'><button>Download task data</button></a></p>";
		
		$args = array(
				'role'         => 'timesheet'
			 );
			 
		$users = get_users( $args );	
		
		echo "<p>Next email will be sent at " . date("m/d/Y G:i:s", (wp_next_scheduled( 'oht_reminder_send' ) - (3600 * 4))) . "</p>";
		echo "<p>That is in " . $this->time2string((wp_next_scheduled( 'oht_reminder_send' ) - time()));
		echo "</p>";
		echo "<p>" . count($users) . " people have registered</p>";
	}
	
	function time2string($time) {
		$d = floor($time/86400);
		$_d = ($d < 10 ? '0' : '').$d;

		$h = floor(($time-$d*86400)/3600);
		$_h = ($h < 10 ? '0' : '').$h;

		$m = floor(($time-($d*86400+$h*3600))/60);
		$_m = ($m < 10 ? '0' : '').$m;

		$s = $time-($d*86400+$h*3600+$m*60);
		$_s = ($s < 10 ? '0' : '').$s;

		$time_str = $_d.':'.$_h.':'.$_m.':'.$_s;

		return $time_str;
	}
	
	function menu_option() {
	
		add_menu_page("Office Hours Tracker", "Office Hours Tracker", "manage_options", "oht", array($this, "main_oht_menu"));	
		add_submenu_page('oht', 'Office Hours Config', 'Office Hours Config', 'manage_options', 'oht_config', array($this, 'config_page'));
		
	}

} 

$oht_Settings_config = new oht_Settings_config();
