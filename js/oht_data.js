function oht_update_class(class_id){ 

	jQuery(document).ready(function($) {
	
		var data = {
			action: "oht_update_class",
			class: class_id,
			inst: jQuery("#inst_" + class_id).val(),
			department: jQuery("#dept_" + class_id).val(),
			compensation: jQuery("#comp_" + class_id).val(),
			length: jQuery("#length_" + class_id).val(),
			type: jQuery("#type_" + class_id).val(),
			credit: jQuery("#credit_" + class_id).val(),
			nonce: oht_data.answerNonce,
		};
		
		jQuery.post(oht_data.ajaxurl, data, function(response) {
				alert(response);
			}
		);
	});
	
}


function oht_add_tasks(){ 

	if(jQuery('#oht_other_task').val()!=="" && jQuery("#oht_other_time").val() == "NONE" ){
		alert("Please choose a time for the other task");
		return false;
	}

	return true;

}

function oht_first_class_verify(){ 

	if(jQuery('#oht_institution').val()==""){
		alert("Please enter an institution");
		return false;
	}

	if(jQuery('#oht_department').val()==""){
		alert("Please enter a department");
		return false;
	}
	
	if(jQuery("#oht_credit").val() == "NONE" && jQuery('#oht_other_credit').val()==""){
		alert("Please enter the credits for this course");
		return false;
	}

	if(jQuery("#oht_compensation").val()==""){
		alert("Please enter a compensation for this class");
		return false;
	}
	
	if(jQuery("#oht_class_name").val()==""){
		alert("Please enter a name for this class");
		return false
	}
	
	return true;

}

function oht_task_delete(task, class_id, week_id){
	jQuery(document).ready(function($) {
	
		var data = {
			nonce: oht_data.answerNonce,
			action: "oht_delete_task",
			task_id: task,
			week_id: week_id,
			class_id: class_id,
		};
		
		jQuery.post(oht_data.ajaxurl, data, function(response) {
				alert("Task deleted");
				jQuery("#task_" + task).remove();
				jQuery("#total_" + week_id).html(response/60);
			}
		);
	});
}

function oht_show_add(){

	if(jQuery("#oht_new_class").is(":visible")){
		jQuery("#oht_new_class").hide();
	}else{
		jQuery("#oht_new_class").show();
	}
	
}

function oht_show_class_form(class_id){

	if(jQuery("#oht_class_" +  class_id).is(":visible")){
		jQuery("#oht_class_" +  class_id).hide();
	}else{
		jQuery("#oht_class_" +  class_id).show();
	}
	
}