<?php

class oht_User_profile{
	
	public function __construct() {			
		add_action('show_user_profile', array($this, 'user_profile_fields'));
		add_action('edit_user_profile', array($this, 'user_profile_fields'));
		add_action('edit_user_profile_update', array($this, 'update_extra_profile_fields'));
	}
 
	function update_extra_profile_fields($user_id) {	
		if ( current_user_can('edit_user',$user_id) ){
			update_user_meta($user_id, 'phone_number', filter_var($_POST["oht_phone_number"], FILTER_SANITIZE_STRING));
			update_user_meta($user_id, 'address', filter_var($_POST["oht_address"], FILTER_SANITIZE_STRING));
			update_user_meta($user_id, 'update', filter_var($_POST["oht_update"], FILTER_SANITIZE_STRING));
			if($_POST['update_permission']=="on"){
				$permission = 1;
			}else{
				$permission = 0;
			}
			update_user_meta($user_id, 'update_permission', $permission);
			update_user_meta($user_id, 'address2', filter_var($_POST['oht_address2'], FILTER_SANITIZE_STRING));
			update_user_meta($user_id, 'city', filter_var($_POST['oht_city'], FILTER_SANITIZE_STRING));
			update_user_meta($user_id, 'state', filter_var($_POST['oht_state'], FILTER_SANITIZE_STRING));
			update_user_meta($user_id, 'zipcode', filter_var($_POST['oht_zipcode'], FILTER_SANITIZE_STRING));
		}
	}
	
	function user_profile_fields($user){
	
		echo '<table class="form-table"><tbody>'
	
		?><tr id="phone_number">
			<th><label for="number1">Phone Number</label></th>
			<td>
				<input type="text" name="oht_phone_number" class="regular-text" size="16"
				value="<?PHP echo get_user_meta($user->data->ID, 'phone_number', TRUE); ?>" />
				<p class="description">Your phone number</p>
			</td>
		</tr><?PHP
		
		?><tr id="address">
			<th><label for="address1">Street Address</label></th>
			<td>
				<input type="text" name="oht_address" class="regular-text" size="16" autocomplete="off" value="<?PHP echo get_user_meta($user->data->ID, 'address', TRUE); ?>" />
				<p class="description">Street address</p>
			</td>
		</tr><?PHP
		
		?><tr id="address2">
			<th><label for="address2">Street Address 2</label></th>
			<td>
				<input type="text" name="oht_address2" class="regular-text" size="16" value="<?PHP echo get_user_meta($user->data->ID, 'address2', TRUE); ?>" autocomplete="off" />
				<p class="description">Street address 2</p>
			</td>
		</tr><?PHP
		
		?><tr id="city">
			<th><label for="city1">City</label></th>
			<td>
				<input type="text" name="oht_city" class="regular-text" size="16" value="<?PHP echo get_user_meta($user->data->ID, 'city', TRUE); ?>"" autocomplete="off">
				<p class="description">Your City</p>
			</td>
		</tr><?PHP
		
		?><tr id="state">
			<th><label for="state1">State</label></th>
			<td>
				<input type="text" name="oht_state" class="regular-text" size="16" value="<?PHP echo get_user_meta($user->data->ID, 'state', TRUE); ?>" autocomplete="off">
				<p class="description">Your state</p>
			</td>
		</tr><?PHP
		
		?><tr id="zipcode">
			<th><label for="zipcode1">Zip code</label></th>
			<td>
				<input type="text" name="oht_zipcode" class="regular-text" size="16" value="<?PHP echo get_user_meta($user->data->ID, 'zipcode', TRUE); ?>" autocomplete="off">
				<p class="description">Your Zipcode</p>
			</td>
		</tr><?PHP
		
		$update = get_user_meta($user->data->ID, 'update', TRUE);
		
		?><tr id="update">
			<th><label for="update1">Update Preference</label></th>
			<td>
				<select name="oht_update">
					<option <?PHP if($update=="none"){ echo "selected"; } ?> value="none">None</option>
					<option <?PHP if($update=="email"){ echo "selected"; } ?> value="email">Email</option>
					<option <?PHP if($update=="sms"){ echo "selected"; } ?> value="sms">Mobile SMS</option>
				</select>
				<p class="description">Your update preference</p>
				<?PHP
					if($update == "sms" && get_user_meta($user->data->ID, 'phone_number', TRUE)==""){
						?><p style="background:#f00; color:#000; padding:5px; font-weight:bold">Please set a mobile phone number if you wish to receive SMS notifications</p><?PHP
					}
				?>
			</td>
		</tr><?PHP

		?><tr id="update">
			<th><label for="update1">SMS Permission</label></th>
			<td>
				<input type="checkbox" name="update_permission" 
				<?PHP
					$permission = get_user_meta($user->data->ID, 'update_permission', TRUE);
					if($permission==1){
						echo " checked ";
					}
				?> />
				<p class="description">Check if you give your permission to receive SMS</p>
			</td>
		</tr><?PHP

		echo "</tbody></table>";

	}
	
} 

$oht_User_profile = new oht_User_profile();
