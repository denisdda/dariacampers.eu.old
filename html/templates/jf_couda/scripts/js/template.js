/**
 * @package     Joomla.Site
 * @subpackage  Templates.protostar
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @since       3.2
 */

(function($) {

		/**
     * RESPONSIVE MENU // DO NOT TOUCH! (unless you know what you're doing)
     */ 

    function start_responsive_menu() {
		var page_wrapper = $('#page-wrapper'),
        responsive_trigger = $('.zn-res-trigger'),
        zn_back_text = 'go back',
        back_text = '<li class="zn_res_menu_go_back"><span class="zn_res_back_icon icon-angle-left"></span><a href="#">'+zn_back_text+'</a></li>',
        cloned_menu = $('#main-menu > ul').clone().attr({id:"zn-res-menu", "class":""});

        var responsive_menu = cloned_menu.prependTo(page_wrapper);

        // BIND OPEN MENU TRIGGER
        responsive_trigger.click(function(e){
            e.preventDefault();
            
            responsive_menu.addClass('zn-menu-visible');
            set_height();

        });

        // ADD ARROWS TO SUBMENUS TRIGGERS
        responsive_menu.find('li:has(> ul)').addClass('zn_res_has_submenu').prepend('<span class="zn_res_submenu_trigger icon-angle-right"></span>');
        // ADD BACK BUTTONS
        responsive_menu.find('.zn_res_has_submenu > ul').addBack().prepend(back_text);

        // REMOVE BACK BUTTON LINK
        $( '.zn_res_menu_go_back' ).click(function(e){
            e.preventDefault();
            var active_menu = $(this).closest('.zn-menu-visible');
            active_menu.removeClass('zn-menu-visible');
            set_height();
            if( active_menu.is('#zn-res-menu') ) {
                page_wrapper.css({'height':'auto'});
            }
        });

        // OPEN SUBMENU'S ON CLICK
        $('.zn_res_submenu_trigger').click(function(e){
            e.preventDefault();
            $(this).siblings('ul').addClass('zn-menu-visible');
            set_height();
        });
    }

    function set_height() {
		var page_wrapper = $('#page-wrapper');
        var height = $('.zn-menu-visible').last().css({height:'auto'}).outerHeight(true),
            window_height  = $(window).height(),
            adminbar_height = 0,
            admin_bar = $('#wpadminbar');

        // CHECK IF WE HAVE THE ADMIN BAR VISIBLE
        if(height < window_height) {
            height = window_height;
            if ( admin_bar.length > 0 ) {
                adminbar_height = admin_bar.outerHeight(true);
                height = height - adminbar_height;
            }
        }

        $('.zn-menu-visible').last().attr('style','');
        page_wrapper.css({'height':height});
    }

    // MAIN TRIGGER FOR ACTIVATING THE RESPONSIVE MENU   
	
    function triggerMenu() {
		var menu_activated = false;
		var page_wrapper = $('#page-wrapper');
        if ( $(window).width() < 1200 ) {
            if ( !menu_activated ){
                start_responsive_menu();
                menu_activated = true;
            }
            page_wrapper.addClass('zn_res_menu_visible');
        } else {
            // WE SHOULD HIDE THE MENU
            $('.zn-menu-visible').removeClass('zn-menu-visible');
            page_wrapper.css({'height':'auto'}).removeClass('zn_res_menu_visible');
        }
    }

    $(window).scroll(function(event){
		
		if ($(this).scrollTop() > 100) {
            $("#back-top").fadeIn();
        } else {
            $("#back-top").fadeOut();
        }
		
    });

	$(window).load(function() {	
		var masonry_portfolio_selectors = $('.portfolio-filter .filter-item');

		if(masonry_portfolio_selectors!='undefined'){
			var masonry_portfolio = $('.portfolio-wrapper');
			masonry_portfolio.imagesLoaded( function(){
				 masonry_portfolio.isotope({
					itemSelector: '.portfolio-item',
					layoutMode: 'fitRows'
				});
			});

			masonry_portfolio_selectors.on('click', function(e){
				e.preventDefault();
				masonry_portfolio_selectors.removeClass('active');
				$(this).addClass('active');
				var selector = $(this).attr('data-filter');
				masonry_portfolio.isotope({ filter: selector });
				return false;
			});
		}
		
		var masonry_blog = $('.blog-masonry');
		masonry_blog.imagesLoaded( function(){
			masonry_blog.isotope({
				itemSelector: '.blog-item',
			});
		});
		
		var masonry_gallery = $('.gallery-masonry');
		masonry_gallery.imagesLoaded( function(){
			masonry_gallery.isotope({
				itemSelector: '.gallery-item',
			});
		});
		
		$('.owl-carousel').owlCarousel({
			dots: false,
			nav: true,
			navText: ['<i class="icon-angle-left animation"></i>','<i class="icon-angle-right animation"></i>'],
			responsive: {
				0:{
					items:1
				},
				600:{
					items:3
				},
				1000:{
					items:4
				}
			}
		});
	});
	
	$(document).click(function () {
        $('.searchPanel .search').removeClass('active');
    });
	
	$(document).ready(function() {
		
		$('body').css('padding-top', $('#header').outerHeight());
		
		if($("#slideshow-fullscreen").length) {
			$('body').css('padding-top', 0);
			$("#slideshow-fullscreen").css({ 'height' : $(window).height()});
		}
		
		$('#countdown-wrap').css('height', $(window).height() - $('#header').outerHeight());
		
		$('.btn-slide').click(function(){
			$('#panel').slideToggle();
			return false;
		});
		
		$('.searchPanel').click(function (e) {
			e.stopPropagation();
		});
		
		$('.searchPanel span').click(function (e) {
			e.stopPropagation();
			$('.searchPanel .search').toggleClass('active');
		});
		
		$('.nivo-image').nivoLightbox();
		
		$("#back-top").hide();
		
		$("#back-top").children('a').click(function () {
            $('body,html').animate({
                scrollTop: 0
            }, 800);
            return false;
        });
		
		triggerMenu();
		
	});
	
	$(window).resize(function(){
		$('body').css('padding-top', $('#header').outerHeight());
		if($("#slideshow-fullscreen").length) {
			$('body').css('padding-top', 0);
			$("#slideshow-fullscreen").css({ 'height' : $(window).height()});
		}		
		$('#countdown-wrap').css('height', $(window).height() - $('#header').outerHeight());
	});

})(jQuery);