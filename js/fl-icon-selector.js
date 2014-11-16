var FLIconSelector;

(function($){

    FLIconSelector = {
        
        _content: null,
        _lightbox: null,
        _rendered: false,
        
        open: function(callback)
        {
            if(!FLIconSelector._rendered) {
                FLIconSelector._render();
            }
            
            if(FLIconSelector._content === null) {
            
                FLIconSelector._lightbox.open('<div class="fl-builder-lightbox-loading"></div>');
            
                FLBuilder.ajax({
                    action: 'fl_builder_render_icon_selector'
                }, FLIconSelector._getContentComplete);
            }
            else {
                FLIconSelector._lightbox.open();
            }
            
            FLIconSelector._lightbox.on('select', function(event, icon){
                FLIconSelector._lightbox.off('select');
                FLIconSelector._lightbox.close();
                callback(icon);
            });
        },
        
        _render: function()
        {
            FLIconSelector._lightbox = new FLLightbox({
                className: 'fl-icon-selector'
            });
            
            FLIconSelector._rendered = true;
        },
        
        _getContentComplete: function(html)
        {
            FLIconSelector._content = html;
            FLIconSelector._lightbox.setContent(html);
            $('.fl-icons-filter select').on('change', FLIconSelector._filter);
            $('.fl-icons-list i').on('click', FLIconSelector._select);
            $('.fl-icon-selector-cancel').on('click', $.proxy(FLIconSelector._lightbox.close, FLIconSelector._lightbox));
        },
        
        _filter: function()
        {
            var val = $(this).val();
            
            if(val == 'all') {
                $('.fl-icons-section').show();
            }
            else {
                $('.fl-icons-section').hide();
                $('.fl-' + val).show();
            }
        },
        
        _select: function()
        {
            var icon = $(this).attr('class');
            
            FLIconSelector._lightbox.trigger('select', icon);
        }
    };

})(jQuery);