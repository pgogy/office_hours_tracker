<?php

/*
	Plugin Name: Office Hours Tracker
	Description: Allows the site to present an Office Hours time tracker
	Version: 0.2
	Author: pgogy
	Author URI: http://www.pgogywebstuff.com
*/

require_once('code/config/class-oht_Settings_config.php');
require_once('code/config/class-oht_Settings_register_email_config.php');
require_once('code/config/class-oht_Settings_not_registered_config.php');
require_once('code/config/class-oht_Settings_reminder_email_config.php');
require_once('code/config/class-oht_Settings_register_instructions_config.php');
require_once('code/display/class-oht_Settings_timesheet_display.php');
require_once('code/display/class-oht_Settings_register_display.php');
require_once('code/options/class-oht_Settings_timesheet_time_options.php');
require_once('code/options/class-oht_Settings_timesheet_tasks_options.php');
require_once('code/user/class-oht_Register_user.php');
require_once('code/user/class-oht_Redirect_user.php');
require_once('code/user/class-oht_User_profile.php');
require_once('code/scheduled_tasks/class-oht_schedule_emails.php');
require_once('code/download/class-oht_download.php');

class OHT{

	function oht_activate(){

		global $wpdb;
		
		if(!get_option("oht_time_sheet_role")){
			add_role( 'timesheet', 'Time Sheet', array( 'read' => true ) );
			add_option("oht_time_sheet_role", TRUE);
		}
		
		if(!get_option("oht_classes_data")){

			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

			$table_name = $wpdb->prefix . "oht_classes";

			$sql = "CREATE TABLE " . $table_name . " (
				  id bigint(20) NOT NULL AUTO_INCREMENT,
				  user_id bigint(20),
				  employer varchar(200),
				  department varchar(200),
				  class_name varchar(200),
				  class_type varchar(200),
				  class_length bigint(10),
				  compensation bigint(20),
				  credits bigint(20),
				  UNIQUE KEY id(id)
				);";
			
			dbDelta($sql);
			
			$table_name = $wpdb->prefix . "oht_class_tasks";

			$sql = "CREATE TABLE " . $table_name . " (
				  id bigint(20) NOT NULL AUTO_INCREMENT,
				  class_id bigint(20),
				  task varchar(200),
				  hours varchar(200),
				  comments varchar(500),
				  time bigint(20),
				  week bigint(20),
				  UNIQUE KEY id(id)
				);";
			
			dbDelta($sql);
			
			add_option("oht_classes_data", TRUE);
			
		}
		
		if(!get_option("oht_time_data")){

			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

			$table_name = $wpdb->prefix . "oht_times";

			$sql = "CREATE TABLE " . $table_name . " (
				  id bigint(20) NOT NULL AUTO_INCREMENT,
				  time  int(10),
				  UNIQUE KEY id(id)
				);";
			
			dbDelta($sql);
			
			$wpdb->insert( $table_name, array( 'time' => 15));
			$wpdb->insert( $table_name, array( 'time' => 30));
			$wpdb->insert( $table_name, array( 'time' => 45));
			$wpdb->insert( $table_name, array( 'time' => 60));
			$wpdb->insert( $table_name, array( 'time' => 75));
			$wpdb->insert( $table_name, array( 'time' => 90));
			$wpdb->insert( $table_name, array( 'time' => 105));
			$wpdb->insert( $table_name, array( 'time' => 120));
			$wpdb->insert( $table_name, array( 'time' => 135));
			$wpdb->insert( $table_name, array( 'time' => 150));
			$wpdb->insert( $table_name, array( 'time' => 165));
			$wpdb->insert( $table_name, array( 'time' => 180));
			$wpdb->insert( $table_name, array( 'time' => 195));
			$wpdb->insert( $table_name, array( 'time' => 210));
			$wpdb->insert( $table_name, array( 'time' => 225));
			$wpdb->insert( $table_name, array( 'time' => 240));
			$wpdb->insert( $table_name, array( 'time' => 255));
			$wpdb->insert( $table_name, array( 'time' => 260));
			$wpdb->insert( $table_name, array( 'time' => 275));
			$wpdb->insert( $table_name, array( 'time' => 290));
			$wpdb->insert( $table_name, array( 'time' => 305));
			$wpdb->insert( $table_name, array( 'time' => 320));
			$wpdb->insert( $table_name, array( 'time' => 335));
			$wpdb->insert( $table_name, array( 'time' => 350));
			$wpdb->insert( $table_name, array( 'time' => 365));
			$wpdb->insert( $table_name, array( 'time' => 380));
			$wpdb->insert( $table_name, array( 'time' => 395));
			$wpdb->insert( $table_name, array( 'time' => 410));
			$wpdb->insert( $table_name, array( 'time' => 435));
			$wpdb->insert( $table_name, array( 'time' => 450));
			$wpdb->insert( $table_name, array( 'time' => 465));
			$wpdb->insert( $table_name, array( 'time' => 480));
			$wpdb->insert( $table_name, array( 'time' => 495));
			$wpdb->insert( $table_name, array( 'time' => 510));
			$wpdb->insert( $table_name, array( 'time' => 535));
			$wpdb->insert( $table_name, array( 'time' => 550));
			$wpdb->insert( $table_name, array( 'time' => 565));
			$wpdb->insert( $table_name, array( 'time' => 580));
			$wpdb->insert( $table_name, array( 'time' => 595));
			
			add_option("oht_time_data", TRUE);
			
		}
	
		if(!get_option("oht_tasks_data")){

			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

			$table_name = $wpdb->prefix . "oht_tasks";

			$sql = "CREATE TABLE " . $table_name . " (
				  id bigint(20) NOT NULL AUTO_INCREMENT,
				  task varchar(200),
				  description varchar(600),
				  UNIQUE KEY id(id)
				);";
			
			dbDelta($sql);
			
			$wpdb->insert( $table_name, array( 
												'task' => "Class Time",
												'description' => ""
											) );
			
			$wpdb->insert( $table_name, array( 
												'task' => "Preparation",
												'description' => "<p>This could include:</p>
													<ul>
														<li>Drafting syllabus, lesson plans, bibliographies etc</li>
														<li>Preparing lectures and lessons</li>
													</ul>"
											) );
											
			$wpdb->insert( $table_name, array( 
												'task' => "Time with students",
												'description' => "<p>This could include:</p>
													<ul>
														<li>Office hours</li>
														<li>Answering student emails</li>
														Attending student activities or events</li>
														<li>Performing clinical or education site visits</li>
														<li>Supervising student work, projects</li>
														<li>Writing recommendations</li>
													</ul>"
											) );
											
			$wpdb->insert( $table_name, array( 
												'task' => "Administration",
												'description' => "<p>This could include:</p>
																	<ul>
<li>Attending meetings (departmental, etc); </li>	
<li>Responding to administrator and department emails;</li>
<li>Work-related travel, such as attending student events/meeting or supervising students</li>
<li>Attending/planning student recruiting events, etc.</li>
<li>Applying and interviewing for job positions and assignments</li>
</ul>"
											) );
											
			$wpdb->insert( $table_name, array( 
												'task' => "Evaluation",
												'description' => "<p>This could include:</p>
																<ul>
																	<li>Advising students</li>
																	<li>Grading</li>
																</ul>"
											) );
											
			$wpdb->insert( $table_name, array( 
												'task' => "Research, Writing and Professional Development",
												'description' => "<p>This could include:</p>
																	<ul>
																		<li>Research and writing in your field</li>
																		<li>Attending conferences</li>
																		<li>Meeting professional development requirements.</li>
																	</ul>"
											) );

			add_option("oht_tasks_data", TRUE);
			
		}
		
	
	}
	
	function scheduled_email(){
		$oht_schedule_emails = new oht_schedule_emails();
		$oht_schedule_emails->scheduled_email();
	}
	
	function unschedule_email(){
		$timestamp = wp_next_scheduled( 'oht_reminder_send' );
		wp_unschedule_event($timestamp, 'oht_reminder_send');
	}
	
}


$oht = new OHT();

register_activation_hook( __FILE__, array($oht,'oht_activate'));
register_activation_hook(__FILE__, array($oht,'scheduled_email'));
register_deactivation_hook(__FILE__, array($oht,'unschedule_email'));