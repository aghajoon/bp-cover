var $j = jQuery.noConflict();

function delete_pic_cover(activity_id, adminUrl){
    jQuery('#'+activity_id).children(".delete-pic").html("");
    jQuery('#'+activity_id ).children(".bpci-loading").show(); 
    jQuery.ajax({
        type: 'post',
        url: adminUrl,
        data: { action: "delete_pic_cover", activity_id:activity_id },
        success:
        function(data) {
        	jQuery('#'+activity_id).parent().hide();        	
        }
     });  
}
 function select_pic_rtmedia_for_cover(photo_id, adminUrl){
    jQuery.ajax({
        type: 'post',
        url: adminUrl,
        data: { action: "select_pic_rtmedia_for_cover", photo_id:photo_id, },
        success:
        function(data) {
        	 location.reload();        	
        }
     });  
}

 function select_pic_for_cover(activity_id, adminUrl){
 jQuery('#'+activity_id ).children(".bpci-loading").show(); 
    jQuery.ajax({
        type: 'post',
        url: adminUrl,
        data: { action: "select_pic_for_cover", activity_id:activity_id, },
        success:
        function(data) {
        	 location.reload();        	
        }
     });  
}
(function($){
    $('.btn-save').hide();
    $('.edit-cover').click(function() {
	$('#profile-mass').fadeIn("slow");
    $(".img-profile-header-background").css('cursor', 'pointer');
    var y1 = $('.image-upload-container').height();
    var y2 = $('.img-profile-header-background').height();
    var x1 = $('.image-upload-container').width();
    var x2 = $('.img-profile-header-background').width();
    $(".img-profile-header-background").draggable({
        scroll: false,
                                  axis: "y",
                                  drag: function(event, ui) {
                                      if(ui.position.top >= 0)
                                      {
                                          ui.position.top = 0;
                                      }
                                      else if(ui.position.top <= y1 - y2)
                                      {
                                          ui.position.top = y1 - y2;
                                      }
    
            },
        stop: function(event, ui) {
            $('input[name=id]').val(ui.position.top);
			$('.edit-cover').hide();
			$('#profile-mass').fadeOut("slow");
			$('.btn-save').show();
        }
    });
 });		      

  
  $(".btn-save").click(function () {
 
  var $selectedInput = $("input[name=id]");
  $.ajax({
     url: ajaxurl,
	 type: 'POST',
	 data: { 
	 action:'bp_cover_position',
	id : $selectedInput.val()    
	 },
	 beforeSend:  function() {                
               $('#bpci-polaroid-upload-banner').addClass('bpci-loading');
            },
	success: function(value) {
            jQuery(this).html(value);
			$('#bpci-polaroid-upload-banner').removeClass('bpci-loading');            
			$('#mass-drag').fadeIn("slow");
			$('.btn-save').hide();
			$('.edit-cover').show();			
			//window.setTimeout(function(){
			//			$('#mass-drag').fadeOut("slow");                        
			//		}, 2000);
			location.reload();
          }     
  });
  
	 return false; 
  });

   $('.uploadBox').each(function() {
   if ($(this).attr('id') == "profilefileupload") {
   $("input[name=avatar_filename]").change(function() {

   var file = $('#avatar_pic').get(0).files[0];
  // var max_file_size = 2;//size in MO
   
   /* check file type, we only want images */
   if( file.type.indexOf('image') == -1 ) {
      alert('Please select an image file only.');
      return false;
   }   
 
   name = file.name;
   size = file.size;
   type = file.type;
   
  // sizeInMo = Math.round((size / 1024) * 100) / 100 ;
   
   /* check file size */
  // if( sizeInMo > max_file_size ){
   //   alert('Your image is too big, please reduce it (max file size : ' + max_file_size + 'MO)')
   //   return false;
 //  }
   
   var reader = new FileReader();
   
   $('#bpci-polaroid-upload-avatar').addClass('bpci-loading');
   
   reader.onload = (function(theFile) {
      return function(e) {
         bpciImageUpload(e.target.result, type, name,size);
         return false;
    };
   })(file);
 
   reader.readAsDataURL(file);
   
   return false;
});
 
function bpciImageUpload( img, type, name,size ) {
   /*
   * ajaxurl is already defined in BuddyPress
   * if you're not using BuddyPress, you can define it this way :
   * ajaxurl = "<?php echo admin_url('admin-ajax.php');?>";
   */
   
   $.post( ajaxurl, {
      action: 'bp_caver_avatar_handle_upload',      
      'encodedimg': img,
      'imgtype':type,
      'imgname':name,
	  'imgsize':size

   },
   function(response) {     
      if( response[0] != "0" ) {
         sendToContentEditableavatar( response[1], response[2]);
      }else {alert(response[1]);
	    $('#bpci-polaroid-upload-avatar').removeClass('bpci-loading');
		}
   }, 'json');
         
}
 
function sendToContentEditableavatar(fullimage, resizedimage){						
						jQuery.ajax({
							type: 'POST',
							url: ajaxurl,
							data: {"action": "bp_avatar_refresh"},							
							success: function(data){	
								$(".ava").html(data);		
								}
						});
						}	
  } else if ($(this).attr('id') == "bannerfileupload") {
   $("input[name=cover_filename]").change(function() {   
   var file = $('#cover_pic').get(0).files[0];
  // var max_file_size = 2;//size in MO
   
   /* check file type, we only want images */
   if( file.type.indexOf('image') == -1 ) {
      alert('Please select an image file only.');
      return false;
   }   
 
   name = file.name;
   size = file.size ;
   type = file.type;
   
  // sizeInMo = Math.round((size / 1024) * 100) / 100 ;
   
   /* check file size */
   //if( sizeInMo > max_file_size ){
  //   alert('Your image is too big, please reduce it (max file size : ' + max_file_size + 'MO)')
  //    return false;
 // }
   
   var reader = new FileReader();
   
   $('#bpci-polaroid-upload-banner').addClass('bpci-loading');
   
   reader.onload = (function(theFile) {
      return function(e) {
         bpciImageUploadava(e.target.result, type, name,size);
         return false;
    };
   })(file);
 
   reader.readAsDataURL(file);
   
   return false;
});
 
function bpciImageUploadava( img, type, name,size ) {
   /*
   * ajaxurl is already defined in BuddyPress
   * if you're not using BuddyPress, you can define it this way :
   * ajaxurl = "<?php echo admin_url('admin-ajax.php');?>";
   */
   
   $.post( ajaxurl, {
      action: 'bp_cover_handle_upload',     
      'encodedimg': img,
      'imgtype':type,
      'imgname':name,
	  'imgsize':size
	  	
	  
   },
   function(response) {     
      if( response[0] != "0" ) {
         sendToContentEditable( response[1], response[2]);		 
      }else {
	  alert(response[1]);
	  $('#bpci-polaroid-upload-banner').removeClass('bpci-loading');
	  };
   }, 'json');
         
}

function sendToContentEditable(fullimage, resizedimage){						
		jQuery.ajax({
				type: 'POST',
				url: ajaxurl,
				data: {"action": "bp_cover_refresh"},							
					   success: function(data){								
						 //$(".image-upload-container").html(data);
						 location.reload();
								}
					 });
		}						
   }
});					


  $(".btn-remove").click(function () {
  $.ajax({
     url: ajaxurl,
	 type: 'post',
	 data: {'action': 'bp_cover_delete' },
	 beforeSend:  function() {                
               $('#bpci-polaroid-upload-banner').addClass('bpci-loading');
            },
	success: function(data) {
		// $("#profile-mass").toggleClass('mass');
		  $('#bpci-polaroid-upload-banner').removeClass('bpci-loading');
location.reload();
        }
  });
	 return false; 
  });


	
})(jQuery);

