var FLBuilderPreview;

(function($){

    /**
     * @class FLBuilderPreview
     */
    FLBuilderPreview = function(o)
    {
        // Type
        this.type = o.type;
        
        // Save the current state.
        this._saveState();
        
        // Render an initial preview?
        if(o.layout != 'undefined' && o.layout) {
            FLBuilder._renderLayout(o.layout, $.proxy(this._init, this));
        }
        else {
            this._init();
        }
    };

    FLBuilderPreview.prototype = {

        /**
         * @param type
         */  
        type                : '',

        /**
         * @param nodeId
         */  
        nodeId              : null,

        /**
         * @param classes
         */  
        classes             : {},
        
        /**
         * @param elements
         */  
        elements            : {},
        
        /**
         * @param state
         * @private
         */  
        state               : null,
        
        /**
         * @param _styleSheet
         * @private
         */  
        _styleSheet         : null,
        
        /**
         * @param _timeout
         * @private
         */  
        _timeout            : null,
        
        /**
         * @param _lastClassName
         * @private
         */  
        _lastClassName      : null,
        
        /**
         * @param _xhr
         * @private
         */  
        _xhr                : null,
    
        /**
         * @method _init
         * @private
         */
        _init: function()
        {
            // Node Id
            this.nodeId = $('.fl-builder-settings').data('node');
    
            // Elements and Class Names
            this._initElementsAndClasses();
            
            // Default field previews.
            this._initDefaultFieldPreviews();
    
            // Init
            switch(this.type) {
                    
                case 'row':
                this._initRow();
                break;
                
                case 'col':
                this._initColumn();
                break;
                
                case 'module':
                this._initModule();
                break;
            }
        },
    
        /**
         * @method _initElementsAndClasses
         * @private
         */
        _initElementsAndClasses: function()
        {
            var contentClass;
            
            // Content Class
            if(this.type == 'row') {
                contentClass = '.fl-row-content-wrap';
            }
            else {
                contentClass = '.fl-' + this.type + '-content';
            }
            
            // Class Names
            $.extend(this.classes, {
                settings        : '.fl-builder-' + this.type + '-settings',
                settingsHeader  : '.fl-builder-' + this.type + '-settings .fl-lightbox-header',
                node            : FLBuilder._contentClass + ' .fl-node-' + this.nodeId,
                content         : FLBuilder._contentClass + ' .fl-node-' + this.nodeId + ' ' + contentClass
            });
            
            // Elements
            $.extend(this.elements, {
                settings        : $(this.classes.settings),
                settingsHeader  : $(this.classes.settingsHeader),
                node            : $(this.classes.node),
                content         : $(this.classes.content)
            });
        },
    
        /**
         * @method updateCSSRule
         */
        updateCSSRule: function(selector, property, value)
        {
            // Make sure we have a stylesheet.
            if(!this._styleSheet) {
                this._styleSheet = new FLStyleSheet();		    
            }
            
            // Update the rule.
            this._styleSheet.updateRule(selector, property, value);
        },
    
        /**
         * @method delay
         */
        delay: function(length, callback)
        {
            this._cancelDelay();
            this._timeout = setTimeout(callback, length);
        },
    
        /**
         * @method _cancelDelay
         * @private
         */
        _cancelDelay: function()
        {
            if(this._timeout != null) {
                clearTimeout(this._timeout);
            }
        },
    
        /**
         * @method hexToRgb
         */
        hexToRgb: function(hex) 
        {
            var bigInt  = parseInt(hex, 16),
                r       = (bigInt >> 16) & 255,
                g       = (bigInt >> 8) & 255,
                b       = bigInt & 255;
            
            return [r, g, b];
        },
    
        /**
         * @method parseFloat
         */
        parseFloat: function(value) 
        {
            return isNaN(parseFloat(value)) ? 0 : parseFloat(value);
        },
        
        /* States
        ----------------------------------------------------------*/
        
        /**
         * @method _saveState
         * @private
         */
        _saveState: function() 
        {
            var post    = $('#fl-post-id').val(),
                css     = $('#fl-builder-layout-' + post + '-css').attr('href'),
                js      = $('script[src*="/fl-builder/' + post + '"]').attr('src'),
                html    = $(FLBuilder._contentClass).html();
                
            this.state = {
                css     : css,
                js      : js,
                html    : html
            };
        },
    
        /**
         * @method preview
         */
        preview: function() 
        {
            var form     = $('.fl-builder-settings-lightbox .fl-builder-settings'),
                nodeId   = form.attr('data-node'),
                settings = FLBuilder._getSettings(form);
            
            // Abort an existing preview request. 
            this._cancelPreview();

            // Make a new preview request.
            this._xhr = FLBuilder.ajax({
                action          : 'fl_builder_render_preview',
                node_id         : nodeId,
                node_preview    : settings,
                'wp-minify-off' : '1'
            }, $.proxy(this._renderPreview, this));
        },
    
        /**
         * @method delayPreview
         */
        delayPreview: function(e)
        {
            var heading         = typeof e == 'undefined' ? [] : $(e.target).closest('tr').find('th'),
                widgetHeading   = $('.fl-builder-widget-settings .fl-builder-settings-title'),
                lightboxHeading = $('.fl-builder-settings .fl-lightbox-header'),
                loaderSrc       = flBuilderUrl + 'img/ajax-loader-small.gif',
                loader          = $('<img class="fl-builder-preview-loader" src="' + loaderSrc + '" />');
            
            $('.fl-builder-preview-loader').remove();
            
            if(heading.length > 0) {
                heading.append(loader);
            }
            else if(widgetHeading.length > 0) {
                widgetHeading.append(loader);
            }
            else if(lightboxHeading.length > 0) {
                lightboxHeading.append(loader);
            }
            
            this.delay(1000, $.proxy(this.preview, this));  
        },
    
        /**
         * @method _cancelPreview
         * @private
         */
        _cancelPreview: function() 
        {
            if(this._xhr) {
                this._xhr.abort();
                this._xhr = null;
            }
        },
    
        /**
         * @method _renderPreview
         * @private
         */
        _renderPreview: function(response) 
        {
            this._xhr = null;
            
            FLBuilder._renderLayout(response, $.proxy(this._renderPreviewComplete, this));
        },
    
        /**
         * @method _renderPreviewComplete
         * @private
         */
        _renderPreviewComplete: function() 
        {
            // Refresh the elements.
            this._initElementsAndClasses();
            
            // Remove the loading graphic.
            $('.fl-builder-preview-loader').remove();
        },
    
        /**
         * @method revert
         */
        revert: function() 
        {
            // Canel any preview delays or requests.
            this._cancelDelay();
            this._cancelPreview();
            
            // Remove the preview stylesheet.
            if(this._styleSheet) {
                this._styleSheet.remove();
            }
            
            // Render the layout.
            FLBuilder._renderLayout(this.state);
        },
    
        /**
         * @method clear
         */
        clear: function() 
        {
            // Canel any preview delays or requests.
            this._cancelDelay();
            this._cancelPreview();
            
            // Remove the preview stylesheet.
            if(this._styleSheet) {
                this._styleSheet.remove();
                this._styleSheet = null;
            }
        },
        
        /* Node Text Color Settings
        ----------------------------------------------------------*/
    
        /**
         * @method _initNodeTextColor
         * @private
         */
        _initNodeTextColor: function()
        {
            // Elements
            $.extend(this.elements, {
                textColor : $(this.classes.settings + ' input[name=text_color]')
            });
            
            // Events
            this.elements.textColor.on('change', $.proxy(this._textColorChange, this));
        },
        
        /**
         * @method _textColorChange
         * @private
         */
        _textColorChange: function(e)
        {
            if(this.elements.textColor.val() == '') {
                this.updateCSSRule(this.classes.node, 'color', 'inherit');
                this.updateCSSRule(this.classes.node + ' *', 'color', 'inherit');
            }
            else {
                this.delay(100, $.proxy(function(){
                    this.updateCSSRule(this.classes.node, 'color', '#' + this.elements.textColor.val());
                    this.updateCSSRule(this.classes.node + ' *', 'color', '#' + this.elements.textColor.val());
                }, this));   
            }
        },
        
        /* Node Bg Settings
        ----------------------------------------------------------*/
    
        /**
         * @method _initNodeBg
         * @private
         */
        _initNodeBg: function()
        {
            // Elements
            $.extend(this.elements, {
                bgType                      : $(this.classes.settings + ' select[name=bg_type]'),
                bgColor                     : $(this.classes.settings + ' input[name=bg_color]'),
                bgColorPicker               : $(this.classes.settings + ' .fl-picker-bg_color'),
                bgOpacity                   : $(this.classes.settings + ' input[name=bg_opacity]'),
                bgImageSrc                  : $(this.classes.settings + ' select[name=bg_image_src]'),
                bgRepeat                    : $(this.classes.settings + ' select[name=bg_repeat]'),
                bgPosition                  : $(this.classes.settings + ' select[name=bg_position]'),
                bgAttachment                : $(this.classes.settings + ' select[name=bg_attachment]'),
                bgSize                      : $(this.classes.settings + ' select[name=bg_size]'),
                bgVideo                     : $(this.classes.settings + ' input[name=bg_video]'),
                bgVideoFallbackSrc          : $(this.classes.settings + ' select[name=bg_video_fallback_src]'),
                bgSlideshowSource           : $(this.classes.settings + ' select[name=ss_source]'),
                bgSlideshowPhotos           : $(this.classes.settings + ' input[name=ss_photos]'),
                bgSlideshowFeedUrl          : $(this.classes.settings + ' input[name=ss_feed_url]'),
                bgSlideshowSpeed            : $(this.classes.settings + ' input[name=ss_speed]'),
                bgSlideshowTrans            : $(this.classes.settings + ' select[name=ss_transition]'),
                bgSlideshowTransSpeed       : $(this.classes.settings + ' input[name=ss_transitionDuration]'),
                bgParallaxImageSrc          : $(this.classes.settings + ' select[name=bg_parallax_image_src]')
            });
            
            // Events
            this.elements.bgType.on(                'change', $.proxy(this._bgTypeChange, this));
            this.elements.bgColor.on(               'change', $.proxy(this._bgColorChange, this));
            this.elements.bgOpacity.on(             'keyup',  $.proxy(this._bgOpacityChange, this));
            this.elements.bgImageSrc.on(            'change', $.proxy(this._bgPhotoChange, this));
            this.elements.bgRepeat.on(              'change', $.proxy(this._bgPhotoChange, this));
            this.elements.bgPosition.on(            'change', $.proxy(this._bgPhotoChange, this));
            this.elements.bgAttachment.on(          'change', $.proxy(this._bgPhotoChange, this));
            this.elements.bgSize.on(                'change', $.proxy(this._bgPhotoChange, this));
            this.elements.bgSlideshowSource.on(     'change', $.proxy(this._bgSlideshowChange, this));
            this.elements.bgSlideshowPhotos.on(     'change', $.proxy(this._bgSlideshowChange, this));
            this.elements.bgSlideshowFeedUrl.on(    'keyup',  $.proxy(this._bgSlideshowChange, this));
            this.elements.bgSlideshowSpeed.on(      'keyup',  $.proxy(this._bgSlideshowChange, this));
            this.elements.bgSlideshowTrans.on(      'change', $.proxy(this._bgSlideshowChange, this));
            this.elements.bgSlideshowTransSpeed.on( 'keyup',  $.proxy(this._bgSlideshowChange, this));
            this.elements.bgParallaxImageSrc.on(    'change', $.proxy(this._bgParallaxChange, this));
        },
        
        /**
         * @method _bgTypeChange
         * @private
         */
        _bgTypeChange: function(e)
        {
            var val = this.elements.bgType.val();
                
            // Clear bg styles first.
            this.elements.node.removeClass('fl-row-bg-video');
            this.elements.node.removeClass('fl-row-bg-slideshow');
            this.elements.node.removeClass('fl-row-bg-parallax');
            this.elements.node.find('.fl-bg-video').remove();
            this.elements.node.find('.fl-bg-slideshow').remove();
            this.elements.content.css('background-image', '');
            
            this.updateCSSRule(this.classes.content, {
                'background-color'  : 'transparent',
                'background-image'  : 'none'
            });
            
            // Color
            if(val == 'color') {
                this.elements.bgColor.trigger('change');
            }
            
            // Photo
            else if(val == 'photo') {
                this.elements.bgImageSrc.trigger('change');
            }
            
            // Video
            else if(val == 'video' && this.elements.bgVideo.val() != '') {
                this.preview();
            }
            
            // Slideshow
            else if(val == 'slideshow') {
                this._bgSlideshowChange();
            }
            
            // Parallax
            else if(val == 'parallax') {
                this.elements.bgParallaxImageSrc.trigger('change');
            }
        },
        
        /**
         * @method _bgColorChange
         * @private
         */
        _bgColorChange: function(e)
        {
            var rgb, alpha, value;
            
            if(this.elements.bgColor.val() == '' || isNaN(this.elements.bgOpacity.val())) {
                this.updateCSSRule(this.classes.content, 'background-color', 'transparent');  
            }
            else {
            
                rgb    = this.hexToRgb(this.elements.bgColor.val()),
                alpha  = this.parseFloat(this.elements.bgOpacity.val())/100,
                value  = 'rgba(' + rgb.join() + ', ' + alpha + ')';
                    
                this.delay(100, $.proxy(function(){
                    this.updateCSSRule(this.classes.content, 'background-color', value);
                }, this));   
            }
        },
        
        /**
         * @method _bgOpacityChange
         * @private
         */
        _bgOpacityChange: function(e)
        {
            this.elements.bgColor.trigger('change');
        },
        
        /**
         * @method _bgPhotoChange
         * @private
         */
        _bgPhotoChange: function(e)
        {
            if(this.elements.bgImageSrc.val()) {

                this.updateCSSRule(this.classes.content, {
                    'background-image'      : 'url(' + this.elements.bgImageSrc.val() + ')',
                    'background-repeat'     : this.elements.bgRepeat.val(),
                    'background-position'   : this.elements.bgPosition.val(),
                    'background-attachment' : this.elements.bgAttachment.val(),
                    'background-size'       : this.elements.bgSize.val()
                });
            }
        },
        
        /**
         * @method _bgSlideshowChange
         * @private
         */
        _bgSlideshowChange: function(e)
        {
            var eles        = this.elements,
                source      = eles.bgSlideshowSource.val(),
                photos      = eles.bgSlideshowPhotos.val(),
                feed        = eles.bgSlideshowFeedUrl.val(),
                speed       = eles.bgSlideshowSpeed.val(),
                transSpeed  = eles.bgSlideshowTransSpeed.val();
            
            if(source == 'wordpress' && photos == '') {
                return;
            }
            else if(source == 'smugmug' && feed == '') {
                return;
            }
            else if(isNaN(parseInt(speed))) {
                return;
            }
            else if(isNaN(parseInt(transSpeed))) {
                return;
            }
            
            this.delay(500, $.proxy(this.preview, this));
        },
        
        /**
         * @method _bgParallaxChange
         * @private
         */
        _bgParallaxChange: function(e)
        {
            if(this.elements.bgParallaxImageSrc.val()) {
            
                this.updateCSSRule(this.classes.content, {
                    'background-image'      : 'url(' + this.elements.bgParallaxImageSrc.val() + ')',
                    'background-repeat'     : 'no-repeat',
                    'background-position'   : 'center center',
                    'background-attachment' : 'fixed',
                    'background-size'       : 'cover'
                });
            }
        },
        
        /* Node Border Settings
        ----------------------------------------------------------*/
    
        /**
         * @method _initNodeBorder
         * @private
         */
        _initNodeBorder: function()
        {
            // Elements
            $.extend(this.elements, {
                borderType              : $(this.classes.settings + ' select[name=border_type]'),
                borderColor             : $(this.classes.settings + ' input[name=border_color]'),
                borderColorPicker       : $(this.classes.settings + ' .fl-picker-border_color'),
                borderOpacity           : $(this.classes.settings + ' input[name=border_opacity]'),
                borderTopWidth          : $(this.classes.settings + ' input[name=border_top]'),
                borderBottomWidth       : $(this.classes.settings + ' input[name=border_bottom]'),
                borderLeftWidth         : $(this.classes.settings + ' input[name=border_left]'),
                borderRightWidth        : $(this.classes.settings + ' input[name=border_right]')
            });
            
            // Events
            this.elements.borderType.on(            'change', $.proxy(this._borderTypeChange, this));
            this.elements.borderColor.on(           'change', $.proxy(this._borderColorChange, this));
            this.elements.borderOpacity.on(         'keyup',  $.proxy(this._borderOpacityChange, this));
            this.elements.borderTopWidth.on(        'keyup',  $.proxy(this._borderWidthChange, this));
            this.elements.borderBottomWidth.on(     'keyup',  $.proxy(this._borderWidthChange, this));
            this.elements.borderLeftWidth.on(       'keyup',  $.proxy(this._borderWidthChange, this));
            this.elements.borderRightWidth.on(      'keyup',  $.proxy(this._borderWidthChange, this));
        },
        
        /**
         * @method _borderTypeChange
         * @private
         */
        _borderTypeChange: function(e)
        {
            var val = this.elements.borderType.val();
                
            this.updateCSSRule(this.classes.content, {
                'border-style'  : val == '' ? 'none' : val
            });
            
            this.elements.borderColor.trigger('change');
            this.elements.borderTopWidth.trigger('keyup');
        },
        
        /**
         * @method _borderColorChange
         * @private
         */
        _borderColorChange: function(e)
        {
            var rgb, alpha, value;
            
            if(this.elements.borderColor.val() == '' || isNaN(this.elements.borderOpacity.val())) {
                this.updateCSSRule(this.classes.content, 'border-color', 'transparent');  
            }
            else {
            
                rgb    = this.hexToRgb(this.elements.borderColor.val()),
                alpha  = parseInt(this.elements.borderOpacity.val())/100,
                value  = 'rgba(' + rgb.join() + ', ' + alpha + ')';
                    
                this.delay(100, $.proxy(function(){
                    this.updateCSSRule(this.classes.content, 'border-color', value);
                }, this));   
            }
        },
        
        /**
         * @method _bgOpacityChange
         * @private
         */
        _borderOpacityChange: function(e)
        {
            this.elements.borderColor.trigger('change');
        },
        
        /**
         * @method _getBorderWidths
         * @private
         */
        _getBorderWidths: function(e)
        {
            var top     = this.elements.borderTopWidth.val(),
                bottom  = this.elements.borderBottomWidth.val(),
                left    = this.elements.borderLeftWidth.val(),
                right   = this.elements.borderRightWidth.val();
            
            if(top == '') {
                top = this.elements.borderTopWidth.attr('placeholder');
            }
            if(bottom == '') {
                bottom = this.elements.borderBottomWidth.attr('placeholder');
            }
            if(left == '') {
                left = this.elements.borderLeftWidth.attr('placeholder');
            }
            if(right == '') {
                right = this.elements.borderRightWidth.attr('placeholder');
            }
            
            return {
                top     : this.parseFloat(top),
                bottom  : this.parseFloat(bottom),
                left    : this.parseFloat(left),
                right   : this.parseFloat(right)
            };
        },
        
        /**
         * @method _borderWidthChange
         * @private
         */
        _borderWidthChange: function(e)
        {
            var widths = this._getBorderWidths();
            
            this.elements.borderColor.trigger('change');
            
            this.updateCSSRule(this.classes.content, {
                'border-top-width'      : widths.top + 'px',
                'border-bottom-width'   : widths.bottom + 'px',
                'border-left-width'     : widths.left + 'px',
                'border-right-width'    : widths.right + 'px',
            });
            
            this._positionAbsoluteBgs();
        },
        
        /* Node Class Name Settings
        ----------------------------------------------------------*/
    
        /**
         * @method _initNodeClassName
         * @private
         */
        _initNodeClassName: function()
        {
            // Elements
            $.extend(this.elements, {
                className : $(this.classes.settings + ' input[name=class]')
            });
            
            // Events
            this.elements.className.on('keyup', $.proxy(this._classNameChange, this));
        },
        
        /**
         * @method _classNameChange
         * @private
         */
        _classNameChange: function(e)
        {
            var className = this.elements.className.val();
            
            if(this._lastClassName != null) {
                this.elements.node.removeClass(this._lastClassName);
            }
            
            this.elements.node.addClass(className);
            this._lastClassName = className;
        },
        
        /* Node Margin Settings
        ----------------------------------------------------------*/
    
        /**
         * @method _initMargins
         * @private
         */
        _initMargins: function()
        {
            // Elements
            $.extend(this.elements, {
                marginTop       : $(this.classes.settings + ' input[name=margin_top]'),
                marginBottom    : $(this.classes.settings + ' input[name=margin_bottom]'),
                marginLeft      : $(this.classes.settings + ' input[name=margin_left]'),
                marginRight     : $(this.classes.settings + ' input[name=margin_right]')
            });
            
            // Events
            this.elements.marginTop.on(     'keyup', $.proxy(this._marginChange, this));
            this.elements.marginBottom.on(  'keyup', $.proxy(this._marginChange, this));
            this.elements.marginLeft.on(    'keyup', $.proxy(this._marginChange, this));
            this.elements.marginRight.on(   'keyup', $.proxy(this._marginChange, this));
        },
        
        /**
         * @method _getMargins
         * @private
         */
        _getMargins: function(e)
        {
            var top     = this.elements.marginTop.val(),
                bottom  = this.elements.marginBottom.val(),
                left    = this.elements.marginLeft.val(),
                right   = this.elements.marginRight.val();
            
            if(top == '') {
                top = this.elements.marginTop.attr('placeholder');
            }
            if(bottom == '') {
                bottom = this.elements.marginBottom.attr('placeholder');
            }
            if(left == '') {
                left = this.elements.marginLeft.attr('placeholder');
            }
            if(right == '') {
                right = this.elements.marginRight.attr('placeholder');
            }
            
            return {
                top     : this.parseFloat(top),
                bottom  : this.parseFloat(bottom),
                left    : this.parseFloat(left),
                right   : this.parseFloat(right)
            };
        },
        
        /**
         * @method _marginChange
         * @private
         */
        _marginChange: function(e)
        {
            var margins = this._getMargins();
            
            this.updateCSSRule(this.classes.content, {
                'margin-top'      : margins.top + 'px',
                'margin-bottom'   : margins.bottom + 'px',
                'margin-left'     : margins.left + 'px',
                'margin-right'    : margins.right + 'px',
            });
            
            this._positionAbsoluteBgs();
        },
        
        /* Node Padding Settings
        ----------------------------------------------------------*/
    
        /**
         * @method _initPadding
         * @private
         */
        _initPadding: function()
        {
            // Elements
            $.extend(this.elements, {
                paddingTop       : $(this.classes.settings + ' input[name=padding_top]'),
                paddingBottom    : $(this.classes.settings + ' input[name=padding_bottom]'),
                paddingLeft      : $(this.classes.settings + ' input[name=padding_left]'),
                paddingRight     : $(this.classes.settings + ' input[name=padding_right]')
            });
            
            // Events
            this.elements.paddingTop.on(     'keyup', $.proxy(this._paddingChange, this));
            this.elements.paddingBottom.on(  'keyup', $.proxy(this._paddingChange, this));
            this.elements.paddingLeft.on(    'keyup', $.proxy(this._paddingChange, this));
            this.elements.paddingRight.on(   'keyup', $.proxy(this._paddingChange, this));
        },
        
        /**
         * @method _getPadding
         * @private
         */
        _getPadding: function(e)
        {
            var top     = this.elements.paddingTop.val(),
                bottom  = this.elements.paddingBottom.val(),
                left    = this.elements.paddingLeft.val(),
                right   = this.elements.paddingRight.val();
            
            if(top == '') {
                top = this.elements.paddingTop.attr('placeholder');
            }
            if(bottom == '') {
                bottom = this.elements.paddingBottom.attr('placeholder');
            }
            if(left == '') {
                left = this.elements.paddingLeft.attr('placeholder');
            }
            if(right == '') {
                right = this.elements.paddingRight.attr('placeholder');
            }
            
            return {
                top     : this.parseFloat(top),
                bottom  : this.parseFloat(bottom),
                left    : this.parseFloat(left),
                right   : this.parseFloat(right)
            };
        },
        
        /**
         * @method _paddingChange
         * @private
         */
        _paddingChange: function(e)
        {
            var padding = this._getPadding();
               
            this.updateCSSRule(this.classes.content, {
                'padding-top'      : padding.top + 'px',
                'padding-bottom'   : padding.bottom + 'px',
                'padding-left'     : padding.left + 'px',
                'padding-right'    : padding.right + 'px',
            });
            
            this._positionAbsoluteBgs();
        },
        
        /* Absolutely Positioned Backgrounds
        ----------------------------------------------------------*/
        
        /**
         * @method _positionAbsoluteBgs
         * @private
         */
        _positionAbsoluteBgs: function()
        {
            var slideshow = this.elements.node.find('.fl-bg-slideshow'),
                video     = this.elements.node.find('.fl-bg-video'),
                margins   = null,
                borders   = null;
                
            if(slideshow.length > 0 || video.length > 0) {
            
                margins = this._getMargins();
                borders = this._getBorderWidths();
                
                if(slideshow.length > 0) {
                 
                    this.updateCSSRule(this.classes.node + ' .fl-bg-slideshow', {
                        'top'      : (margins.top + borders.top) + 'px',
                        'bottom'   : (margins.bottom + borders.bottom) + 'px',
                        'left'     : (margins.left + borders.left) + 'px',
                        'right'    : (margins.right + borders.right) + 'px',
                    });
                
                    FLBuilder._resizeLayout();
                }
                if(video.length > 0) {
                 
                    this.updateCSSRule(this.classes.node + ' .fl-bg-video', {
                        'top'      : (margins.top + borders.top) + 'px',
                        'bottom'   : (margins.bottom + borders.bottom) + 'px',
                        'left'     : (margins.left + borders.left) + 'px',
                        'right'    : (margins.right + borders.right) + 'px',
                    });
                }
            }
        },
        
        /* Row Settings
        ----------------------------------------------------------*/
    
        /**
         * @method _initRow
         * @private
         */
        _initRow: function()
        {
            // Elements
            $.extend(this.elements, {
                width          : $(this.classes.settings + ' select[name=width]'),
                contentWidth   : $(this.classes.settings + ' select[name=content_width]')
            });
            
            // Events
            this.elements.width.on(         'change', $.proxy(this._rowWidthChange, this));
            this.elements.contentWidth.on(  'change', $.proxy(this._rowContentWidthChange, this));
            
            // Common Elements
            this._initNodeTextColor();
            this._initNodeBg();
            this._initNodeBorder();
            this._initNodeClassName();
            this._initMargins();
            this._initPadding();
        },
        
        /**
         * @method _rowWidthChange
         * @private
         */
        _rowWidthChange: function(e)
        {
            var row = this.elements.node;
            
            if(this.elements.width.val() == 'full') {
                row.removeClass('fl-row-fixed-width');
                row.addClass('fl-row-full-width');
            }
            else {
                row.removeClass('fl-row-full-width');
                row.addClass('fl-row-fixed-width');
            }
        },
        
        /**
         * @method _rowContentWidthChange
         * @private
         */
        _rowContentWidthChange: function(e)
        {
            var content = this.elements.content.find('.fl-row-content');
            
            if(this.elements.contentWidth.val() == 'full') {
                content.removeClass('fl-row-fixed-width');
                content.addClass('fl-row-full-width');
            }
            else {
                content.removeClass('fl-row-full-width');
                content.addClass('fl-row-fixed-width');
            }
        },
        
        /* Columns Settings
        ----------------------------------------------------------*/
    
        /**
         * @method _initColumn
         * @private
         */
        _initColumn: function()
        {
            // Elements
            $.extend(this.elements, {
                size : $(this.classes.settings + ' input[name=size]')
            });
            
            // Events
            this.elements.size.on('keyup', $.proxy(this._colSizeChange, this));
            
            // Common Elements
            this._initNodeTextColor();
            this._initNodeBg();
            this._initNodeBorder();
            this._initNodeClassName();
            this._initMargins();
            this._initPadding();
        },
        
        /**
         * @method _colSizeChange
         * @private
         */
        _colSizeChange: function()
        {
            var preview         = this,
                minWidth        = 10,
                maxWidth        = 100 - minWidth,
                size            = parseFloat(this.elements.size.val()),
                prev            = this.elements.node.prev('.fl-col'),
                next            = this.elements.node.next('.fl-col'),
                sibling         = next.length === 0 ? prev : next,
                siblings        = this.elements.node.siblings('.fl-col'),
                siblingsWidth   = 0;
                
            // Don't resize if we onlt have one column or no size.
            if(siblings.length === 0 || isNaN(size)) {
                return;
            }
            
            // Adjust sizes based on other columns.
            siblings.each(function() {
            
                if($(this).data('node') == sibling.data('node')) {
                    return;
                }
                
                maxWidth        -= parseFloat($(this)[0].style.width);
                siblingsWidth   += parseFloat($(this)[0].style.width);
            });
            
            // Make sure the new width isn't too small.
            if(size < minWidth) {
                size = minWidth;
            }
            
            // Make sure the new width isn't too big.
            if(size > maxWidth) {
                size = maxWidth;
            }
        
            // Update the widths.
            sibling.css('width', (100 - siblingsWidth - size) + '%');
            this.elements.node.css('width', size + '%');
        },
        
        /* Module Settings
        ----------------------------------------------------------*/
    
        /**
         * @method _initModule
         * @private
         */
        _initModule: function()
        {
            this._initNodeClassName();
            this._initMargins();
        },
        
        /* Default Field Previews
        ----------------------------------------------------------*/
        
        /**
         * @method _initDefaultFieldPreviews
         * @private
         */
        _initDefaultFieldPreviews: function()
        {
            var fields      = this.elements.settings.find('.fl-field'),
                field       = null,
                fieldType   = null,
                preview     = null,
                i           = 0;
            
            for( ; i < fields.length; i++) {
            
                field   = fields.eq(i);
                preview = field.data('preview');
                
                if(preview.type == 'refresh') {
                    this._initFieldRefreshPreview(field);
                }
                if(preview.type == 'text') {
                    this._initFieldTextPreview(field);
                }
                if(preview.type == 'css') {
                    this._initFieldCSSPreview(field);
                }
                if(preview.type == 'widget') {
                    this._initFieldWidgetPreview(field);
                }
            }
        },
        
        /**
         * @method _initFieldRefreshPreview
         * @private
         */
        _initFieldRefreshPreview: function(field)
        {
            var fieldType = field.data('type'),
                preview   = field.data('preview'),
                callback  = $.proxy(this.delayPreview, this);
            
            switch(fieldType) {
                        
                case 'text':
                    field.find('input[type=text]').on('keyup', callback);
                break;
                
                case 'textarea':
                    field.find('textarea').on('keyup', callback);
                break;
                
                case 'select':
                    field.find('select').on('change', callback);
                break;
                
                case 'color':
                    field.find('.fl-color-picker-value').on('change', callback);
                break;
                
                case 'photo':
                    field.find('select').on('change', callback);
                break;
                
                case 'multiple-photos':
                    field.find('input').on('change', callback);
                break;
                
                case 'photo-sizes':
                    field.find('select').on('change', callback);
                break;
                
                case 'video':
                    field.find('input').on('change', callback);
                break;
                
                case 'icon':
                    field.find('input').on('change', callback);
                break;
                
                case 'form':
                    field.delegate('input', 'change', callback);
                break;
                
                case 'editor':
                    this._addTextEditorCallback(field, preview);
                break;
                
                case 'code':
                    field.find('textarea').on('change', callback);
                break;
                
                case 'post-type':
                    field.find('select').on('change', callback);
                break;
                
                case 'suggest':
                    field.find('.as-values').on('change', callback);
                break;
            }
        },
        
        /**
         * @method _initFieldTextPreview
         * @private
         */
        _initFieldTextPreview: function(field)
        {
            var fieldType = field.data('type'),
                preview   = field.data('preview'),
                callback  = $.proxy(this._previewText, this, preview);
            
            switch(fieldType) {
                
                case 'text':
                    field.find('input[type=text]').on('keyup', callback);
                break;
                
                case 'textarea':
                    field.find('textarea').on('keyup', callback);
                break;
                
                case 'code':
                    field.find('textarea').on('change', callback);
                break;
                
                case 'editor':
                    this._addTextEditorCallback(field, preview);
                break;
            }
        },
        
        /**
         * @method _previewText
         * @private
         */
        _previewText: function(preview, e)
        {
            var element = this.elements.node.find(preview.selector),
                text    = $('<div>' + $(e.target).val() + '</div>');
                
            if(element.length > 0) {
                text.find('script').remove();
                element.html(text.html());
            }
        },
        
        /**
         * @method _previewTextEditor
         * @private
         */
        _previewTextEditor: function(preview, id, e)
        {
            var element  = this.elements.node.find(preview.selector),
                editor   = typeof tinyMCE != 'undefined' ? tinyMCE.get(id) : null,
                textarea = $('#' + id),
                text     = '';

            if(element.length > 0) {
            
                if(editor && textarea.css('display') == 'none') {
                    text = $('<div>' + editor.getContent() + '</div>');
                } 
                else {
                    text = $('<div>' + textarea.val() + '</div>');
                }
            
                text.find('script').remove();
                element.html(text.html());
            }
        },
        
        /**
         * @method _addTextEditorCallback
         * @private
         */
        _addTextEditorCallback: function(field, preview)
        {
            var id       = field.find('textarea.wp-editor-area').attr('id'),
                callback = null;
                
            if(preview.type == 'refresh') {
                callback = $.proxy(this.delayPreview, this);
            }
            else if(preview.type == 'text') {
                callback = $.proxy(this._previewTextEditor, this, preview, id);
            }
            else {
                return;
            }
                            
            $('#' + id).on('keyup', callback);
            
            if(typeof tinyMCE != 'undefined') {
                editor = tinyMCE.get(id);
                editor.on('change', callback);
                editor.on('keyup', callback);
            }
        },
        
        /**
         * @method _initFieldCSSPreview
         * @private
         */
        _initFieldCSSPreview: function(field)
        {
            var fieldType = field.data('type'),
                preview   = field.data('preview'),
                callback  = $.proxy(this._previewCSS, this, preview);
            
            switch(fieldType) {
                
                case 'text':
                    field.find('input[type=text]').on('keyup', callback);
                break;
                
                case 'select':
                    field.find('select').on('change', callback);
                break;
                
                case 'color':
                    field.find('.fl-color-picker-value').on('change', $.proxy(this._previewColor, this, preview));
                break;
            }
        },
        
        /**
         * @method _previewCSS
         * @private
         */
        _previewCSS: function(preview, e)
        {
            var selector = this.classes.node + ' ' + preview.selector,
                property = preview.property,
                unit     = typeof preview.unit == 'undefined' ? '' : preview.unit,
                value    = $(e.target).val();
                
            if(unit == '%') {
                value = parseInt(value)/100;
            }
            else {
                value += unit;
            }
            
            this.updateCSSRule(selector, property, value);
        },
        
        /**
         * @method _previewColor
         * @private
         */
        _previewColor: function(preview, e)
        {
            var selector = this.classes.node + ' ' + preview.selector,
                val      = $(e.target).val(),
                color    = val == '' ? 'inherit' : '#' + val;
            
            this.updateCSSRule(selector, preview.property, color);
        },
        
        /**
         * @method _initFieldWidgetPreview
         * @private
         */
        _initFieldWidgetPreview: function(field)
        {
            var callback = $.proxy(this.delayPreview, this);
            
            field.find('input').on('keyup', callback);
            field.find('input[type=checkbox]').on('click', callback);
            field.find('textarea').on('keyup', callback);
            field.find('select').on('change', callback);
        }
    };

})(jQuery);