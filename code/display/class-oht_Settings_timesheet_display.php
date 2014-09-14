<?php

class oht_Settings_timesheet_display{
	
	public function __construct() {	
		
		add_filter('the_content', array($this, 'display'));	
		add_action("wp_enqueue_scripts", array($this,"scripts_styles"));
		add_action("wp_ajax_oht_update_class", array($this,"update_class"));
		add_action("wp_ajax_oht_delete_task", array($this,"delete_task"));
		
	}
	
	function update_class(){
		
		global $wpdb, $current_user;
		
		if(wp_verify_nonce($_REQUEST['nonce'], 'oht_data_js_nonce')){
			
			$table_name = $wpdb->prefix . "oht_classes";
			
			$wpdb->update( 
				$table_name, 
				array( 
					'employer' => stripslashes(filter_var($_POST['inst'], FILTER_SANITIZE_STRING)),
					'department' => stripslashes(filter_var($_POST['department'], FILTER_SANITIZE_STRING)),
					'compensation' => stripslashes(str_replace("$","",str_replace(",","",filter_var($_POST['compensation'], FILTER_SANITIZE_STRING)))),
					'class_length' => stripslashes(filter_var($_POST['length'], FILTER_SANITIZE_STRING)),
					'class_type' => stripslashes(filter_var($_POST['type'], FILTER_SANITIZE_STRING)),
					'credits' => stripslashes(filter_var($_POST['credit'], FILTER_SANITIZE_STRING)),
					
				), 
				array( 'id' => $_POST['class'] ), 
				array( 
					'%s',	// value1
					'%s',	// value1
					'%s',	// value1
					'%s',	// value1
					'%s',	// value1
					'%s'	// value1
				), 
				array( '%d' ) 
			);
			
			echo "Class updated";
										
		}
									
		die();
	}
	
	function delete_task(){
		
		global $wpdb;
		
		if(wp_verify_nonce($_REQUEST['nonce'], 'oht_data_js_nonce')){
			
			$table_name = $wpdb->prefix . "oht_class_tasks";
			
			$wpdb->query( 
				$wpdb->prepare( 
					"DELETE FROM " . $table_name . "
					WHERE id = %d",
					filter_var($_POST['task_id'], FILTER_VALIDATE_INT)
					)
			);
			
			$tasks = $wpdb->get_results( 
				$wpdb->prepare( 
					"select sum(hours) as total from $table_name WHERE week = %d and class_id= %d", filter_var($_POST['week_id'], FILTER_VALIDATE_INT), filter_var($_POST['class_id'], FILTER_VALIDATE_INT)
				)
			);
			
			echo $tasks[0]->total;
										
		}
									
		die();
	}
	
	function scripts_styles(){
		global $post;
		$data = get_option('oht_config_settings');
		if($data['data_page_id']==$post->ID){
			wp_register_style( 'oht_data_css', plugins_url( '../../css/oht_data.css', __FILE__ ) );
			wp_enqueue_style( 'oht_data_css' );
			wp_register_script( 'oht_data_js', plugins_url( '../../js/oht_data.js', __FILE__ ) );
			wp_localize_script( 'oht_data_js', 'oht_data', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ), 'answerNonce' => wp_create_nonce( 'oht_data_js_nonce' ) ) );
			wp_enqueue_script( 'oht_data_js' );
		}
	}
	
	public function display($content){
	
		global $post, $current_user;
		$data = get_option('oht_config_settings');
		
		if($data['data_page_id']==$post->ID){
		
			if($current_user->data->ID==0){
				$register_data = get_option('oht_not_registered_settings');
				echo $register_data['not_registered'];
				return "";
			}
			
			echo $content;
		
			if(!isset($_GET['class_id'])){
				
				if ( isset( $current_user->roles ) && is_array( $current_user->roles ) ) {
					if ( in_array( 'timesheet', $current_user->roles ) ) {
						?>Hello, <?PHP 
						echo get_user_meta( $current_user->ID, "first_name", TRUE );
						?>.<?PHP
					}else{
						?>Hello, <?PHP 
						echo get_user_meta( $current_user->ID, "first_name", TRUE );
						?>.<?PHP
					}
				
				}
			
			}
			
			global $wpdb;
			
			if(!isset($_GET['class_id'])){
			
				$table_name = $wpdb->prefix . "oht_classes";
				
				$classes = $wpdb->get_results( 
					$wpdb->prepare( 
						"select * from $table_name WHERE user_id = %d", $current_user->ID
						)
				);
				
				if(count($classes) == 0){
					if(isset($_POST['new_class'])){
						if($_POST['new_class'] == 'new_class'){
							$this->add_class_to_db();
							$classes = $wpdb->get_results( 
								$wpdb->prepare( 
									"select * from $table_name WHERE user_id = %d", $current_user->ID
									)
							);
							$this->show_classes($classes);
							$this->add_new_class();
						}
					}else{
						$this->add_first_class();
					}
				}else{
					if(isset($_POST['add_new_class'])){
						if($_POST['add_new_class'] == 'add_new_class'){
							$this->add_class_to_db();
						}
					}
					$classes = $wpdb->get_results( 
					$wpdb->prepare( 
						"select * from $table_name WHERE user_id = %d", $current_user->ID
						)
					);
					$this->show_classes($classes);
					$this->add_new_class();
				}
				
			}else{
			
				
				$data = get_option('oht_config_settings');
				$some_url = get_permalink($data['data_page_id']);
			
				echo "<p><a href='" . $some_url . "'><button id='oht_back_button'>Back to all classes</button></a></p>";
				
				$this->add_task($_GET['class_id']);
						
				$table_name = $wpdb->prefix . "oht_class_tasks";
		
				$tasks = $wpdb->get_results( 
					$wpdb->prepare( 
						"select * from $table_name WHERE class_id = %d order by week ASC", filter_var($_GET['class_id'], FILTER_VALIDATE_INT)
						)
				);
		
				echo "<div class='all_tasks'>";
		
				$this->add_new_task($_GET['class_id']);
		
				$this->list_tasks($tasks, $_GET['class_id']);				
			
				echo "</div>";
			
			}
	
		}else{
			return $content;
		}
	
	}
	
	function get_class($class){
	
		global $wpdb, $current_user;
		$class_table = $wpdb->prefix . "oht_classes";
		$dept_table = $wpdb->prefix . "oht_departments";
		$inst_table = $wpdb->prefix . "oht_institutions";
		
		$class = $wpdb->get_results( 
			$wpdb->prepare( 
				"select * from $class_table c WHERE user_id = %d and c.id = %d", $current_user->ID, filter_var($class, FILTER_VALIDATE_INT)
				)
		);
		
		return $class;
	}
	
	function add_task($class_id){
	
		global $wpdb;			

		if(isset($_POST['oht_add_new_task'])){
			if($_POST['oht_add_new_task']=="add_new_task"){			
						
				if(!wp_verify_nonce($_POST['add_new_task'],'oht_add_new_task') ){
					echo "Invalid security";
					die();
				}
						
				$table_name = $wpdb->prefix . "oht_tasks";
				
				$tasks = $wpdb->get_results("SELECT id, task, description FROM " . $table_name . " order by task ASC");
				
				$tasks_list = array();
				
				foreach($tasks as $task){
					$tasks_list[$task->id] = $task->task;
				}
				
				$table_name = $wpdb->prefix . "oht_class_tasks";
				
				foreach($_POST as $entry => $value){
					if($value != "NONE"){
						if(strpos($entry,"oht_time")!==FALSE){
							$parts = explode("_", $entry);
							$wpdb->insert( $table_name, array( 
													'class_id' => filter_var($_POST['oht_class'], FILTER_VALIDATE_INT),
													'task' => filter_var($tasks_list[$parts[2]], FILTER_SANITIZE_STRING),
													'hours' => filter_var($value, FILTER_VALIDATE_INT),
													'time' => filter_var(time(), FILTER_VALIDATE_INT),
													'week' => filter_var($_POST['task_week'], FILTER_VALIDATE_INT),
													'comments' => filter_var($_POST['oht_task_comments'], FILTER_SANITIZE_STRING),
												)
							);
						}
					}
				}
				
				if(isset($_POST['oht_other_task'])){
					if($_POST['oht_other_time'] != "NONE"){
						$wpdb->insert( $table_name, array( 
													'class_id' => filter_var($_POST['oht_class'], FILTER_VALIDATE_INT),
													'task' => filter_var($_POST['oht_other_task'], FILTER_SANITIZE_STRING),
													'hours' => filter_var($_POST['oht_other_time'], FILTER_VALIDATE_INT),
													'comments' => filter_var($_POST['oht_task_comments'], FILTER_SANITIZE_STRING),
												)
							);
					}					
				}
					
			}
				
		}
		
	}
	
	function list_tasks($tasks, $class_id){
	
		global $wpdb;
		
		?>
		<div class="tasks">
			<p class='task_title'>Class : <?PHP
		
				$class = $this->get_class($class_id);
				echo $class[0]->class_name;

			?></p><?PHP
	
		$curr_week = 0;
		$week_total = 0;
		$total = false;
	
		foreach($tasks as $task){
		
			if($curr_week != $task->week){	
				$curr_week = $task->week;
				if($week_total!=0){
					?><p><span id="total_<?PHP echo $task->id; ?>"><?PHP echo ($week_total/60); ?></span> hours worked</p><?PHP
					$week_total = 0;
				}
				?><h2>Tasks for Week <?PHP echo $task->week; ?> </h2><?PHP
			}
		
			?>
				<div id='task_<?PHP echo $task->id; ?>' class='task'>
					<div>
					<p><?PHP echo $task->task; ?></p></div>
					<div>
					<?PHP
						$week_total += $task->hours;
						echo $task->hours;					
					?> minutes </div>				
					<div class="oht_delete_button">
						<button onclick="javascript:oht_task_delete(<?PHP echo $task->id; ?>,<?PHP echo $_GET['class_id']; ?>,<?PHP echo $task->week; ?>)">Delete</button>
					</div>
				</div>
			<?PHP
		}
		?><p><span id="total_<?PHP echo $task->week; ?>"><?PHP echo ($week_total/60); ?></span> hours worked</p><?PHP
		
		if($task->comments!=""){
			?><p>Your comment <em>"<?PHP echo $task->comments; ?>"</em></p><?PHP
		}
		echo "</div>";
	}
	
	function add_new_task($class_id){
	
		global $wpdb;
	
		$table_name = $wpdb->prefix . "oht_tasks";
		
		$tasks = $wpdb->get_results("SELECT id, task, description FROM " . $table_name . " order by task ASC");
		
		?><h3>Add tasks</h3><form class='add_new_task' action='' method='POST' onsubmit='return oht_add_tasks();'><?PHP
		
		wp_nonce_field('oht_add_new_task','add_new_task');
		
		echo "<p>Add your tasks and time spent on them this week</p>";
		echo "<p>Which Week is this for?</p>";
		
		?><p><select name='task_week'><?PHP
					
					for($x=1;$x<=52;$x++){
					
						echo "<option ";
						
						if($class->class_length == $x){
						
							echo " selected ";
						
						}
						
						echo " value='" . $x . "'>Week " . $x . "</option>";
					
					}
					
					?>
		</select></p><?PHP
		
		$table_name = $wpdb->prefix . "oht_times";
		
		$times = $wpdb->get_results("SELECT time FROM " . $table_name . " order by time ASC");
		
		foreach($tasks as $task){
		
			if($task->task!=""){
				
				echo "<div class='oht_task'>";
				echo "<div><p class='oht_task_name'>" . $task->task . "</p></div>";
				
				echo "<div><select id='oht_time' name='oht_time_" . $task->id . "'><option value='NONE'>Time spent...</option>";
				
				foreach($times as $time){
				
					if($time->time!=""){
				
						echo "<option value='" . addslashes($time->time) . "'>" . $time->time . " minutes</option>";
					
					}
					
				}
				
				echo "</select></div>";
				echo "<p><div class='oht_task_desc'>" . $task->description . "</p></div></div>";
			
			}
			
		}
	
		echo "<div class='oht_task'>";
		echo "<p>Add a task not listed</p><input type='text' id='oht_other_task' name='oht_other_task' />";
		echo "<p><select id='oht_time' name='oht_other_time'><option value='NONE'>Time spent...</option>";
				
		foreach($times as $time){
		
			if($time->time!=""){
		
				echo "<option value='" . addslashes($time->time) . "'>" . $time->time . " minutes</option>";
			
			}
			
		}
		
		echo "</select></p>";
		echo "</div>";
		echo "<p>Any Comments</p><textarea id='oht_other_time' name='oht_task_comments'></textarea>";
		echo "<input type='hidden' value='add_new_task' name='oht_add_new_task' />";
		echo "<input type='hidden' value='" . $class_id . "' name='oht_class' />";
		
		?><p><input type='submit' value='Add tasks' /></p>
		</form><?PHP
	}
	
	function show_classes($classes){
	
		global $wpdb;
		
		echo "<div id='oht_class_list'>";
		
		echo "<h3>Your Classes</h3>";
		
		foreach($classes as $class){
			?>
			<div class="oht_class">
				<h4>Class : <?PHP echo $class->class_name; ?></h4>
				<p><?PHP echo $class->employer; ?></p>
				<p><?PHP echo $class->department; ?></p><?PHP				
						
				$table_name = $wpdb->prefix . "oht_class_tasks";
				
				$tasks = $wpdb->get_results( 
					$wpdb->prepare( 
						"select * from $table_name WHERE class_id = %d order by week ASC", filter_var($class->id, FILTER_VALIDATE_INT)
						)
				);
				
				echo "<div>";
				echo "<button onclick='javascript:oht_show_class_form(" . $class->id . ")'>Edit class</button>";
				echo "</div>";
				echo "<div class='oht_class_form' id='oht_class_" . $class->id . "'>";
				$this->show_class_form($class);
				echo "</div>";
				
				$data = get_option('oht_config_settings');
				$some_url = get_permalink($data['data_page_id']);
				$params = array( 'class_id' => $class->id );
				$some_url = add_query_arg( $params, $some_url );
				
				if(count($tasks)==0){
					echo "<p><a href='" . $some_url . "'><button>Add a task</button></a></p>";
				}else{
					echo "<p>" . count($tasks) . " task(s) added </p><a href='" . $some_url . "'><button>Add more tasks</button></a></p>";
					$total = 0;
					$max = 0;
					foreach($tasks as $task){
						$total += $task->hours;
						if($task->time<$max){
							$max = $task->time;
						}
					}
					echo "<p>" . ($total/60) . " Hours worked | ";
					echo " Last Updated : " . date("n/j/Y",$task->time) . " </p>";
				}
				
				echo "</p>";
				
			?></div>
			<?PHP
		}
		
		echo "</div>";
		
	}
	
	function show_class_form($class){
		?>
			<div>
				<form class='update_class' action='javascript:oht_update_class(<?PHP echo $class->id; ?>)'>
					<p>Institution<input type='text' id='inst_<?PHP echo $class->id; ?>' value="<?PHP echo $class->employer; ?>" /></p>
					<p>Department<input type='text' id='dept_<?PHP echo $class->id; ?>' value="<?PHP echo $class->department; ?>" /></p>
					<p>Compensation<input type='text' id='comp_<?PHP echo $class->id; ?>' value='<?PHP echo $class->compensation; ?>' /></p>
					<p>Course delivery</p>					
					<select id='type_<?PHP echo $class->id; ?>'>";
						<option <?PHP if($class->class_type=="campus"){ echo " selected "; } ?> value='campus'>Campus based</option>
						<option <?PHP if($class->class_type=="online"){ echo " selected "; } ?> value='online'>Online Only</option>
						<option <?PHP if($class->class_type=="hybrid"){ echo " selected "; } ?> value='hybrid'>Hybrid</option>
					</select>
					<p>Length</p><select id='length_<?PHP echo $class->id; ?>'><?PHP
					
					for($x=1;$x<=52;$x++){
					
						echo "<option ";
						
						if($class->class_length == $x){
						
							echo " selected ";
						
						}
						
						echo " value='" . $x . "'>" . $x . " Week(s)</option>";
					
					}
					
					?>
					</select>
					<p>Credits</p>
					<select id='credit_<?PHP echo $class->id; ?>'><option value='NONE'>Select...</option><?PHP
		
					for($x=1;$x<=5;$x++){					
						
						echo "<option ";
						
						if($class->credits == $x){
						
							echo " selected ";
						
						}
					
						echo " value='" . $x . "'>" . $x . "</option>";
					
					}
		
					?></select>	
					<br />
					<input type='submit' value='Update' />
				</form>
			</div>
		<?PHP
	}
	
	public function add_class_to_db(){
	
		if(!wp_verify_nonce($_POST['oht_add_class'],'oht_add_class') ){
			echo "Invalid security";
			die();
		}
	
		global $wpdb, $current_user;
		
		$table_name = $wpdb->prefix . "oht_classes";
		
		$inst = $_POST['oht_institution'];
		$dept = $_POST['oht_department'];
		
		if($_POST['oht_credit'] != "NONE"){
			$credit = $_POST['oht_credit'];
		}else{
			$credit = $_POST['oht_other_credit'];
		}
		
		$wpdb->insert( $table_name, array( 
											'user_id' => filter_var($current_user->ID, FILTER_VALIDATE_INT),
											'employer' => $inst,
											'department' => $dept,
											'class_name' => $_POST['oht_class_name'],
											'compensation' => str_replace("$","",str_replace(",","",$_POST['oht_compensation'])),
											'class_type' => $_POST['oht_type'],
											'class_length' => filter_var($_POST['oht_length'], FILTER_VALIDATE_INT),
											'credits' => filter_var($credit, FILTER_VALIDATE_INT),
										)
					);

	}
	
	public function class_form(){
	
		global $wpdb;
	
		$table_name = $wpdb->prefix . "oht_institutions";
		
		wp_nonce_field('oht_add_class','oht_add_class');
		
		echo "<p>Full Name of college or university (Please no acronyms):</p>";
		
		echo "<input type='text' name='oht_institution' id='oht_institution' />";
		
		echo "<p>Department</p><input type='text' id='oht_department' name='oht_department' />";
		
		echo "<p>What is your compensation for the entire course?</p><input type='text' name='oht_compensation' id='oht_compensation' />";
		
		echo "<p>Where is the course taught?</p><select id='oht_type' name='oht_type'>";
		
		echo "<option value='campus'>Campus based</option><option value='online'>Online Only</option><option value='hybrid'>Hybrid</option>";
		
		echo "</select>";
		
		echo "<p>How long is the course?</p><select id='oht_length' name='oht_length'>";
		
		for($x=1;$x<=52;$x++){
		
			echo "<option value='" . $x . "'>" . $x . " Week(s)</option>";
		
		}
		
		echo "</select>";
		
		echo "<p>How many credits is the course?</p><select id='oht_credit' name='oht_credit'><option value='NONE'>Select...</option>";
		
		for($x=1;$x<=5;$x++){
		
			echo "<option value='" . $x . "'>" . $x . "</option>";
		
		}
		
		echo "</select>";
		
		echo "<p>Other, if the correct number of credits is not listed above:</p><input type='text' name='oht_other_credit' id='oht_other_credit' />";
		
		echo "<p>Please give this class a name</p><input type='text' id='oht_class_name' name='oht_class_name' />";
		
		echo "<p><input type='submit' value='Create Class' /></p>";
	
	}
	
	public function add_new_class(){
	
		global $wpdb;
	
		echo "<div id='timesheet'>";
	
		echo "<div id='oht_class_add'><button onclick='javascript:oht_show_add()'>Add another class</button></div>";
	
		echo "<div id='oht_new_class'>";
		
		echo "<form action='' onsubmit='return oht_first_class_verify()' method='POST'>";
		
		$this->class_form();
		
		echo "<input type='hidden' name='add_new_class' value='add_new_class' />";
		
		echo "</form>";
		
		echo "</div>";
		
		echo "</div>";
		
	}
	
	
	public function add_first_class(){
	
		global $wpdb;
	
		echo "<div id='timesheet'>";
		
		echo "<form action='' onsubmit='return oht_first_class_verify()' method='POST'>";
	
		echo "<p>Please add your first class</p>";
		
		$this->class_form();
		
		echo "<input type='hidden' name='new_class' value='new_class' />";
		
		echo "</form>";
		
		echo "</div>";
		
	}
	
	function tasks_and_time(){
		
		$table_name = $wpdb->prefix . "oht_tasks";
		
		$tasks = $wpdb->get_results("SELECT id,task FROM " . $table_name . " order by task ASC");
		
		echo "<select name='task'>";
		
		foreach($tasks as $task){
		
			if($task->task!=""){
		
				echo "<option value='" . $task->id . "'>" . $task->task . "</option>";
			
			}
			
		}
		
		echo "</select>";
		
		$table_name = $wpdb->prefix . "oht_times";
		
		$times = $wpdb->get_results("SELECT id,time FROM " . $table_name . " order by time ASC");
		
		echo "<p>Select a time</p><select name='time'>";
		
		foreach($times as $time){
		
			if($time->time!=""){
		
				echo "<option value='" . $time->id . "'>" . $time->time . "</option>";
			
			}
			
		}
		
		echo "</select>";

	}

} 

$oht_Settings_timesheet_display = new oht_Settings_timesheet_display();
