
(function($) {
      	$(document).ready(function(){
	
	$('ul.tab-navigation li a').click(function(){
		var tab_id = $(this).attr('data-tab');

		$('ul.tab-navigation li a').removeClass('active');
		$('.tab').removeClass('active');

		$(this).addClass('active');
		$("#"+tab_id).addClass('active');
	});

});	
    $(function() {
        $.fn.manilogo = function(options) {
            var selector = $(this).selector; // Get the selector
            // Set default options
            var defaults = {
                'preview' : '.preview-upload',
                'text'    : '.text-upload',
                'button'  : '.button-upload',
            };
            var options  = $.extend(defaults, options);
        	// When the Button is clicked...
            $(options.button).click(function() {  
                // Get the Text element.
                var text = $(this).siblings(options.text);
                
                // Show WP Media Uploader popup
                tb_show('Defult cover', 'media-upload.php?referer=manilogo&type=image&TB_iframe=true&post_id=0', false);
        		
        		// Re-define the global function 'send_to_editor'
        		// Define where the new value will be sent to
                window.send_to_editor = function(html) {
                	// Get the URL of new image
                    var src = $('img', html).attr('src');
                    // Send this value to the Text field.
                    text.attr('value', src).trigger('change'); 
                    tb_remove(); // Then close the popup window
                }
                return false;
            });

            $(options.text).bind('change', function() {
            	// Get the value of current object
                var url = this.value;
                // Determine the Preview field
                var preview = $(this).siblings(options.preview);
                // Bind the value to Preview field
                $(preview).attr('src', url);
            });
        }

        // Usage
        $('.button').manilogo(); // Use as default option.

    });
	
	$(".cover").each(function(){
        
		var $coverUrl = $("#cover_profile"),
			$removeBtn = $(".remove-btn", this),
			$CoverImg = $(".preview-upload", this);
			
		remove_Cover();
		$(document).hover(function(){
			if(!$removeBtn.hasClass("active")){
				remove_Cover();
			}
		});
		
		function remove_Cover(){
			if(!$CoverImg.attr("src") == ""){
				$removeBtn.show().addClass("active");
				$removeBtn.click(function(){
					$CoverImg.removeAttr("src", "");
					$coverUrl.removeAttr("value");
					$(this).hide();
					return false
				});
			}
		}
	
	});
	$(".cover-group").each(function(){
        var $covergroupUrl = $("#cover_group"),		
			$removeBtn = $(".remove-btn", this),
			$CoverImg = $(".preview-upload", this);
			
		remove_Cover();
		$(document).hover(function(){
			if(!$removeBtn.hasClass("active")){
				remove_Cover();
			}
		});
		
		function remove_Cover(){
			if(!$CoverImg.attr("src") == ""){
				$removeBtn.show().addClass("active");
				$removeBtn.click(function(){
					$CoverImg.removeAttr("src", "");
					$covergroupUrl.removeAttr("value");
					$(this).hide();
					return false
				});
			}
		}
	
	});
	
		$(".cover-avatar").each(function(){
        var $coveravatarUrl = $("#bp_cover_avatar"),		
			$removeBtn = $(".remove-btn", this),
			$CoverImg = $(".preview-upload", this);
			
		remove_Cover();
		$(document).hover(function(){
			if(!$removeBtn.hasClass("active")){
				remove_Cover();
			}
		});
		
		function remove_Cover(){
			if(!$CoverImg.attr("src") == ""){
				$removeBtn.show().addClass("active");
				$removeBtn.click(function(){
					$CoverImg.removeAttr("src", "");
					$coveravatarUrl.removeAttr("value");
					$(this).hide();
					return false
				});
			}
		}
	
	});
}(jQuery));