function task_updated(task_id){ 

	jQuery(document).ready(function($) {
	
		var content;
		var editor = tinyMCE.get(task_id + "_description");
		if (editor) {
			// Ok, the active tab is Visual
			content = editor.getContent();
		} else {
			// The active tab is HTML, so just query the textarea
			content = $('#'+ task_id + "_description").val();
		}
	
		var data = {
			action: "oht_task_updated",
			task_id:task_id,
			task_value:jQuery("#" + task_id + "_task").val(),
			task_description:content,
			nonce: manage_task.answerNonce
		};
		
		jQuery.post(manage_task.ajaxurl, data, function(response) {
			alert(response);
		});
	});
	
}