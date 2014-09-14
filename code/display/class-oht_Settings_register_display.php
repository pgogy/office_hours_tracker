<?php

class oht_Settings_register_display{
	
	public function __construct() {			
		add_filter('the_content', array($this, 'display'));	
		add_action("wp_enqueue_scripts", array($this,"scripts_styles"));
	}
	
	function scripts_styles(){
		global $post;
		$data = get_option('oht_config_settings');
		if($data['register_page_id']==$post->ID){
			wp_register_style( 'oht_register_css', plugins_url( '../../css/oht_register.css', __FILE__ ) );
			wp_enqueue_style( 'oht_register_css' );
			wp_register_script( 'oht_register_js', plugins_url( '../../js/oht_register.js', __FILE__ ), array("jquery") );
			wp_localize_script( 'oht_register_js', 'oht_register', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ), 'answerNonce' => wp_create_nonce( 'oht_register_js_nonce' ) ) );
			wp_enqueue_script( 'oht_register_js', array("jquery") );
		}
	}
	
	public function show_form(){
	
		?><form id="register_form" method="POST" action="" onsubmit="return oht_register_user();">
			 	<div><p class='inst'>Please enter your details below. Fields marked * are required</p></div>
				<div><p>User name *</p><p><input type="text" name="user_name" id="user_name" /></p></div>
				<div><p>First name *</p><p><input type="text" name="first_name" id="first_name" /></p></div>
				<div><p>Last name *</p><p><input type="text" name="family_name" id="family_name" /></p></div>
				<div><p>Street Address *</p><p><input type="address" name="address" id="address" /></p></div>
				<div><p>Street Address 2</p><p><input type="address2" name="address2" id="address2" /></p></div>
				<div><p>City *</p><p><input type="city" name="city" id="city" /></p></div>
				<div><p>State *</p><p><input type="state" name="state" id="state" /></p></div>
				<div><p>Zip code *</p><p><input type="zipcode" name="zipcode" id="zipcode" /></p></div>				
				<div><p>Email Address *</p><p><input type="text" name="email" id="email" /></p></div>
				<div><p>Phone Number *</p><p><input type="text" name="number" id="number" /></p></div>
				<div><p>How do you want to receive reminders to fill out the time card?</p><p><select name="update" id="update"><option value="email">Email</option><option value="sms">Mobile SMS</option><option value="none">Opt-out of notifications</option></select></div>
				<div>
					<p class="sms_notice">
						If you choose to receive notifications via text message,  SEIU will never charge you for text message alerts, but carrier message and data rates may apply. Text STOP to 787753 to unsubscribe, and HELP for more info. You will receive one reminder per week while participating in the project.
					</p>
				</div>
				<div id="oht_sms_agree">
					<p>
						I agree, and want to receive text message alerts
						<input type="checkbox" id="sms_agree" name="sms_agree" />
					</p>
				</div>
				<div><p>Choose a Password *</p><p><input type="text" name="password" id="password" /></p></div>
				<div><p class='button'><input type="submit" value="Register" /></p></div>
				<div id="oht_error"></div>
		</form><?PHP
	
	}
	
	public function display($content){
	
		global $post, $oht_Register_user;
		$data = get_option('oht_config_settings');
		
		if($data['register_page_id']==$post->ID){
		
			echo $content;
		
			if(isset($_POST['first_name'])){
				
				if(!$oht_Register_user->register_user()){
					$this->show_form();
				}
			
			}else{
			
				$this->show_form();
				
			}
			
		}else{
			return $content;
		}
	
	}

} 

$oht_Settings_register_display = new oht_Settings_register_display();
