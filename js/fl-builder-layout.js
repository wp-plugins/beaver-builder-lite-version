(function($){

    if(typeof FLBuilderLayout != 'undefined') {
        return;
    }
    
    FLBuilderLayout = {
        
        init: function()
        {
            var win = $(window);
            
            // Destroy existing layout events.
            FLBuilderLayout._destroy();
            
            // Add the builder body class.
            $('body').addClass('fl-builder');
            
            // Add the builder touch body class.
            if(FLBuilderLayout._isTouch()) {
                $('body').addClass('fl-builder-touch');
            }
            
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
            
            // Init module animations.
            if($('.fl-builder-edit').length === 0 && typeof jQuery.fn.waypoint !== 'undefined' && !FLBuilderLayout._isTouch()) {
                FLBuilderLayout._initModuleAnimations();
            }
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
            $('.fl-animation').waypoint({
                offset: '80%',
                handler: FLBuilderLayout._doModuleAnimation
            });
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
        }
    };

    $(function(){
        FLBuilderLayout.init();
    });

})(jQuery);