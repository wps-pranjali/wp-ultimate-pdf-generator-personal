
jQuery(document).ready(function() {
	
		jQuery('.js-example-basic-multiple').select2();
	
	if(bulk_obj.post_export == true)
	{
		jQuery('<option>').val('export').text(bulk_obj.txt).appendTo("select[name='action']");
		jQuery('<option>').val('export').text(bulk_obj.txt).appendTo("select[name='action2']");
	}
	jQuery( document).ready(function(){

		if( jQuery( '#start_custom_footer_text' ).is(':checked') ){
			jQuery('.start_page_custom_footer').show();
		}else{
			jQuery('.start_page_custom_footer').hide();
		}
		if( jQuery( '#start_custom_footer_text_email' ).is(':checked') ){
			jQuery('#custom_footer_email_start').show();
		}else{
			jQuery('#custom_footer_email_start').hide();
		}
		if( jQuery('#start_custom_footer_text_contact').is(':checked' ) ){
			jQuery('#custom_footer_contact').show();
		}else{
			jQuery('#custom_footer_contact').hide();
		}
		if( jQuery('#start_custom_footer_text_address').is(':checked' ) ){
			jQuery('#custom_footer_address_start').show();
		}else{
			jQuery('#custom_footer_address_start').hide();
		}
		if( jQuery('#start_custom_footer_text_url').is(':checked' ) ){
			jQuery('#custom_footer_url').show();
		}else{
			jQuery('#custom_footer_url').hide();
		}
		if( jQuery('#watermark_text_pdf').is(':checked' ) ){
			jQuery('#doc_add_watermark').show();
		}else{
			jQuery('#doc_add_watermark').hide();
		}
		if( jQuery('#watermark_image_pdf').is(':checked' ) ){
			jQuery('#doc_add_watermark_image').show();
		}else{
			jQuery('#doc_add_watermark_image').hide();
		}
		
		jQuery( '#start_custom_footer_text_email').on( 'change',function(){
			if( jQuery('#start_custom_footer_text_email' ).is(':checked') ){
				jQuery('#custom_footer_email_start').show();
			}else{
				jQuery('#custom_footer_email_start').hide();
			}
		});
		jQuery( '#watermark_image_pdf').on( 'change',function(){
			if( jQuery('#watermark_image_pdf').is(':checked' ) ){
				jQuery('#doc_add_watermark_image').show();
			}else{
				jQuery('#doc_add_watermark_image').hide();
			}
		});
		jQuery( '#watermark_text_pdf').on( 'change',function(){
			if( jQuery('#watermark_text_pdf').is(':checked' ) ){
				jQuery('#doc_add_watermark').show();
			}else{
				jQuery('#doc_add_watermark').hide();
			}
		});
		jQuery('#start_custom_footer_text').on('change',function(){
			if( jQuery( '#start_custom_footer_text' ).is(':checked') ){
				jQuery('.start_page_custom_footer').show();
			}else{
				jQuery('.start_page_custom_footer').hide();
			}
		});
		
		jQuery('#start_custom_footer_text_contact').on('change',function(){
			if( jQuery('#start_custom_footer_text_contact').is(':checked' ) ){
				jQuery('#custom_footer_contact').show();
			}else{
				jQuery('#custom_footer_contact').hide();
			}
		});
		jQuery('#start_custom_footer_text_address').on('change',function(){
			if( jQuery('#start_custom_footer_text_address').is(':checked' ) ){
				jQuery('#custom_footer_address_start').show();
			}else{
				jQuery('#custom_footer_address_start').hide();
			}
		});
		jQuery('#start_custom_footer_text_url').on('change',function(){
			if( jQuery('#start_custom_footer_text_url').is(':checked' ) ){
				jQuery('#custom_footer_url').show();
			}else{
				jQuery('#custom_footer_url').hide();
			}
		});
	});
});

jQuery(function ($) {

	/////////////// License Activation ////////////////
	$('#mwb_updf_license_save').on('click',function(e){
		e.preventDefault();
		$('.licennse_notification').html('');
		var mwb_license = $('#mwb_updf_license_key').val();
		if( mwb_license == '' )
		{
			$('#mwb_updf_license_key').css('border','1px solid red');
			return false;
		}
		else
		{
			$('#mwb_updf_license_key').css('border','none');
		}
		$('.loading_image').show();
		
		$.ajax({
			url: bulk_obj.ajaxurl, 
			type: "POST",  
			dataType: 'json',
			data:{
				'action':'mwb_pdf_register_license',
				'mwb_nonce':bulk_obj.mwb_pdf_nonce,
				'license_key':mwb_license
			},
			success: function(response) 
			{
				if( response.status == false )
				{
					response.msg = 'Something Went Wrong! Please try again';
					$('.licennse_notification').css('color','red');
					$('.licennse_notification').html(response.msg);
				}
				$('.loading_image').hide();
				if(response.status == true )
				{
					$('.licennse_notification').css('color','green');
					$('.licennse_notification').html(response.msg);
					window.location.reload();
				}
				else
				{
					$('.licennse_notification').html(response.msg);
				}
			}
		});
	});
	var mwb_pdf_pointer_1 = bulk_obj.pointers;
	

	
	// hides as soon as the DOM is ready
	jQuery('div.section_body').hide();
    // shows on clicking the noted link
    jQuery('#mwbpdf_gen-options').find('h3').click(function() {
    	jQuery(this).toggleClass("open");
    	jQuery(this).next("div").slideToggle('1000');
    	$(".section_body").not($(this).next()).hide("slow","linear");
    	jQuery('#mwbpdf_gen-options').find('h3').not($(this)).removeClass('open') ;
    	return false;
    });
    //for page header settings
    jQuery('#page_header').bind('change', function() {
    	if(jQuery(this).val() == 'None') {
    		jQuery('#custom_logo').hide();
    		jQuery('.upload_imglogo_button').hide();
    	} else {
    		if ( '' === jQuery( '#logo_img_url' ).val() ) {
    			jQuery( '#custom_logo' ).hide();
    		} else 
    		jQuery('#custom_logo').show();
    		jQuery('.upload_imglogo_button').show();
    	}
    }).change();
  	//for link button settings
  	jQuery('#link_button').bind('change', function() {
  		if(jQuery(this).val() == 'default') {
  			jQuery('#custom_link').hide();
  			jQuery('.upload_link_button').hide();
  		} else {
  			if ( '' === jQuery( '#custon_link_url' ).val() ) {
  				jQuery( '#custom_link' ).hide();
  			} else
  			jQuery('#custom_link').show();
  			jQuery('.upload_link_button').show();
  		}
  	}).change();
    // Only show the "remove image" button when needed
    if ( '' === jQuery( '#custon_link_url' ).val() ) {
    	jQuery( '#custom_link' ).hide();
    }
    if ( '' === jQuery( '#logo_img_url' ).val() ) {
    	jQuery( '#custom_logo' ).hide();
    }
    if ( '' === jQuery( '#background_img_url' ).val() ) {
    	jQuery( '#wbgimg' ).hide();
    	jQuery( '.remove_img_button' ).hide();
    }
    if ( '' === jQuery( '#Cover_image_url' ).val() ) {
    	jQuery( '#wbgcoverimg' ).hide();
    	jQuery( '.remove_cover_button' ).hide();
    }
    if ( '' === jQuery( '#bullet_img_url' ).val() ) {
    	jQuery( '.remove_bullet_button' ).hide();
    	jQuery( '#bulletimg' ).hide();
    }
    //for general tab settings
    $('#gentab').click(function(e) { switchTab(e, 'gentab'); });
    $('#advtab').click(function(e) { switchTab(e, 'advtab'); });
    function switchTab(e, tab) {
    	e.preventDefault();

			// detect physical tab change to hide status messages
			if (typeof window.gdeTabChg != 'undefined') {
				$('#message').hide();
				delete window.gdeTabChg;
			}
			
			var newcontid = tab.substr(0,3) + "content";
			
			$('#' + newcontid).show();
			$('#' + tab).addClass('ui-tabs-selected ui-state-active');
			$('#' + tab).blur();
			
			if (tab !== 'gentab') {
				$('#gencontent').hide();
				$('#gentab').removeClass('ui-tabs-selected ui-state-active');
			}
			if (tab !== 'advtab') {
				$('#advcontent').hide();
				$('#advtab').removeClass('ui-tabs-selected ui-state-active');
			}
			
			
			// record tab change
			window.gdeTabChg = true;
		}
		
		
	});
//for show and hide check box
function showHideCheck(id,check) {
	if(check.checked) {
		document.getElementById(id).className = '';
	} else {
		document.getElementById(id).className = 'noDis';
	}
}
//Only show the "remove image" button when needed
if ( '' === jQuery( '#custon_link_url' ).val() ) {
	jQuery( '.remove_link_button' ).hide();
	jQuery( '#imglink' ).hide();
}

jQuery(document).ready(function() {
	jQuery('.upload_link_button').click(function(){
		inputField = jQuery(this).parent('div');
		tb_show('watermark', 'media-upload.php?TB_iframe=true');
		window.send_to_editor = function(html)
		{  
			url = jQuery(html).find('img').attr('src');
			if(typeof url == 'undefined')
				url = jQuery(html).attr('src');	
			jQuery( '#custon_link_url' ).val( url );
			jQuery( '#customlink' ).find( 'img' ).attr( 'src', url );
			jQuery( '.remove_link_button' ).show();
			jQuery( '#imglink' ).show();
			jQuery('#custom_link').show();
			tb_remove();
		};
		return false;
	});
});
//to remove link
jQuery( document ).on( 'click', '.remove_link_button', function() {
	jQuery('#custon_link_url').val('');
	jQuery('#imglink').hide();
	jQuery(this).hide();

	return false;
});

//Only show the "remove image" button when needed
if ( '' === jQuery( '#logo_img_url' ).val() ) {
	jQuery( '.remove_logo_button' ).hide();
	jQuery( '#imglogo' ).hide();
}

jQuery(document).ready(function() {
	jQuery('.upload_imglogo_button').click(function(){
		inputField = jQuery(this).parent('div');
		tb_show('Logo', 'media-upload.php?TB_iframe=true');
		window.send_to_editor = function(html)
		{  
			url = jQuery(html).find('img').attr('src');
			if(typeof url == 'undefined')
				url = jQuery(html).attr('src');	
			jQuery( '#logo_img_url' ).val( url );
			jQuery( '#customlogo' ).find( 'img' ).attr( 'src', url );
			jQuery( '.remove_logo_button' ).show();
			jQuery( '#imglogo' ).show();
			jQuery('#custom_logo').show();
			tb_remove();
		};
		return false;
	});
});
//remove logo button
jQuery( document ).on( 'click', '.remove_logo_button', function() {
	jQuery('#logo_img_url').val('');
	jQuery('#imglogo').hide();
	jQuery(this).hide();

	return false;
});


//fot set background image url
if ( '' === jQuery( '#background_img_url' ).val() ) {
	jQuery( '.remove_img_button' ).hide();
	jQuery( '#wbgimg' ).hide();
}

jQuery(document).ready(function() {
	if ( '' == jQuery( '#Cover_image_url' ).val() ) {
	
		jQuery( '.remove_cover_button' ).hide();
		jQuery( '#wbgcoverimg' ).hide();
	}

	jQuery('.upload_imgbg_button').click(function(){
		inputField = jQuery(this).parent('div');
		tb_show('watermark', 'media-upload.php?TB_iframe=true');
		window.send_to_editor = function(html)
		{  
			url = jQuery(html).find('img').attr('src');
			if(typeof url == 'undefined')
				url = jQuery(html).attr('src');	
			jQuery( '#background_img_url' ).val( url );
			jQuery( '#Watermark_Background' ).find( 'img' ).attr( 'src', url );
			jQuery( '.remove_img_button' ).show();
			jQuery( '#wbgimg' ).show();
			tb_remove();
		};
		return false;
	});
	jQuery('.upload_coverimage_button' ).click(function(){
		tb_show('Cover Image', 'media-upload.php?TB_iframe=true');
		window.send_to_editor = function(html)
		{  
			url = jQuery(html).find('img').attr('src');
			if(typeof url == 'undefined')
				url = jQuery(html).attr('src');	
			jQuery( '#Cover_image_url' ).val( url );
			jQuery( '#cover_Background' ).find( 'img' ).attr( 'src', url );
			jQuery( '.remove_img_button' ).show();
			jQuery( '#wbgcoverimg' ).show();
			tb_remove();
		};
		return false;
	});
	jQuery('.upload_bgcoverimage_button' ).click(function(){
		tb_show('Cover Background Image', 'media-upload.php?TB_iframe=true');
		window.send_to_editor = function(html)
		{  
			url = jQuery(html).find('img').attr('src');
			if(typeof url == 'undefined')
				url = jQuery(html).attr('src');	
			jQuery( '#Cover_image_url' ).val( url );
			jQuery( '#cover_Background' ).find( 'img' ).attr( 'src', url );
			jQuery( '.remove_img_button' ).show();
			jQuery( '#wbgcoverimg' ).show();
			tb_remove();
		};
		return false;
	});
});
//to remove image button
jQuery( document ).on( 'click', '.remove_img_button', function() {
	jQuery('#background_img_url').val('');
	jQuery('#wbgimg').attr('src', '');
	jQuery(this).hide();
	jQuery( '#wbgimg' ).hide();
	return false;
});
jQuery( document ).on( 'click', '.remove_cover_button', function() {
	jQuery('#Cover_image_url').val('');
	jQuery('#wbgcoverimg').attr('src', '');
	jQuery(this).hide();
	jQuery( '#wbgcoverimg' ).hide();
	return false;
});

//to manage bullet image url
if ( '' === jQuery( '#bullet_img_url' ).val() ) {
	jQuery( '.remove_bullet_button' ).hide();
	jQuery( '#bulletimg' ).hide();
}
  //to upload bullet image url
  jQuery(document).ready(function() {
  	jQuery('.upload_bullet_button').click(function(){
  		inputField = jQuery(this).parent('div');
  		tb_show('Bullet Symbol', 'media-upload.php?TB_iframe=true');
  		window.send_to_editor = function(html)
  		{  
  			url = jQuery(html).find('img').attr('src');
  			if(typeof url == 'undefined')
  				url = jQuery(html).attr('src');	
  			jQuery( '#bullet_img_url' ).val( url );
  			jQuery( '#Watermark_bullet' ).find( 'img' ).attr( 'src', url );
  			jQuery( '.remove_bullet_button' ).show();
  			jQuery( '#bulletimg' ).show();
  			tb_remove();
  		};
  		return false;
  	});
  });
//to remove bullet image url
jQuery( document ).on( 'click', '.remove_bullet_button', function() {
	jQuery('#bullet_img_url').val('');
	jQuery('#bulletimg').attr('src', '');
	jQuery(this).hide();
	jQuery( '#bulletimg' ).hide();
	return false;
});

jQuery(function($)
{	
	//on setting button click 
	jQuery('.settingbutton').click(function(e)
	{
		var con=confirm(bulk_obj.alert_saved_apply);
		if(con==true)
		{
			var set=jQuery('#sett').val();
			debugger;
			call_form();

			
			e.preventDefault();
			return false;

		}
	});	
});

function call_form() 
{
	debugger;
	my_form=document.createElement('FORM');
	my_form.name='savesetting';
	my_form.method='POST';
	my_tb=document.createElement('INPUT');
	my_tb.type='hidden';
	my_tb.name='currentsetting';
	my_tb.value=jQuery('#sett').val();
	my_form.appendChild(my_tb);
	my_tb=document.createElement('INPUT');
	my_tb.type='hidden';
	my_tb.name='s_wpnonce';
	my_tb.value= bulk_obj.reset_nonce;
	my_form.appendChild(my_tb);
	document.body.appendChild(my_form);
	
	jQuery(my_form).submit();
}

jQuery(function($)
{ 
	jQuery('.savemeta').click(function()
	{

		$(".test .txt").each(function() {
			var txtval=($(this).val());

			var txtid=($(this).attr('id'));

			jQuery.ajax({
				url: ajaxurl,
				type: 'POST',
				data: {action:'savemetafunction',txtval:txtval,txtid:txtid},
				cache: false,
				dataType: 'json',
				success:function()
				{

			
			}
			
		});
		});
	});
});

jQuery(function($)
{
	jQuery('.posttype').click(function()
	{
		$this=$(this);
		id=$this.attr('id');
		chk=id.split("_",1);
		$( '.clicked' ).removeClass( 'clicked' );
		jQuery( '.display-block' ).addClass( 'display-none' );
		jQuery( '.display-block' ).removeClass( 'display-block' );

		$this.addClass( 'clicked' );
		jQuery('.meta'+chk).removeClass( 'display-none' );
		jQuery('.meta'+chk).addClass( 'display-block' );
		
	});
});

jQuery(function($)
{	
	//to same meta deta
	jQuery('.savemeta').click(function()
	{
		$(".test .chk").each(function()
		{
			if($(this).is(":checked"))
			{
				var chkval=1;
				var chkid=$(this).attr('id');
				jQuery.ajax({
					url: ajaxurl,
					type: 'POST',
					data: {action:'savemeta',chkid:chkid,chkval:chkval},
					cache: false,
					dataType: 'json',
					success:function(res)
					{
				
				}
			});
			}
			else
			{
				chkid=$(this).attr('id');
				chkval=' ';
				jQuery.ajax({
					url: ajaxurl,
					type: 'POST',
					data: {action:'unsavemeta',chkid:chkid,chkval:chkval},
					cache: false,
					dataType: 'json',
					success:function()
					{


					}
				});
			} 
		});
	});
});

jQuery(function($)
{
	jQuery('.settingbuttonremove').click(function()
	{
		remove=jQuery('#sett').val();
		if(remove=='Default Settings')
		{
			alert(bulk_obj.not_delete_default_setting);
			return false;
		}
		var chk=confirm(bulk_obj.alert_remove_txt);
		if(chk==true)
		{
			jQuery.ajax({
				url: ajaxurl,
				type: 'POST',
				data:{action:'remove',data:remove},
				success:function(response)
				{ 
					alert(bulk_obj.deleted_successfully);
					location.reload();
				}
			});
		}
	});
});
jQuery(function($)
{
	jQuery('#mwb_submit_saved_setting_display').click(function()
	{
		var settingname=jQuery('.settingname').val();
		if(settingname=='')
		{
			alert(bulk_obj.enter_setting_name);
			return false;
		}


	});
});
jQuery(function($)
{
	jQuery('#mwb_submit_saved_setting_display').click(function()
	{
		var setting_value=jQuery('#sett option:selected').text();
		if(setting_value=='')
		{
			alert(bulk_obj.select_setting_name);
			return false;

		}
		jQuery('.notice').show();

	});

});
jQuery(function($)
{
	jQuery('#savesett').click(function()
	{
		var settingname=jQuery('.settingname').val();
		if(settingname=='')
		{
			alert('Please enter a setting name');
			return false;
		}
	});
	jQuery(".viewsetting").on("click",function(e){
		e.preventDefault();
		var arg = jQuery('#sett').val();
		var Url = document.URL;
		Url = Url + '&mwbpdf_gensetting=' + arg;       
		window.open(Url,'_blank');  
	});
	jQuery(".previewposttemplate").on("click",function(e){
		e.preventDefault();
		var arg = jQuery('.mwb_post_select_tmplt').val();
		var arg2 = jQuery('#sett').val();
		var Url = document.URL;
		
		Url = Url + '&mwbpdf_posttmplt=' + arg;
		Url = Url + '&mwbpdf_gensetting=' + arg2;
		Url = Url + '&mwbpdf_preview=post';

		window.open(Url,'_blank');  
	});
	jQuery(".previewproducttemplate").on("click",function(e){
		e.preventDefault();
		var arg = jQuery('.mwb_product_select_tmplt').val();
		var arg2 = jQuery('#sett').val();
		var Url = document.URL;
		Url = Url + '&mwbpdf_producttmplt=' + arg;
		Url = Url + '&mwbpdf_gensetting=' + arg2;
		Url = Url + '&mwbpdf_preview=product';
		window.open(Url,'_blank');  
	});
	jQuery( ".previewcovertemplate" ).on("click",function(e) {
		e.preventDefault();
		jQuery(".coverpagemodal").css("display","block");
		var arg = jQuery('.mwb_cover_select_tmplt').val();
		if( arg == "tmplt1"){
			jQuery(".Cover_image_preview").attr("src", bulk_obj.path+"/asset/images/template1.png");
		}
		if( arg == "tmplt2"){
			jQuery(".Cover_image_preview").attr("src", bulk_obj.path+"/asset/images/template2.png");
		}
		if( arg == "tmplt3" ){
			jQuery(".Cover_image_preview").attr("src", bulk_obj.path+"/asset/images/template3.png");
		}
		if( arg == "tmplt4"){
			jQuery(".Cover_image_preview").attr("src", bulk_obj.path+"/asset/images/template4.png");
		}
	});
	jQuery(".close" ).on("click",function( e ){
		e.preventDefault();
		jQuery(".coverpagemodal").css("display","none");
	});
	jQuery('.comnt').click(function()
	{
		if(this.checked==true)
		{
			jQuery('.comauthimg').show();
			jQuery('.comauth').show();
			jQuery('.comdate').show();
		}
		else
		{
			jQuery('.comauthimg').hide();
			jQuery('.comauth').hide();
			jQuery('.comdate').hide();
		}
	});

});
jQuery(document).ready(function() {
	jQuery('.records').removeClass('fixed');
	var settingname=jQuery('.settingname').val();
	jQuery('.comnt').click(function()
	{
		if(this.checked==true)
		{
			jQuery('.comauthimg').show();
			jQuery('.comauth').show();
			jQuery('.comdate').show();
		}
		else
		{
			jQuery('.comauthimg').hide();
			jQuery('.comauth').hide();
			jQuery('.comdate').hide();
		}
	});
	if(jQuery('.comnt').is(":checked"))
	{
		jQuery('.comauthimg').show();
		jQuery('.comauth').show();
		jQuery('.comdate').show();
	}
	
	if(settingname!='')
	{
		jQuery( ".setting" ).val(settingname);		
	}
	jQuery('.clickfront').click(function()
	{
		if(this.checked==true){
			jQuery('.showall').show();
			jQuery('.showloggedin').show();
		
		}
		else{
			jQuery('.showall').hide();
			jQuery('.showloggedin').hide();
		
		}
	});
	if(jQuery('.clickfront').is(":checked"))
	{
		jQuery('.showall').show();
		jQuery('.showloggedin').show();
		
	}
	
	/////////////////////////////////////
	jQuery('.showloggedin').click(function()
	{
		if(jQuery('.showloggedin').children('input').is(":checked")){
			
			jQuery('.showemail').show();
		}
		else{
			
			jQuery('.showemail').hide();
		}
	});
	if(jQuery('.showloggedin').children('input').is(":checked"))
	{
		jQuery('.showemail').show();
	}

});
function removelog(id)
{
	jQuery.ajax({
		url: ajaxurl,
		type: 'POST',
		data:{
			action:'removelog',
			id:id,
			remove_nonce : bulk_obj.mwb_pdf_nonce,
		},
		success:function(response)
		{
			alert(response);
			location.reload();
		}
	});
}
jQuery(function ($) {
	
	current_value = $('#display_file_name').val()
	if (current_value == 'custom_name'){
		$('#display_file_name_input').show()
	}else {
		$('#display_file_name_input').hide()
	}
		$(document).on('change','#display_file_name',function(){
	
			current_value = $('#display_file_name').val()
			
			if (current_value == 'custom_name'){
				$('#display_file_name_input').show()
			}else {
				$('#display_file_name_input').hide()
			}
		})
	
	
	
});
	

document.addEventListener('DOMContentLoaded', () => {
	document.querySelectorAll('.wps-switch input').forEach((el) => {
		el.addEventListener('change', function () {
			const source = this.dataset.source;
			const value = this.checked ? 'on' : 'off';
			const toast = document.getElementById('wps-toast-msg');

			jQuery.ajax({
				url    : ajaxurl,
				method : 'post',
				data   : {
					action   : 'wps_pgfw_save_embed_source',
					nonce    : bulk_obj.mwb_pdf_nonce,
					is_enable: value,
					souce_name: source,
				},
				success: function (msg) {
					console.log('ajax run');
					console.log(msg);
					if (msg.success) {
						toast.textContent = '✅ Setting saved!';
						toast.style.display = 'inline-block';
						setTimeout(() => {
							toast.style.display = 'none';
						}, 2000);
					}
				}, error : function() {
					console.log('error encounter');
				} 
			});
		});
	});
});
