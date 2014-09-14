<?php

class oht_schedule_emails{
	
	public function __construct() {		
		add_filter( 'cron_schedules', array($this, 'add_weekly_schedule'));
		add_action( 'init', array($this, 'weekly_schedule'));
		add_action( 'oht_reminder_send', array($this, 'oht_send_reminder_emails_out'));
	}
	
	function scheduled_email(){
	
		$timestamp = wp_next_scheduled('oht_reminder_send');
	
		if( $timestamp == false ){
		
			if(!wp_schedule_event(1411052400, 'weekly', 'oht_reminder_send')){
				
			}else{
				
			}
			
		}
			
	}
	
	function oht_send_weekly_emails(){
	
		$this->oht_send_weekly_email();
	
	}
	
	function weekly_schedule(){
		 $timestamp = wp_next_scheduled( 'oht_send_timesheet_reminder' );
	}
	
	function add_weekly_schedule( $schedules ) {
	
		$schedules['weekly'] = array(
			'interval' => 60 * 60 * 24 * 7, 
			'display' => __( 'weekly', 'office-hours-trackers' )
		);
		
		return $schedules;
	
	}
	
	function oht_send_reminder_emails_out(){

		$data_email = get_option("oht_reminder_email_settings");
		
		if($data_email['reminder_email_on'] == "on"){

			$args = array(
				'role'         => 'timesheet'
			 );
			 
			$report = "Email sent to<br /><br />";
		
			$users = get_users( $args );
			
			add_filter( 'wp_mail_content_type', array($this,'set_html_content_type') );	
			
			foreach($users as $user){
			
				$update = get_user_meta($user->data->ID, "update", true);
				
				if($update == "email"){
				
					$first_name = get_user_meta($user->data->ID, "first_name", true);
					$email = $user->data->user_email;
			
					$headers = 'From: ' . $data_email['reminder_email_from'] . ' <' . $data_email['reminder_email_address'] . '>' . "\r\n";

					$replace_message = str_replace("%NAME%", $first_name, $data_email['reminder_email_body']);

					$html_message = str_replace("\n", "<br />", $replace_message);
			
					wp_mail( $email, $data_email['reminder_email_subject'], $html_message, $headers );	
				
					$report .= $email . "<br />";
					
				}
			
			}
			
			wp_mail($data_email['reminder_email_report'], "Reminder Email Report", $report, $headers );	
			
			remove_filter( 'wp_mail_content_type', array($this, 'set_html_content_type') );
			
		}
	
	}

	function set_html_content_type() {
		return 'text/html';
	}
	
} 

$oht_schedule_emails = new oht_schedule_emails();
