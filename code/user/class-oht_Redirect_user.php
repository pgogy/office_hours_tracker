<?php

class oht_Redirect_user{
	
	public function __construct() {			
		add_filter( 'login_redirect', array($this, 'timesheet_redirect'),10,3);
	}

	function timesheet_redirect($redirect_to, $request, $user) {

		global $user;
		
		if ( isset( $user->roles ) && is_array( $user->roles ) ) {
			if ( in_array( 'timesheet', $user->roles ) ) {
				$data = get_option('oht_config_settings');
				return get_permalink($data['data_page_id']);
			} 
		} 
		
		return home_url();
		
	}
	
} 

$oht_Redirect_user = new oht_Redirect_user();
