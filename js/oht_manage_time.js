function time_updated(time_id){ 

	jQuery(document).ready(function($) {
	
		var data = {
			action: "wht_time_updated",
			time_id:time_id,
			time_value:jQuery("#" + time_id + "_time").val(),
			nonce: manage_time.answerNonce
		};
		
		jQuery.post(manage_time.ajaxurl, data, function(response) {
			alert(response);
		});
	});
	
}