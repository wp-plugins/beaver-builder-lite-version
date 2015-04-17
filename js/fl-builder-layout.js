(function($){

    if(typeof FLBuilderLayout != 'undefined') {
        return;
    }
    
    FLBuilderLayout = {
        
        init: function()
        {
            // Destroy existing layout events.
            FLBuilderLayout._destroy();
            
            // Init CSS classes.
            FLBuilderLayout._initClasses();
            
            // Init anchor links.
            FLBuilderLayout._initAnchorLinks();
            
            // Init backgrounds.
            FLBuilderLayout._initBackgrounds();
            
            // Init module animations.
            FLBuilderLayout._initModuleAnimations();
            
            // Init forms.
            FLBuilderLayout._initForms();
        },
        
        _destroy: function()
        {
            var win = $(window);
            
            win.off('scroll.fl-bg-parallax');
            win.off('resize.fl-bg-video');
        },
        
        _isTouch: function()
        {
            if(('ontouchstart' in window) || (window.DocumentTouch && document instanceof DocumentTouch)) {
                return true;
            }
            
            return false;
        },
        
        _initClasses: function()
        {
            // Add the builder body class.
            $('body').addClass('fl-builder');
            
            // Add the builder touch body class.
            if(FLBuilderLayout._isTouch()) {
                $('body').addClass('fl-builder-touch');
            }
        },
        
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
        
        _initParallaxBackgrounds: function()
        {
            $('.fl-row-bg-parallax').each(FLBuilderLayout._initParallaxBackground);
        },
        
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
        
        _scrollParallaxBackgrounds: function()
        {
            $('.fl-row-bg-parallax').each(FLBuilderLayout._scrollParallaxBackground);
        },
        
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
        
        _resizeBgVideos: function()
        {
            $('.fl-bg-video').each(FLBuilderLayout._resizeBgVideo);
        },
        
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
        
        _initModuleAnimations: function()
        {
	        if($('.fl-builder-edit').length === 0 && typeof jQuery.fn.waypoint !== 'undefined' && !FLBuilderLayout._isTouch()) {
	            $('.fl-animation').waypoint({
	                offset: '80%',
	                handler: FLBuilderLayout._doModuleAnimation
	            });
	        }
        },
        
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
        
        _initAnchorLinks: function()
        {
            $( 'a' ).each( FLBuilderLayout._initAnchorLink );
        },
        
        _initAnchorLink: function()
        {
			var link 	= $( this ),
				href 	= link.attr( 'href' ),
				id   	= null,
				element = null;
			
			if ( 'undefined' != typeof href && href.indexOf( '#' ) > -1 ) {
				
				try {
					
					id 		= href.split( '#' ).pop();
					element = $( '#' + id );
					
					if ( element.length > 0 ) {
						if ( element.hasClass( 'fl-row' ) || element.hasClass( 'fl-col' ) || element.hasClass( 'fl-module' ) ) {
							$( link ).on( 'click', FLBuilderLayout._scrollToElementOnLinkClick );
						}
					}
				}
				catch( e ) {}
			}
        },
        
        _scrollToElementOnLinkClick: function( e )
        {
			var link 	= $( this ),
				href 	= link.attr( 'href' ),
				id   	= href.split( '#' ).pop(),
				element = $( '#' + id ),
				dest 	= 0,
	            win  	= $( window ),
	            doc  	= $( document );
	            
	        if ( element.length > 0 ) {
	        
	            if ( element.offset().top > doc.height() - win.height() ) {
	                dest = doc.height() - win.height();
	            } 
	            else {
	                dest = element.offset().top - 100;
	            }
	
	            $( 'html, body' ).animate( { scrollTop: dest }, 1000, 'swing' );
	        }
	        
	        e.preventDefault();
        },
        
        _initForms: function()
        {
	        if ( ! FLBuilderLayout._hasPlaceholderSupport ) {
	        	$( '.fl-form-field input' ).each( FLBuilderLayout._initFormFieldPlaceholderFallback );
	        }
	        
	        $( '.fl-form-field input' ).on( 'focus', FLBuilderLayout._clearFormFieldError );
        },
        
        _hasPlaceholderSupport: function()
        {
	        var input = document.createElement( 'input' );
	        
	        return 'undefined' != input.placeholder;
        },
        
        _initFormFieldPlaceholderFallback: function()
        {
	        var field 		= $( this ),
	        	val 		= field.val(),
	        	placeholder = field.attr( 'placeholder' );
	        
	        if ( 'undefined' != placeholder && '' == val ) {
		        field.val( placeholder );
		        field.on( 'focus', FLBuilderLayout._hideFormFieldPlaceholderFallback );
		        field.on( 'blur', FLBuilderLayout._showFormFieldPlaceholderFallback );
	        }
        },
        
        _hideFormFieldPlaceholderFallback: function()
        {
	        var field 		= $( this ),
	        	val   		= field.val(),
	        	placeholder = field.attr( 'placeholder' );
	        
	        if ( val == placeholder ) {
		        field.val( '' );
	        }
        },
        
        _showFormFieldPlaceholderFallback: function()
        {
	        var field 		= $( this ),
	        	val   		= field.val(),
	        	placeholder = field.attr( 'placeholder' );
	        
	        if ( '' == val ) {
		        field.val( placeholder );
	        }
        },
        
        _clearFormFieldError: function()
        {
	        var field = $( this );
	        
	        field.removeClass( 'fl-form-error' );
	        field.siblings( '.fl-form-error-message' ).hide();
        }
    };

    $(function(){
        FLBuilderLayout.init();
    });

})(jQuery);