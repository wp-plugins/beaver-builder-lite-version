var FLLightbox;

(function($){

    FLLightbox = function(settings)
    {
        this._init(settings);
        this._render();
        this._bind();
    };
    
    FLLightbox.closeParent = function(child)
    {
        var instanceId = $(child).closest('.fl-lightbox-wrap').attr('data-instance-id');
            
        FLLightbox._instances[instanceId].close();
    };
    
    FLLightbox._instances = {};

    FLLightbox.prototype = {

        _id: null,
        _node: null,
        _visible: false,
        _resizeTimer: null,
        _draggable: false,
        _defaults: {
            className: '',
            destroyOnClose: false
        },
        
        open: function(content)
        {
            this._node.show();
            this._visible = true;
            
            if(typeof content !== 'undefined') {
                this.setContent(content);
            }
            else {
                this._resize();
            }
            
            this.trigger('open');
        },
        
        close: function()
        {
            this._node.hide();
            this._visible = false;
            this.trigger('close');
            
            if(this._defaults.destroyOnClose) {
                this.destroy();
            }
        },
        
        setContent: function(content)
        {
            this._node.find('.fl-lightbox-content').html(content);
            this._resize();
        },
        
        empty: function()
        {
            this._node.find('.fl-lightbox-content').empty();
        },
        
        on: function(event, callback)
        {
            this._node.on(event, callback);
        },
        
        off: function(event)
        {
            this._node.off(event);
        },
        
        trigger: function(event, params)
        {
            this._node.trigger(event, params);
        },
        
        draggable: function(toggle)
        {
            var toggle      = typeof toggle === 'undefined' ? false : toggle,
                mask        = this._node.find('.fl-lightbox-mask'),
                lightbox    = this._node.find('.fl-lightbox');
            
            if(this._draggable) {
                lightbox.draggable('destroy');
            }
            
            if(toggle) {
            
                this._unbind();
                this._draggable = true;
                mask.hide();
            
                lightbox.draggable({
                    cursor: 'move',
                    handle: toggle.handle || ''
                });
            }
            else {
                mask.show();
                this._bind();
                this._draggable = false;
            }
            
            this._resize();
        },
        
        destroy: function()
        {
            $(window).off('resize.fl-lightbox-' + this._id);
            
            this._node.empty();
            this._node.remove();
            
            FLLightbox._instances[this._id] = 'undefined';
            try{ delete FLLightbox._instances[this._id] } catch(e){}
        },
        
        _init: function(settings)
        {
            var i    = 0,
                prop = null;
            
            for(prop in FLLightbox._instances) {
                i++;
            }
            
            this._defaults = $.extend({}, this._defaults, settings);
            this._id = new Date().getTime() + i;
            FLLightbox._instances[this._id] = this;
        },
        
        _render: function()
        {
            this._node = $('<div class="fl-lightbox-wrap" data-instance-id="'+ this._id +'"><div class="fl-lightbox-mask"></div><div class="fl-lightbox"><div class="fl-lightbox-content-wrap"><div class="fl-lightbox-content"></div></div></div></div>');
            
            this._node.addClass(this._defaults.className);
            
            $('body').append(this._node);
        },
        
        _bind: function()
        {
            $(window).on('resize.fl-lightbox-' + this._id, $.proxy(this._delayedResize, this));
        },
        
        _unbind: function()
        {
            $(window).off('resize.fl-lightbox-' + this._id);
        },
        
        _delayedResize: function()
        {
            clearTimeout(this._resizeTimer);
            
            this._resizeTimer = setTimeout($.proxy(this._resize, this), 250);
        },
        
        _resize: function()
        {
            if(this._visible) {
            
                var lightbox  = this._node.find('.fl-lightbox'),
                    boxHeight = lightbox.height(),
                    boxWidth  = lightbox.width(),
                    win       = $(window),
                    winHeight = win.height(),
                    winWidth  = win.width(),
                    top       = '0px',
                    left      = ((winWidth - boxWidth)/2 - 30) + 'px';
                
                // Zero out margins and position.
                lightbox.css({
                    'margin' : '0px',
                    'top'    : 'auto',
                    'left'   : 'auto'
                });
                
                // Get the top position. 
                if(winHeight - 80 > boxHeight) {
                    top = ((winHeight - boxHeight)/2 - 40) + 'px';
                }
                
                // Set the positions.
                if(this._draggable) {
                    lightbox.css('top', top);
                    lightbox.css('left', FLBuilderConfig.isRtl ? '-' + left : left);
                }
                else {
                    lightbox.css('margin-top', top);
                    lightbox.css('margin-left', 'auto');
                    lightbox.css('margin-right', 'auto');
                }
            }
        },
        
        _onKeypress: function(e)
        {
            if(e.which == 27 && this._visible) {
    			this.close();
    		}
        }
    };

})(jQuery);