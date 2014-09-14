<?php

class oht_Settings_taskssheet_tasks_options{
	
	public function __construct() {	
		
		if ( is_admin() ) {
			add_action('admin_menu', array($this, 'menu_option'));	
			add_action('admin_enqueue_scripts', array($this, 'manage_tasks_js'));		
			add_action('wp_ajax_oht_task_updated', array($this, 'manage_tasks_ajax'));
			add_action('admin_head', array($this, 'manage_postform'));		
		}
		
	}
	
	function manage_tasks_ajax(){
	
		if(wp_verify_nonce($_REQUEST['nonce'], 'manage_tasks_js_nonce')){
		
			global $wpdb;
			$table_name = $wpdb->prefix . "oht_tasks";
			
			$wpdb->update( 
				$table_name, 
				array( 
					'task' => filter_var($_POST['task_value'], FILTER_SANITIZE_STRING),
					'description' => $_POST['task_description']
				), 
				array( 'id' => filter_var($_POST['task_id'], FILTER_VALIDATE_INT) ), 
				array( 
					'%s',	// value1
					'%s'	// value2
				), 
				array( '%d' ) 
			);
			
			echo "Updated";
			
		
		}
		
		die();
	
	}
	
	function manage_tasks_js() {
	
		wp_enqueue_script( 'manage_tasks_js', plugins_url('../../js/oht_manage_tasks.js', __FILE__), array(), '1.0.0', true );
		wp_localize_script( 'manage_tasks_js', 'manage_task', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ), 'answerNonce' => wp_create_nonce( 'manage_tasks_js_nonce' ) ) );
		
	}
	
	function manage_postform(){
		
		if (!empty($_POST['oht_tasks_add_new'])){

			if(!wp_verify_nonce($_POST['oht_tasks_add_new'],'oht_tasks_add_new') ){
			
				print 'Sorry, your nonce did not verify.';
				exit;
				
			}else{	

				global $wpdb;
				
				$table_name = $wpdb->prefix . "oht_tasks";
			
				$wpdb->insert( $table_name, array( 
													'task' => filter_var($_POST['new_task'], FILTER_SANITIZE_STRING),
													'description' => $_POST['new_description']
												)
							 );
			
			}
		
		}
		
	}
	
	function config_page() {
	
		global $wpdb;
		
		$table_name = $wpdb->prefix . "oht_tasks";

		?>
		<div class="wrap">
			<h2>Tasks Management</h2>
			<h3>Add New tasks</h3>
			<form method="post" action=""><?PHP
			
					wp_nonce_field('oht_tasks_add_new','oht_tasks_add_new');
			
				?><p>Tasks : <input name='new_task' size='40' value='Enter a new task' /></p>
				<p>Description for task</p>
				<?PHP
				
					wp_editor( "Enter task description here", "new_description", $settings = array(
							"textarea_name" => "new_description",
							"rows" => 10,
							) 
						);
				
				?>
				<br />
				<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />			
			</form>
			<h3>Manage existing tasks</h3><?PHP
			
				$tasks = $wpdb->get_results("SELECT id,task,description FROM " . $table_name . " order by task ASC");
			
				foreach($tasks as $task){
				
					if($task->task!=""){
				
						echo "<p>Task : <input id='" . $task->id . "_task' size='40' value='" . $task->task . "' /></p>"; 
						echo "<p>Description for task</p>";
						
						wp_editor( $task->description, $task->id . "_description", $settings = array(
							"textarea_name" => $task->id . "_description",
							"rows" => 10,
							) 
						);
						
						echo "<p><button onClick='javascript:task_updated(" . $task->id . ");'>Update</button></p>";
					
					}
					
				}
			
			?>
		</div>
		<?php
		
	}
	
	function menu_option() {
	
		add_submenu_page('oht', 'Tasks options', 'Tasks options', 'manage_options', 'oht_tasks_sheet', array($this, 'config_page'));
		
	}

} 

$oht_Settings_taskssheet_tasks_options = new oht_Settings_taskssheet_tasks_options();
