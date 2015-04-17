var FLBuilderGalleryGrid;

(function($) {

    FLBuilderGalleryGrid = function(settings)
    {
        $.extend(this, settings);
        
        if($(this.wrapSelector).length > 0) {
            $(window).on('resize', $.proxy(this.resize, this));
            this.resize();
        }
    };

    FLBuilderGalleryGrid.prototype = {
    
        wrapSelector    : '.fl-gallery-grid',
        itemSelector    : '> *',
        itemWidth       : 400,
        itemHeight      : .75,
    
        resize: function()
        {
            var winWidth    = $(window).width(),
                wrap        = $(this.wrapSelector),
                wrapWidth   = wrap.width(),
                numCols     = winWidth > 480 ? Math.ceil(wrapWidth/this.itemWidth) : 1,
                items       = wrap.find(this.itemSelector),
                itemWidth   = wrapWidth/numCols,
                itemHeight  = itemWidth * this.itemHeight;
            
            items.css({
                'float'  : 'left',
                'height' : itemHeight + 'px',
                'width'  : itemWidth + 'px'
            });
        }
    };

})(jQuery);