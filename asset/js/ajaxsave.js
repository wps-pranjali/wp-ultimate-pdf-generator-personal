jQuery( document ).on( 'click', '#guest_email', function(e) {
	e.preventDefault();
	var mwb_email_to = jQuery('#mwb-email-to').val();
	var postid = jQuery('#emailpostID').val();
	$this = jQuery(this).parent().parent();
	jQuery(this).parent().remove();
	jQuery($this).append('<img src="'+postajaxsave.baseUrl+'/asset/images/updateimg.gif" alt="processing" width="55%">');
	jQuery.ajax({
		url: postajaxsave.ajax_url,
		type: 'post',
		data: {
			action: 'postajax_exportandmail',
			mwb_email_to: mwb_email_to,
			postid: postid,
			nonce : postajaxsave.ajax_nonce
		},
		success: function (response) {
				if(response.NOTSENT){
					jQuery($this).find('img').remove();
					jQuery($this).append('<div><img src="'+postajaxsave.baseUrl+'/asset/images/Windows-error.png" alt="processing" width="50%"><h4>Sorry Your Email is not sent.</h4><input type="submit" class="button-primary" id="send_again" name="send_again" value="Try Again"></div>');
				} else if(response.INVALIDEMAIL) {
					jQuery($this).find('img').remove();
					var htmlq="<div class='mwb-export-popup' style='float:left;padding:20px 20px 20px 20px;'>";
					htmlq+= '<div style="color:red"><h4>Please enter a valid e-mail address.</h4></div>';
					htmlq+='<label id="to-label">To:<input type="email" style="margin-top:10px" id="mwb-email-to" required></label>';
					htmlq+="<input id='guest_email' style='margin-top:10px' class='button-primary' type='submit' name='email_submit' value='Send'></div>";
					jQuery($this).append(htmlq);
				} else if(response.SENT) {
					jQuery($this).html('<img src="'+postajaxsave.baseUrl+'/asset/images/email.png" alt="sent" width="20%" style="margin: 20px auto;"><h4 style="text-align: center;">PDF is sent To Your Email Address</h4>');
				}
		}
	});
});

jQuery(function ($) {
	jQuery('.export-pdf').click(function(e) {
		e.preventDefault();
		var postID = jQuery(this).attr('id');
		var htmlq="<input type='hidden' id='emailpostID'><div class='mwb-export-popup' style='float:left;padding:20px 20px 20px 20px;'>";
		htmlq+='<label id="to-label">To:<input type="email" style="margin-top:10px" id="mwb-email-to" required></label>';
		htmlq+="<input id='guest_email' style='margin-top:10px' class='button-primary' type='submit' name='email_submit' value='Send'>";
		htmlq+="</div>";
		jQuery('#examplePopup1').html(htmlq);
		jQuery('#emailpostID').val(postID);
	});
	jQuery( document ).on( 'click', '#send_again', function(e) {
		$this = jQuery(this).parent().parent();
		jQuery(this).parent().remove();
		var htmlq="<div style='float:left;padding:20px 20px 20px 20px;'>";
		htmlq+='<label id="to-label">To:<input type="email" style="margin-top:10px" id="mwb-email-to" required></label>';
		htmlq+="<input id='guest_email' style='margin-top:10px' class='button-primary' type='submit' name='email_submit' value='Send'>";
		htmlq+="</div>";
		jQuery($this).append(htmlq);
	});
});
