<?php

class oht_download{
	
	public function __construct() {	
		
		add_action('do_feed_oht_user_data', array($this, 'user_download'));			
		add_action('do_feed_oht_sms_data', array($this, 'sms_download'));			
		
	}

	function maybeEncodeCSVField($string) {
		if(strpos($string, ',') !== false || strpos($string, '"') !== false || strpos($string, "\n") !== false) {
			$string = '"' . str_replace('"', '""', $string) . '"';
		}
		return $string;
	}
	
	function user_download() {
	
		global $wpdb;
		
		$user = wp_get_current_user();

		if(in_array("administrator", $user->roles)){
		
			$table_name = $wpdb->prefix . "users";
			$table_meta_name = $wpdb->prefix . "usermeta";

			$args = array(

				);

			$users = get_users($args);

			$csv = "";

			foreach($users as $user){

				if($user->roles[0]=="timesheet"){
					$csv .= $this->maybeEncodeCSVField($user->data->user_login) . ",";
					$csv .= $this->maybeEncodeCSVField($user->data->user_nicename) . ",";
					$csv .= $this->maybeEncodeCSVField($user->data->display_name) . ",";
					$csv .= $this->maybeEncodeCSVField($user->data->user_email) . ",";
					$csv .= $this->maybeEncodeCSVField(get_user_meta($user->ID, 'phone_number', TRUE)) . ",";
					$csv .= $this->maybeEncodeCSVField(get_user_meta($user->ID, 'update', TRUE)) . ",";
					$csv .= $this->maybeEncodeCSVField(get_user_meta($user->ID, 'address', TRUE)) . ",";
					$csv .= $this->maybeEncodeCSVField(get_user_meta($user->ID, 'address2', TRUE)) . ",";
					$csv .= $this->maybeEncodeCSVField(get_user_meta($user->ID, 'city', TRUE)) . ",";
					$csv .= $this->maybeEncodeCSVField(get_user_meta($user->ID, 'state', TRUE)) . ",";
					$csv .= $this->maybeEncodeCSVField(get_user_meta($user->ID, 'zipcode', TRUE)) . ",";
					$permission = get_user_meta($user->ID, 'update_permission', TRUE);
					if($permission == 1){
						$text = "Text permission";
					}else{
						$text = "No text permission";
					}
					$csv .= $this->maybeEncodeCSVField($text);
					$csv .= "\n";	
				}
	
			}

			header("Content-type: text/csv");
			header("Content-Disposition: attachment; filename=office_hours_user_download.csv");
			header("Pragma: no-cache");
			header("Expires: 0");

			echo $csv;
			
		}else{
		
			status_header(404);
			nocache_headers();
			include( get_404_template() );
			exit;
		
		}
			
	}

	function sms_download() {
	
		global $wpdb;
		
		$user = wp_get_current_user();

		if(in_array("administrator", $user->roles)){
		
			$table_name = $wpdb->prefix . "users";
			$table_meta_name = $wpdb->prefix . "usermeta";

			$args = array(

				);

			$users = get_users($args);

			$csv = "";

			foreach($users as $user){

				if($user->roles[0]=="timesheet"){
					$permission = get_user_meta($user->ID, 'update_permission', TRUE);
					$update = get_user_meta($user->ID, 'update', TRUE);
					if($permission == 1 && $update == "sms"){
						$csv .= $this->maybeEncodeCSVField($user->data->user_nicename) . ",";
						$csv .= $this->maybeEncodeCSVField(get_user_meta($user->ID, 'phone_number', TRUE)) . ",";
					}
					$csv .= "\n";	
				}
	
			}

			header("Content-type: text/csv");
			header("Content-Disposition: attachment; filename=office_hours_sms_user_download.csv");
			header("Pragma: no-cache");
			header("Expires: 0");

			echo $csv;
			
		}else{
		
			status_header(404);
			nocache_headers();
			include( get_404_template() );
			exit;
		
		}
			
	}
	
	
} 

$oht_download = new oht_download();
