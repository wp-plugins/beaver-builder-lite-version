(function($){

	if(typeof FLBuilderLayout != 'undefined') {
		return;
	}
	
	/**
	 * Helper class with generic logic for a builder layout.
	 *
	 * @class FLBuilderLayout
	 * @since 1.0
	 */
	FLBuilderLayout = {
		
		/**
		 * Initializes a builder layout.
		 *
		 * @since 1.0
		 * @method init
		 */ 
		init: function()
		{
			// Destroy existing layout events.
			FLBuilderLayout._destroy();
			
			// Init CSS classes.
			FLBuilderLayout._initClasses();
			
			// Init anchor links.
			FLBuilderLayout._initAnchorLinks();
			
			// Init the browser hash.
			FLBuilderLayout._initHash();
			
			// Init backgrounds.
			FLBuilderLayout._initBackgrounds();
			
			// Init module animations.
			FLBuilderLayout._initModuleAnimations();
			
			// Init forms.
			FLBuilderLayout._initForms();
		},
		
		/**
		 * Unbinds builder layout events.
		 *
		 * @since 1.0
		 * @access private
		 * @method _destroy
		 */ 
		_destroy: function()
		{
			var win = $(window);
			
			win.off('scroll.fl-bg-parallax');
			win.off('resize.fl-bg-video');
		},
		
		/**
		 * Checks to see if the current device has touch enabled.
		 *
		 * @since 1.0
		 * @access private
		 * @method _isTouch
		 * @return {Boolean}
		 */ 
		_isTouch: function()
		{
			if(('ontouchstart' in window) || (window.DocumentTouch && document instanceof DocumentTouch)) {
				return true;
			}
			
			return false;
		},
		
		/**
		 * Initializes builder body classes.
		 *
		 * @since 1.0
		 * @access private
		 * @method _initClasses
		 */ 
		_initClasses: function()
		{
			// Add the builder body class.
			$('body').addClass('fl-builder');
			
			// Add the builder touch body class.
			if(FLBuilderLayout._isTouch()) {
				$('body').addClass('fl-builder-touch');
			}
		},
		
		/**
		 * Initializes builder node backgrounds that require
		 * additional JavaScript logic such as parallax.
		 *
		 * @since 1.1.4
		 * @access private
		 * @method _initBackgrounds
		 */ 
		_initBackgrounds: function()
		{
			var win = $(window);
			
			// Init parallax backgrounds.
			if($('.fl-row-bg-parallax').length > 0 && !FLBuilderLayout._isTouch()) {
				FLBuilderLayout._scrollParallaxBackgrounds();
				FLBuilderLayout._initParallaxBackgrounds();
				win.on('scroll.fl-bg-parallax', FLBuilderLayout._scrollParallaxBackgrounds);
			}
			
			// Init video backgrounds.
			if($('.fl-bg-video').length > 0) {
				FLBuilderLayout._resizeBgVideos();
				win.on('resize.fl-bg-video', FLBuilderLayout._resizeBgVideos);
			}
		},
		
		/**
		 * Initializes all parallax backgrounds in a layout.
		 *
		 * @since 1.1.4
		 * @access private
		 * @method _initParallaxBackgrounds
		 */ 
		_initParallaxBackgrounds: function()
		{
			$('.fl-row-bg-parallax').each(FLBuilderLayout._initParallaxBackground);
		},
		
		/**
		 * Initializes a single parallax background.
		 *
		 * @since 1.1.4
		 * @access private
		 * @method _initParallaxBackgrounds
		 */ 
		_initParallaxBackground: function()
		{
			var row     = $(this),
				content = row.find('.fl-row-content-wrap'),
				src     = row.data('parallax-image'),
				img     = new Image();    
				
			if(typeof src != 'undefined') {
			 
				$(img).on('load', function() {
					content.css('background-image', 'url(' + src + ')');
				});
				
				img.src = src;
			}
		},
		
		/**
		 * Fires when the window is scrolled to adjust
		 * parallax backgrounds.
		 *
		 * @since 1.1.4
		 * @access private
		 * @method _scrollParallaxBackgrounds
		 */ 
		_scrollParallaxBackgrounds: function()
		{
			$('.fl-row-bg-parallax').each(FLBuilderLayout._scrollParallaxBackground);
		},
		
		/**
		 * Fires when the window is scrolled to adjust
		 * a single parallax background.
		 *
		 * @since 1.1.4
		 * @access private
		 * @method _scrollParallaxBackground
		 */ 
		_scrollParallaxBackground: function()
		{
			var win     = $(window),
				row     = $(this),
				content = row.find('.fl-row-content-wrap'),
				speed   = row.data('parallax-speed'),
				offset  = content.offset(),
				yPos    = -((win.scrollTop() - offset.top) / speed);
				
			content.css('background-position', 'center ' + yPos + 'px');
		},
		
		/**
		 * Fires when the window is resized to resize
		 * all video backgrounds.
		 *
		 * @since 1.1.4
		 * @access private
		 * @method _resizeBgVideos
		 */ 
		_resizeBgVideos: function()
		{
			$('.fl-bg-video').each(FLBuilderLayout._resizeBgVideo);
		},
		
		/**
		 * Fires when the window is resized to resize
		 * a single video background.
		 *
		 * @since 1.1.4
		 * @access private
		 * @method _resizeBgVideo
		 */ 
		_resizeBgVideo: function()
		{
			if ( 0 === $( this ).find( 'video' ).length ) {
				return;
			}
			
			var wrap        = $(this),
				wrapHeight  = wrap.outerHeight(),
				wrapWidth   = wrap.outerWidth(),
				vid         = wrap.find('video'),
				vidHeight   = vid.data('height'),
				vidWidth    = vid.data('width'),
				newWidth    = wrapWidth,
				newHeight   = Math.round(vidHeight * wrapWidth/vidWidth),
				newLeft     = 0,
				newTop      = 0;
				
			if(vidHeight == '' || vidWidth == '') {
				
				vid.css({
					'left'      : '0px',
					'top'       : '0px',
					'width'     : newWidth + 'px'
				});
			}
			else {
				
				if(newHeight < wrapHeight) {
					newHeight   = wrapHeight;
					newWidth    = Math.round(vidWidth * wrapHeight/vidHeight);  
					newLeft     = -((newWidth - wrapWidth)/2);
				}
				else {
					newTop      = -((newHeight - wrapHeight)/2);
				}
				
				vid.css({
					'left'      : newLeft + 'px',
					'top'       : newTop + 'px',
					'height'    : newHeight + 'px',
					'width'     : newWidth + 'px'
				});
			}
		},
		
		/**
		 * Initializes module animations.
		 *
		 * @since 1.1.9
		 * @access private
		 * @method _initModuleAnimations
		 */ 
		_initModuleAnimations: function()
		{
			if($('.fl-builder-edit').length === 0 && typeof jQuery.fn.waypoint !== 'undefined' && !FLBuilderLayout._isTouch()) {
				$('.fl-animation').waypoint({
					offset: '80%',
					handler: FLBuilderLayout._doModuleAnimation
				});
			}
		},
		
		/**
		 * Runs a module animation.
		 *
		 * @since 1.1.9
		 * @access private
		 * @method _doModuleAnimation
		 */ 
		_doModuleAnimation: function()
		{
			var module = $(this),
				delay  = parseFloat(module.data('animation-delay'));
			
			if(!isNaN(delay) && delay > 0) {
				setTimeout(function(){
					module.addClass('fl-animated');
				}, delay * 1000);
			}
			else {
				module.addClass('fl-animated');
			}
		},
		
		/**
		 * Opens a tab or accordion item if the browser hash is set
		 * to the ID of one on the page.
		 *
		 * @since 1.6.0
		 * @access private
		 * @method _initHash
		 */ 
		_initHash: function()
		{
			var hash 			= window.location.hash.replace( '#', '' ),
				element 		= null,
				tabs			= null,
				responsiveLabel	= null,
				tabIndex		= null,
				label			= null;
			
			if ( '' != hash ) {
				
				element = $( '#' + hash );
					
				if ( element.length > 0 ) {
					
					if ( element.hasClass( 'fl-accordion-item' ) ) {
						setTimeout( function() {
							element.find( '.fl-accordion-button' ).trigger( 'click' );
						}, 100 );
					}
					if ( element.hasClass( 'fl-tabs-panel' ) ) {
						
						setTimeout( function() {
							
							tabs 			= element.closest( '.fl-tabs' );
							responsiveLabel = element.find( '.fl-tabs-panel-label' );
							tabIndex 		= responsiveLabel.data( 'index' );
							label 			= tabs.find( '.fl-tabs-labels .fl-tabs-label[data-index=' + tabIndex + ']' );
						
							if ( responsiveLabel.is( ':visible' ) ) {
								responsiveLabel.trigger( 'click' );	
							}
							else {
								label.trigger( 'click' );
							}
							
						}, 100 );
					}
				}
			}
		},
		
		/**
		 * Initializes all anchor links on the page for smooth scrolling.
		 *
		 * @since 1.4.9
		 * @access private
		 * @method _doModuleAnimation
		 */ 
		_initAnchorLinks: function()
		{
			$( 'a' ).each( FLBuilderLayout._initAnchorLink );
		},
		
		/**
		 * Initializes a single anchor link for smooth scrolling.
		 *
		 * @since 1.4.9
		 * @access private
		 * @method _doModuleAnimation
		 */ 
		_initAnchorLink: function()
		{
			var link    = $( this ),
				href    = link.attr( 'href' ),
				id      = null,
				element = null;
			
			if ( 'undefined' != typeof href && href.indexOf( '#' ) > -1 ) {
				
				try {
					
					id      = href.split( '#' ).pop();
					element = $( '#' + id );
					
					if ( element.length > 0 ) {
						if ( link.hasClass( 'fl-scroll-link' ) || element.hasClass( 'fl-row' ) || element.hasClass( 'fl-col' ) || element.hasClass( 'fl-module' ) ) {
							$( link ).on( 'click', FLBuilderLayout._scrollToElementOnLinkClick );
						}
						if ( element.hasClass( 'fl-accordion-item' ) ) {
							$( link ).on( 'click', FLBuilderLayout._scrollToAccordionOnLinkClick );
						}
						if ( element.hasClass( 'fl-tabs-panel' ) ) {
							$( link ).on( 'click', FLBuilderLayout._scrollToTabOnLinkClick );
						}
					}
				}
				catch( e ) {}
			}
		},
		
		/**
		 * Scrolls to an element when an anchor link is clicked.
		 *
		 * @since 1.4.9
		 * @access private
		 * @method _scrollToElementOnLinkClick
		 * @param {Object} e An event object.
		 */ 
		_scrollToElementOnLinkClick: function( e, callback )
		{
			var element = $( '#' + $( this ).attr( 'href' ).split( '#' ).pop() ),
				dest    = 0,
				win     = $( window ),
				doc     = $( document );
				
			if ( element.length > 0 ) {
			
				if ( element.offset().top > doc.height() - win.height() ) {
					dest = doc.height() - win.height();
				} 
				else {
					dest = element.offset().top - 100;
				}
	
				$( 'html, body' ).animate( { scrollTop: dest }, 1000, 'swing', callback );
				
				e.preventDefault();
			}
		},
		
		/**
		 * Scrolls to an accordion item when a link is clicked.
		 *
		 * @since 1.5.9
		 * @access private
		 * @method _scrollToAccordionOnLinkClick
		 * @param {Object} e An event object.
		 */ 
		_scrollToAccordionOnLinkClick: function( e )
		{
			var element = $( '#' + $( this ).attr( 'href' ).split( '#' ).pop() );
				
			if ( element.length > 0 ) {
			
				var callback = function() {
					if ( element ) {
						element.find( '.fl-accordion-button' ).trigger( 'click' );	
						element = false;
					}
				};
				
				FLBuilderLayout._scrollToElementOnLinkClick.call( this, e, callback );
			}
		},
		
		/**
		 * Scrolls to a tab panel when a link is clicked.
		 *
		 * @since 1.5.9
		 * @access private
		 * @method _scrollToTabOnLinkClick
		 * @param {Object} e An event object.
		 */ 
		_scrollToTabOnLinkClick: function( e )
		{
			var element 		= $( '#' + $( this ).attr( 'href' ).split( '#' ).pop() ),
				tabs			= null,
				label   		= null,
				responsiveLabel = null;
				
			if ( element.length > 0 ) {
				
				tabs 			= element.closest( '.fl-tabs' );
				responsiveLabel = element.find( '.fl-tabs-panel-label' );
				tabIndex 		= responsiveLabel.data( 'index' );
				label 			= tabs.find( '.fl-tabs-labels .fl-tabs-label[data-index=' + tabIndex + ']' );
			
				if ( responsiveLabel.is( ':visible' ) ) {
					
					var callback = function() {
						if ( element ) {
							responsiveLabel.trigger( 'click' );	
							element = false;
						}
					};
					
					FLBuilderLayout._scrollToElementOnLinkClick.call( this, e, callback );
				}
				else {
					label.trigger( 'click' );
				}
				
				e.preventDefault();
			}
		},
		
		/**
		 * Initializes all builder forms on a page.
		 *
		 * @since 1.5.4
		 * @access private
		 * @method _initForms
		 */ 
		_initForms: function()
		{
			if ( ! FLBuilderLayout._hasPlaceholderSupport ) {
				$( '.fl-form-field input' ).each( FLBuilderLayout._initFormFieldPlaceholderFallback );
			}
			
			$( '.fl-form-field input' ).on( 'focus', FLBuilderLayout._clearFormFieldError );
		},
		
		/**
		 * Checks to see if the current device has HTML5
		 * placeholder support.
		 *
		 * @since 1.5.4
		 * @access private
		 * @method _hasPlaceholderSupport
		 * @return {Boolean}
		 */ 
		_hasPlaceholderSupport: function()
		{
			var input = document.createElement( 'input' );
			
			return 'undefined' != input.placeholder;
		},
		
		/**
		 * Initializes the fallback for when placeholders aren't supported.
		 *
		 * @since 1.5.4
		 * @access private
		 * @method _initFormFieldPlaceholderFallback
		 */ 
		_initFormFieldPlaceholderFallback: function()
		{
			var field       = $( this ),
				val         = field.val(),
				placeholder = field.attr( 'placeholder' );
			
			if ( 'undefined' != placeholder && '' == val ) {
				field.val( placeholder );
				field.on( 'focus', FLBuilderLayout._hideFormFieldPlaceholderFallback );
				field.on( 'blur', FLBuilderLayout._showFormFieldPlaceholderFallback );
			}
		},
		
		/**
		 * Hides a fallback placeholder on focus.
		 *
		 * @since 1.5.4
		 * @access private
		 * @method _hideFormFieldPlaceholderFallback
		 */ 
		_hideFormFieldPlaceholderFallback: function()
		{
			var field       = $( this ),
				val         = field.val(),
				placeholder = field.attr( 'placeholder' );
			
			if ( val == placeholder ) {
				field.val( '' );
			}
		},
		
		/**
		 * Shows a fallback placeholder on blur.
		 *
		 * @since 1.5.4
		 * @access private
		 * @method _showFormFieldPlaceholderFallback
		 */ 
		_showFormFieldPlaceholderFallback: function()
		{
			var field       = $( this ),
				val         = field.val(),
				placeholder = field.attr( 'placeholder' );
			
			if ( '' == val ) {
				field.val( placeholder );
			}
		},
		
		/**
		 * Clears a form field error message.
		 *
		 * @since 1.5.4
		 * @access private
		 * @method _clearFormFieldError
		 */ 
		_clearFormFieldError: function()
		{
			var field = $( this );
			
			field.removeClass( 'fl-form-error' );
			field.siblings( '.fl-form-error-message' ).hide();
		}
	};

	/* Initializes the builder layout. */
	$(function(){
		FLBuilderLayout.init();
	});

})(jQuery);