(function($){

	/**
	 * Helper class for the icon selector lightbox.
	 *
	 * @class FLIconSelector
	 * @since 1.0
	 */
	FLIconSelector = {
		
		/**
		 * A reference to the lightbox HTML content that is
		 * loaded via AJAX.
		 *
		 * @since 1.0
		 * @access private
		 * @property {String} _content
		 */
		_content    : null,
		
		/**
		 * A reference to a FLLightbox object.
		 *
		 * @since 1.0
		 * @access private
		 * @property {FLLightbox} _lightbox
		 */
		_lightbox   : null,
		
		/**
		 * A flag for whether the content has already 
		 * been rendered or not.
		 *
		 * @since 1.0
		 * @access private
		 * @property {Boolean} _rendered
		 */
		_rendered   : false,
		
		/**
		 * The text that is used to filter the selection
		 * of visible icons.
		 *
		 * @since 1.0
		 * @access private
		 * @property {String} _filterText
		 */
		_filterText : '',
		
		/**
		 * Opens the icon selector lightbox.
		 *
		 * @since 1.0
		 * @method open
		 * @param {Function} callback A callback that fires when an icon is selected.
		 */ 
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
		
		/**
		 * Renders a new instance of FLLightbox.
		 *
		 * @since 1.0
		 * @access private
		 * @method _render
		 */ 
		_render: function()
		{
			FLIconSelector._lightbox = new FLLightbox({
				className: 'fl-icon-selector'
			});
			
			FLIconSelector._rendered = true;
		},
		
		/**
		 * Callback for when the lightbox content
		 * has been returned via AJAX.
		 *
		 * @since 1.0
		 * @access private
		 * @method _getContentComplete
		 * @param {String} html The lightbox content.
		 */ 
		_getContentComplete: function(html)
		{
			FLIconSelector._content = html;
			FLIconSelector._lightbox.setContent(html);
			$('.fl-icons-filter-select').on('change', FLIconSelector._filter);
			$('.fl-icons-filter-text').on('keyup', FLIconSelector._filter);
			$('.fl-icons-list i').on('click', FLIconSelector._select);
			$('.fl-icon-selector-cancel').on('click', $.proxy(FLIconSelector._lightbox.close, FLIconSelector._lightbox));
		},
		
		/**
		 * Filters the selection of visible icons based on
		 * the library select and search input text.
		 *
		 * @since 1.0
		 * @access private
		 * @method _filter
		 */ 
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
		
		/**
		 * Shows or hides an icon based on the filter text.
		 *
		 * @since 1.0
		 * @access private
		 * @method _filterIcon
		 */ 
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
		
		/**
		 * Called when an icon is selected and fires the
		 * icon-selected event on the lightbox.
		 *
		 * @since 1.0
		 * @access private
		 * @method _select
		 */ 
		_select: function()
		{
			var icon = $(this).attr('class');
			
			FLIconSelector._lightbox.trigger('icon-selected', icon);
		}
	};

})(jQuery);