var $j = jQuery.noConflict();



(function($){
var appendthis =  ("<div class='modal-overlay'><div id='loading'></div></div>");
var appendcover =  ("<div class='modalbox '></div>");
	$('a[data-modal-id]').click(function(e) {
		e.preventDefault();
		el = $(this);
    $("body").append(appendthis);
	$("body").append(appendcover);
    $(".modal-overlay").fadeTo(500, 0.8);   
		var modalBox = $(this).attr('data-modal-id');
		$('#'+modalBox).fadeIn($(this).html());		
    $.ajax({
     url: ajaxurl,
	 type: 'post',
	 data: {'action': 'bp_cover_gallery_pop' },
	 beforeSend:  function() {                              
				$('#loading').addClass('bpci-loading');
            },		
            success: function (html) {
                  $('#loading').removeClass('bpci-loading');
                  $(".modalbox").html(html);
				  $(window).resize();
				  $(".modal-overlay,.js-modal-close").click(function() {
                         $(".modal-box, .modal-overlay").fadeOut(500, function() {
                         $(".modal-overlay,.modalbox").remove();
						 }); 
						 });
				  }
			});
	});   

 $(window).resize(function() {
    $(".modal-box").css({
        top: ($(window).height() - $(".modal-box").outerHeight()) / 2,
        left: ($(window).width() - $(".modal-box").outerWidth()) / 2
    });
});
 



// override standard drag and drop behavior

$("#uploadcover").click(function(){
        $(this).next().trigger('click');
    });
$("#uploadavatar").click(function(){
        $(this).next().trigger('click');
    });
    // show buttons at image rollover
    $('.profile-user-photo-container').mouseover(function() {
        $('#profile-image-upload-buttons').fadeIn("slow");
    })

    // show buttons also at buttons rollover (better: prevent the mouseleave event)
    $('#profile-image-upload-buttons').mouseover(function() {
        $('#profile-image-upload-buttons').fadeIn("slow");
    })

    // hide buttons at image mouse leave
    $('.profile-user-photo-container').mouseleave(function() {
        $('#profile-image-upload-buttons').hide();
    })
	
    // show buttons at image rollover
    $('.panel .panel-profile-header').mouseover(function() {
        $('#banner-image-upload-buttons').fadeIn("slow");
    })
 

    // hide buttons at image mouse leave
    $('.panel .panel-profile-header').mouseleave(function() {
        $('#banner-image-upload-buttons').hide();
    })
	
	
})(jQuery);

