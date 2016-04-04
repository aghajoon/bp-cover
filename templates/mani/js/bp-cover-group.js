var $j = jQuery.noConflict();
function delete_pic_cover_group(activity_id, adminUrl){
    jQuery.ajax({
        type: 'post',
        url: adminUrl,
        data: { action: "delete_pic_cover_group", activity_id:activity_id },
        success:
        function(data) {
        	jQuery('#'+activity_id).parent().hide();        	
        }
     });  
}
 function select_cover_for_group(activity_id, adminUrl){
 jQuery('#'+activity_id ).children(".delete-loader").show(); 
    jQuery.ajax({
        type: 'post',
        url: adminUrl,
        data: { action: "select_cover_for_group", activity_id:activity_id, },
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
    $(".bp-cover-top").css('cursor', 'pointer');
    var y1 = $('.cover').height();
    var y2 = $('.bp-cover-top').height();
    var x1 = $('.cover').width();
    var x2 = $('.bp-cover-top').width();
    $(".bp-cover-top").draggable({
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
	 action:'bp_cover_group_position',
	id : $selectedInput.val()    
	 },
	 beforeSend:  function() {                
               $('#bpci-polaroid-upload-group').addClass('bpci-loading');
            },
	success: function(value) {
            jQuery(this).html(value);
			$('#bpci-polaroid-upload-group').removeClass('bpci-loading');
            location.reload();
          }     
  });
  
	 return false; 
  });

   $('.uploadBox').each(function() {
   if ($(this).attr('id') == "groupfileupload") {
   $("input[name=group_filename]").change(function() {

   var file = $('#group_pic').get(0).files[0];
   //var max_file_size = 2;//size in MO
   
   /* check file type, we only want images */
   if( file.type.indexOf('image') == -1 ) {
      alert('Please select an image file only.');
      return false;
   }   
 
   name = file.name;
   size = file.size ;
   type = file.type;
   
   //sizeInMo = Math.round((size / 1024) * 100) / 100 ;
   
   /* check file size */
  // if( sizeInMo > max_file_size ){
   //   alert('Your image is too big, please reduce it (max file size : ' + max_file_size + 'MO)')
  //    return false;
 //  }
   
   var reader = new FileReader();
   
   $('#bpci-polaroid-upload-group').addClass('bpci-loading');
   
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
      action: 'bp_cover_group_handle_upload',      
      'encodedimg': img,
      'imgtype':type,
      'imgname':name,
	   'imgsize':size
   },
   function(response) {     
      if( response[0] != "0" ) {
         sendToContentEditablegroup( response[1], response[2]);
      }
      else  {alert(response[1]);
	    $('#bpci-polaroid-upload-group').removeClass('bpci-loading');
		}
   }, 'json');
         
}
 
function sendToContentEditablegroup(fullimage, resizedimage){						
						jQuery.ajax({
							type: 'POST',
							url: ajaxurl,
							data: {"action": "bp_group_cover_refresh"},							
							success: function(data){	
								$(".group-cover").html(data);		
								}
						});
						}	
   } 
});					


$("#uploadgroup").click(function(){
        $(this).next().trigger('click');
    });
   
	
})(jQuery);

