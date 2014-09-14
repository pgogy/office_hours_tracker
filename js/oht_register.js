function oht_register_user(){

	jQuery("#oh_error").html("");
	
	if(jQuery("#user_name").val()!=""){
		if(!/^[a-zA-Z0-9]+$/.test(jQuery("#user_name").val())){
			alert("Please only use letters and numbers in your user name");
			return false;
		}
	}else{
		alert("Please add a user name");
		return false;
	}
	
	if(jQuery("#user_name").val() == ""){
		alert("Please enter a value into user name");
		return false;
	}

	if(jQuery("#first_name").val() == ""){
		alert("Please enter a value into first name");
		return false;
	}
	
	if(jQuery("#family_name").val() == ""){
		alert("Please enter a value into last name");
		return false;
	}
	
	if(jQuery("#number").val() == "" && jQuery("#email").val() == ""){
		alert("Please enter a mobile phone number or an email address");
		return false;
	}
	
	if(jQuery("#update").val() == "email" && jQuery("#email").val() == ""){
		alert("If you wish to be contacted over email, please enter an email address");
		return false;
	}
	
	if(jQuery("#update").val() == "sms" && jQuery("#number").val() == ""){
		alert("If you wish to be contacted over SMS, please enter a mobile number");
		return false;
	}
	
	if(jQuery("#update").val() == "sms" && jQuery("#sms_agree").attr("checked") != "checked"){
		alert("If you wish to be contacted over SMS, please check the box to agree");
		return false;
	}
	
	if(jQuery("#password").val() == ""){
		alert("Please enter a password");
		return false;
	}
	
	if(jQuery("#address").val() == ""){
		alert("Please enter a Street address");
		return false;
	}
	
	if(jQuery("#city").val() == ""){
		alert("Please enter a City");
		return false;
	}
	
	if(jQuery("#state").val() == ""){
		alert("Please enter a State");
		return false;
	}
	
	if(jQuery("#zipcode").val() == ""){
		alert("Please enter a Zip Code");
		return false;
	}
	
}

jQuery(document).ready(function($) {
    jQuery("#update").change( function(){
		if(jQuery(this).val()=="sms"){
			jQuery("#oht_sms_agree").css("display","block");
		}else{
			jQuery("#oht_sms_agree").css("display","none");
		}
	});
});
