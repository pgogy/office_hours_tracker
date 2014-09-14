<?php

class oht_Register_user{
	
	public function __construct() {			
		add_action( 'user_register', array($this, 'oht_registration_save'));
	}

	function oht_registration_save( $user_id ) {

		if ( isset( $_POST['first_name'] ) && isset( $_POST['family_name'] ) ){
			update_user_meta($user_id, 'phone_number', filter_var($_POST['number'], FILTER_SANITIZE_STRING));
			update_user_meta($user_id, 'address', filter_var($_POST['address'], FILTER_SANITIZE_STRING));
			update_user_meta($user_id, 'update', filter_var($_POST['update'], FILTER_SANITIZE_STRING));
			if($_POST['sms_agree']=="on"){
				update_user_meta($user_id, 'update_permission', true);
			}
			update_user_meta($user_id, 'address2', filter_var($_POST['address2'], FILTER_SANITIZE_STRING));
			update_user_meta($user_id, 'city', filter_var($_POST['city'], FILTER_SANITIZE_STRING));
			update_user_meta($user_id, 'state',filter_var($_POST['state'], FILTER_SANITIZE_STRING) );
			update_user_meta($user_id, 'zipcode', filter_var($_POST['zipcode'], FILTER_SANITIZE_STRING));
		}

	}
	
	function register_user(){

		$data = get_option("oht_register_instruction_settings");
		$data_email = get_option("oht_register_email_settings");
		
		$userdata = array(
			'user_login'  =>  $_POST['user_name'],
			'first_name'  =>  $_POST['first_name'],
			'last_name'  =>  $_POST['family_name'],
			'user_nicename'  =>  $_POST['first_name'] . " " . $_POST['family_name'],
			'user_email'  =>  $_POST['email'],
			'user_pass'   =>  $_POST['password'],
			'role'	  =>  "timesheet",
		);
	
		$user_id = wp_insert_user( $userdata ) ;
		
		if( !is_wp_error($user_id) ) {		
		
			$headers = 'From: ' . $data_email['register_email_from'] . ' <' . $data_email['register_email_address'] . '>' . "\r\n";

			add_filter( 'wp_mail_content_type', array($this,'set_html_content_type') );	

			$replace_message = str_replace("%NAME%", filter_var($_POST['first_name'], FILTER_SANITIZE_STRING), str_replace("%USERNAME%", filter_var($_POST['user_name'], FILTER_SANITIZE_STRING), $data_email['register_email_body']));

			$html_message = str_replace("\n", "<br />", $replace_message);
	
			wp_mail( $_POST['email'], $data_email['register_email_subject'], $html_message, $headers );	

			remove_filter( 'wp_mail_content_type', array($this, 'set_html_content_type') );
	
			echo '<p>Your username is ' . filter_var($_POST['user_name'], FILTER_SANITIZE_STRING) . '</p><p>' . $data['register_instruction'] . '</p>';

			return true;

		}else{
			$error_string = $user_id->get_error_message();
			
			echo '<div id="message" class="error"><p>' . $error_string . '</p></div>';
			
			return $false;
		}
		
	}

	function set_html_content_type() {
		return 'text/html';
	}
	
} 

$oht_Register_user = new oht_Register_user();
