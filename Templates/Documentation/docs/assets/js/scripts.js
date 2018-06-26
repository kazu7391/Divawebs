/* Scripts initialization */
/*=======================================================================*/

(function(jQuery) {
	"use strict";
	
	jQuery(document).ready(function(){
		
		jQuery('#back-top a').click(function () {
			jQuery('body,html').animate({
				scrollTop: 0
			}, 400);
			return false;
		});

		// slide-down-box-------------------------------	
		jQuery('li:first-child', "#nav").addClass('first');
		jQuery('li:last-child', "#nav").addClass('last');

		jQuery(".slide-down dt i").click(function(){
			if(jQuery(this).parents("li").hasClass("open_item")){
				jQuery(".nav-item.open_item").removeClass("open_item").find('dd').slideToggle(300);
				return false;
			}
			jQuery(".nav-item.open_item").removeClass("open_item").find('dd').slideToggle(300);
			jQuery(this).parents("li").toggleClass("open_item").find('dd').slideToggle(300);
			return false;
		});
		
		//Menu item click to scroll
		jQuery('.nav-item a').each(function(){
			jQuery(this).click(function(event){
				event.preventDefault();
				var thisId = jQuery(this).attr('href');
				
				if(jQuery(thisId).length > 0){
					var thistopOffset = jQuery(thisId).offset().top;
					jQuery("html, body").animate({ scrollTop: thistopOffset }, 400);
				}
			});
		});
		
		jQuery('.mobile-menu').click(function(){
			if(jQuery('#nav_container').hasClass('show')){
				jQuery('#nav_container').removeClass('show');
			} else {
				jQuery('#nav_container').addClass('show');
			}
		});
		//jQuery('body').append('<div id="debug" style="position: fixed;background:#fff;z-index:1000;top: 0;left: 0;"></div>');
	});
	
	// Scroll
	var currentP = 0;
	jQuery(window).scroll(function(){
		var headerH = jQuery('.header').height();
		
		headerH+=44; //44 is padding top of content
		
		var scrollP = jQuery(window).scrollTop();
		
		if(jQuery(window).width() > 480){
			if(scrollP != currentP){
				//Back to top
				if(scrollP >= headerH){
					jQuery('#back-top').addClass('show');
					jQuery('.affix-top').addClass('ontop');
				} else {
					jQuery('#back-top').removeClass('show');
					jQuery('.affix-top').removeClass('ontop');
				}
				currentP = jQuery(window).scrollTop();
			}
		}
		//active menu
		jQuery('#nav .nav-item dt a').each(function () {
			var thisItem = jQuery(this);
			var thisId2 = jQuery(this).attr('href');
			if(jQuery(thisId2).length > 0){
				var thistopOffset2 = jQuery(thisId2).offset().top - 15;
			
				if (thistopOffset2 <= currentP && thistopOffset2 + jQuery(thisId2).outerHeight() > currentP) {
					
					jQuery(this).parents('li').addClass("act_item open_item");
					if(jQuery(this).parents('li').find('dd').length > 0){
						jQuery(this).parents("li").find('dd').slideDown(300);
					}
				}
				else{
					jQuery(this).closest('li').removeClass("act_item open_item");
					jQuery(this).parents("li").find('dd').slideUp(300);
				}
			}
			
		});
		jQuery('#nav .nav-item dd a').each(function () {
			var thisItem = jQuery(this);
			var thisId2 = jQuery(this).attr('href');
			if(jQuery(thisId2).length > 0){
				var thistopOffset2 = jQuery(thisId2).offset().top - 15;
			
				if (thistopOffset2 <= currentP && thistopOffset2 + jQuery(thisId2).outerHeight() > currentP) {
					jQuery(this).parents('li').addClass("act_item open_item");
					if(jQuery(this).parents('li').find('dd').length > 0){
						jQuery(this).closest("li").addClass('current');
					}
				}
				else{
					jQuery(this).closest("li").removeClass('current');
				}
			}
			
		});
	});
})(jQuery);