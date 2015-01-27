var FLIconSelector;

(function($){

    FLIconSelector = {
        
        _content	: null,
        _lightbox	: null,
        _rendered	: false,
        _filterText : '',
        
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
            
            FLIconSelector._lightbox.on('icon-selected', function(event, icon){
                FLIconSelector._lightbox.off('icon-selected');
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
            $('.fl-icons-filter-select').on('change', FLIconSelector._filter);
            $('.fl-icons-filter-text').on('keyup', FLIconSelector._filter);
            $('.fl-icons-list i').on('click', FLIconSelector._select);
            $('.fl-icon-selector-cancel').on('click', $.proxy(FLIconSelector._lightbox.close, FLIconSelector._lightbox));
        },
        
        _filter: function()
        {
            var section = $( '.fl-icons-filter-select' ).val(),
            	text    = $( '.fl-icons-filter-text' ).val();
            
            // Filter sections.
            if ( 'all' == section ) {
                $( '.fl-icons-section' ).show();
            }
            else {
                $( '.fl-icons-section' ).hide();
                $( '.fl-' + section ).show();
            }
            
            // Filter icons.
            FLIconSelector._filterText = text;
            
            if ( '' != text ) {
	            $( '.fl-icons-list i' ).each( FLIconSelector._filterIcon );
            }
            else {
	            $( '.fl-icons-list i' ).show();
            }
        },
        
        _filterIcon: function()
        {
	    	var icon = $( this );
	    	
	    	if ( -1 == icon.attr( 'class' ).indexOf( FLIconSelector._filterText ) ) {
		    	icon.hide();
	    	}
	    	else {
		    	icon.show();
	    	}
	    },
        
        _select: function()
        {
            var icon = $(this).attr('class');
            
            FLIconSelector._lightbox.trigger('icon-selected', icon);
        }
    };

})(jQuery);