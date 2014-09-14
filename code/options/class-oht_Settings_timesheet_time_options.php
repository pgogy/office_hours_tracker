<?php

class oht_Settings_timesheet_time_options{
	
	public function __construct() {	
		
		if ( is_admin() ) {
			add_action('admin_menu', array($this, 'menu_option'));	
			add_action('admin_enqueue_scripts', array($this, 'manage_time_js'));		
			add_action('wp_ajax_oht_time_updated', array($this, 'manage_time_ajax'));
			add_action('admin_head', array($this, 'manage_postform'));		
		}
		
	}
	
	function manage_time_ajax(){
	
		if(wp_verify_nonce($_REQUEST['nonce'], 'manage_time_js_nonce')){
		
			global $wpdb;
			$table_name = $wpdb->prefix . "oht_times";
			
			$wpdb->update( 
				$table_name, 
				array( 
					'time' => filter_var($_POST['time_value'], FILTER_VALIDATE_INT),
				), 
				array( 'id' => filter_var($_POST['time_id'], FILTER_VALIDATE_INT) ), 
				array( 
					'%d'	// value1
				), 
				array( '%d' ) 
			);
			
			echo "Updated";
			
		
		}
		
		die();
	
	}
	
	function manage_time_js() {
	
		wp_enqueue_script( 'manage_time_js', plugins_url('../../js/manage_time.js', __FILE__), array(), '1.0.0', true );
		wp_localize_script( 'manage_time_js', 'manage_time', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ), 'answerNonce' => wp_create_nonce( 'manage_time_js_nonce' ) ) );
		
	}
	
	function manage_postform(){
		
		if (!empty($_POST['oht_time_add_new'])){

			if(!wp_verify_nonce($_POST['oht_time_add_new'],'oht_time_add_new') ){
			
				print 'Sorry, your nonce did not verify.';
				exit;
				
			}else{	

				global $wpdb;
				
				$table_name = $wpdb->prefix . "oht_times";
			
				$wpdb->insert( $table_name, array( 'time' => filter_var($_POST['new_time'], FILTER_VALIDATE_INT)));
			
			}
		
		}
		
	}
	
	function config_page() {
	
		global $wpdb;
		
		$table_name = $wpdb->prefix . "oht_times";

		?>
		<div class="wrap">
			<h2>Task time management</h2>
			<h3>Add New Time</h3>
			<form method="post" action=""><?PHP
			
					wp_nonce_field('oht_time_add_new','oht_time_add_new');
			
				?><p>Time : <input name='new_time' size='40' value='Enter a new time' />
				<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />			
			</form>
			<h3>Manage existing times</h3><?PHP
			
				$times = $wpdb->get_results("SELECT id,time FROM " . $table_name . " order by time ASC");
			
				foreach($times as $time){
				
					if($time->time!=""){
				
						echo "<p>Time : <input id='" . $time->id . "_time' size='40' value='" . $time->time . "' />"; 
						echo "<button onClick='javascript:time_updated(" . $time->id . ");'>Update</button></p>";
					
					}
					
				}
			
			?>
		</div>
		<?php
		
	}
	
	function menu_option() {
	
		add_submenu_page('oht', 'Task Time Options', 'Task Time Options', 'manage_options', 'oht_time_sheet', array($this, 'config_page'));
		
	}

} 

$oht_Settings_timesheet_time_options = new oht_Settings_timesheet_time_options();
