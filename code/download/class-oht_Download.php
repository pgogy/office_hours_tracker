<?php

class oht_download{
	
	public function __construct() {	
		
		add_action('do_feed_oht_user_data', array($this, 'user_download'));			
		add_action('do_feed_oht_sms_data', array($this, 'sms_download'));
		add_action('do_feed_oht_class_data', array($this, 'data_download'));	
		add_action('do_feed_oht_tasks_data', array($this, 'tasks_download'));			
		
	}

	function maybeEncodeCSVField($string) {
		if(strpos($string, ',') !== false || strpos($string, '"') !== false || strpos($string, "\n") !== false) {
			$string = '"' . str_replace('"', '""', $string) . '"';
		}
		return $string;
	}

	function tasks_download() {
	
		global $wpdb;
		
		$user = wp_get_current_user();

		if(in_array("administrator", $user->roles)){

			$table_name = $wpdb->prefix . "oht_classes";
			$table_name_inner = $wpdb->prefix . "oht_class_tasks";

			$args = array(

				);

			$users = get_users($args);

			$csv = "user login, display name, class id, task, hours, comments, week\n";

			foreach($users as $user){

				if($user->roles[0]=="timesheet"){

					$pre_classes = "";

					$pre_classes .= $this->maybeEncodeCSVField($user->data->user_login) . ",";
					$pre_classes .= $this->maybeEncodeCSVField($user->data->user_nicename) . ",";

					$classes = $wpdb->get_results( 
						$wpdb->prepare( 
							"select * FROM " . $table_name . "
							WHERE user_id = %d",
							filter_var($user->data->ID, FILTER_VALIDATE_INT)
						)
					);

					foreach($classes as $class){

						$tasks = $wpdb->get_results( 
							$wpdb->prepare( 
								"select * FROM " . $table_name_inner . "
								WHERE class_id = %d",
								filter_var($class->id, FILTER_VALIDATE_INT)
							)
						);

						foreach($tasks as $task){
							$class_csv = "";
							$class_csv .= $this->maybeEncodeCSVField($task->class_id) . ",";
							$class_csv .= $this->maybeEncodeCSVField(trim($task->task)) . ",";
							$class_csv .= $this->maybeEncodeCSVField($task->hours) . ",";
							$class_csv .= $this->maybeEncodeCSVField($task->comments) . ",";
							$class_csv .= $this->maybeEncodeCSVField($task->week);
							$csv .= $pre_classes . $class_csv . "\n";
						}

					}

				}
	
			}

			header("Content-type: text/csv");
			header("Content-Disposition: attachment; filename=office_hours_tasks_download.csv");
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

	function data_download() {
	
		global $wpdb;
		
		$user = wp_get_current_user();

		if(in_array("administrator", $user->roles)){

			$table_name = $wpdb->prefix . "oht_classes";

			$args = array(

				);

			$users = get_users($args);

			$csv = "login,name,display name,class id,employer,department,class name,delivery,length,compensation,credits\n";

			foreach($users as $user){

				if($user->roles[0]=="timesheet"){

					$pre_classes = "";

					$pre_classes .= $this->maybeEncodeCSVField($user->data->user_login) . ",";
					$pre_classes .= $this->maybeEncodeCSVField($user->data->user_nicename) . ",";

					$classes = $wpdb->get_results( 
						$wpdb->prepare( 
							"select * FROM " . $table_name . "
							WHERE user_id = %d",
							filter_var($user->data->ID, FILTER_VALIDATE_INT)
						)
					);

					foreach($classes as $class){
						$class_csv = "";
						$class_csv .= $this->maybeEncodeCSVField($class->id) . ",";
						$class_csv .= $this->maybeEncodeCSVField($class->employer) . ",";
						$class_csv .= $this->maybeEncodeCSVField($class->department) . ",";
						$class_csv .= $this->maybeEncodeCSVField($class->class_name) . ",";
						$class_csv .= $this->maybeEncodeCSVField($class->class_type) . ",";
						$class_csv .= $this->maybeEncodeCSVField($class->class_length) . ",";
						$class_csv .= $this->maybeEncodeCSVField($class->compensation) . ",";
						$class_csv .= $this->maybeEncodeCSVField($class->credits);
						$csv .= $pre_classes . $class_csv . "\n";
					}

				}
	
			}

			header("Content-type: text/csv");
			header("Content-Disposition: attachment; filename=office_hours_class_download.csv");
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

	
	function user_download() {
	
		global $wpdb;
		
		$user = wp_get_current_user();

		if(in_array("administrator", $user->roles)){
		
			$table_name = $wpdb->prefix . "users";
			$table_meta_name = $wpdb->prefix . "usermeta";

			$args = array(

				);

			$users = get_users($args);

			$csv = "login,name,display name,email,phone number, update preference, address 1, address 2, city, state, zipcode, update permission\n";

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

			$csv = "display name, phone number\n";

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
