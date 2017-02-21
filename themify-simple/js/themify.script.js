var FixedHeader = {},
	themifyScript = {};

// JQUERY FUNCTIONS
(function($){
	
	// Fixed Header /////////////////////////
	FixedHeader = {
		headerHeight: 0,
		init: function() {
			if( '' !== themifyScript.fixedHeader ) {
				this.headerHeight = $('#headerwrap').outerHeight(true);
				this.activate();
				$(window).on('scroll touchstart.touchScroll touchmove.touchScroll', this.activate);
			}
			$('#pagewrap').css('paddingTop', Math.floor( this.headerHeight ));
			$(window).on( 'throttledresize', function() {
				$('#pagewrap').css('paddingTop', Math.floor( $('#headerwrap').outerHeight(true) ));
			});
		},
		activate: function() {
			var $window = $(window),
                            scrollTop = $window.scrollTop(),
                            $headerWrap = $('#headerwrap');
			$('#pagewrap').css('paddingTop', Math.floor( this.headerHeight ));
			if( scrollTop >= FixedHeader.headerHeight ) {
				if ( ! $headerWrap.hasClass( 'fixed-header' ) ) {
					FixedHeader.scrollEnabled();
				}
			} else {
				if ( $headerWrap.hasClass( 'fixed-header' ) ) {
					FixedHeader.scrollDisabled();
				}
			}
		},
		scrollDisabled: function() {
			$('#pagewrap').css('paddingTop', Math.floor( this.headerHeight ));
			$('#headerwrap').removeClass('fixed-header');
			$('#header').removeClass('header-on-scroll');
			$('body').removeClass('fixed-header-on');
		},
		scrollEnabled: function() {
			$('#headerwrap').addClass('fixed-header');
			$('#header').addClass('header-on-scroll');
			$('body').addClass('fixed-header-on');
		}
	};
	
	
	// DOCUMENT READY				
	jQuery(document).ready(function($){
		FixedHeader.init();
		var $body = $('body');

		/////////////////////////////////////////////
		// Scroll to top 							
		/////////////////////////////////////////////
		$('.back-top a').click(function () {
			$('body,html').animate({
				scrollTop: 0
			}, 800);
			return false;
		});
		
		/////////////////////////////////////////////
		// Toggle main nav on mobile 							
		/////////////////////////////////////////////
		// touch dropdown menu
		if( typeof jQuery.fn.themifyDropdown === 'function' ) {
			$( '#main-nav' ).themifyDropdown();
		}
		
		$('#menu-icon').themifySideMenu({
			close: '#menu-icon-close',
			side: 'left'
		});
		
		var $overlay = $( '<div class="body-overlay">' );
		$body.append( $overlay ).on( 'sidemenushow.themify', function () {
			$overlay.addClass( 'body-overlay-on' );
		}).on( 'sidemenuhide.themify', function () {
			$overlay.removeClass( 'body-overlay-on' );
		}).on( 'click.themify touchend.themify', '.body-overlay', function () {
			$( '#menu-icon' ).themifySideMenu( 'hide' );
		}); 

		$(window).resize(function(){
			if( $( '#menu-icon' ).is(':visible') && $('#mobile-menu').hasClass('sidemenu-on')){
				$overlay.addClass( 'body-overlay-on' );
			}
			else{
				 $overlay.removeClass( 'body-overlay-on' );
			}
		});
		
		// Reset slide nav width
		$(window).resize(function(){
			var viewport = $(window).width();
			if (viewport > 780) {
				$('body').removeAttr('style');
			}
		});

	});

	// WINDOW LOAD
	jQuery(window).load(function(){
		
		// EDGE MENU //
		jQuery(function ($) {
			$("#main-nav li").on('mouseenter mouseleave', function (e) {
				if ($('ul', this).length) {
					var elm = $('ul:first', this);
					var off = elm.offset();
					var l = off.left;
					var w = elm.width();
					var docW = $(window).width();
					var isEntirelyVisible = (l + w <= docW);

					if (!isEntirelyVisible) {
						$(this).addClass('edge');
					} else {
						$(this).removeClass('edge');
					}

				}
			});
		});
		
	});

})(jQuery);