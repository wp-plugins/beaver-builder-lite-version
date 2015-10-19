(function( $ ) {
	
	/**
	 * Logic for the builder's help tour.
	 *
	 * @class FLBuilderTour
	 * @since 1.4.9
	 */
	FLBuilderTour = {
	
		/**
		 * A reference to the Bootstrap Tour object.
		 *
		 * @since 1.4.9
		 * @access private
		 * @property {Tour} _tour
		 */
		_tour: null,
		
		/**
		 * Starts the tour or restarts it if it
		 * has already run.
		 *
		 * @since 1.4.9
		 * @method start
		 */
		start: function()
		{
			if ( ! FLBuilderTour._tour ) {
				FLBuilderTour._tour = new Tour( FLBuilderTour._config() );
				FLBuilderTour._tour.init();
			}
			else {
				FLBuilderTour._tour.restart();
			}
			
			FLBuilderTour._tour.start();
		},
		
		/**
		 * Returns a config object for the tour.
		 *
		 * @since 1.4.9
		 * @access private
		 * @method _config
		 * @return {Object}
		 */
		_config: function()
		{
			var config = {
				storage     : false,
				onStart     : FLBuilderTour._onStart,
				onPrev      : FLBuilderTour._onPrev,
				onNext      : FLBuilderTour._onNext,
				onEnd       : FLBuilderTour._onEnd,
				template    : '<div class="popover" role="tooltip"> <i class="fa fa-times" data-role="end"></i> <div class="arrow"></div> <h3 class="popover-title"></h3> <div class="popover-content"></div> <div class="popover-navigation clearfix"> <button class="fl-builder-button fl-builder-button-primary fl-builder-tour-next" data-role="next">' + FLBuilderStrings.tourNext + '</button> </div> </div>',
				steps       : [
					{
						animation   : false,
						element     : '.fl-builder-bar',
						placement   : 'bottom',
						title       : FLBuilderStrings.tourTemplatesTitle,
						content     : FLBuilderStrings.tourTemplates,
						onShown     : function() {
							if ( 0 === $( '.fl-template-selector' ).length ) {
								$( '.popover[class*=tour-]' ).css( 'visibility', 'hidden' );
								FLBuilder._showTemplateSelector();
							}
							else {
								FLBuilderTour._templateSelectorLoaded();
							}
						}
					},
					{
						animation   : false,
						element     : '#fl-builder-blocks-rows .fl-builder-blocks-section-title',
						placement   : 'left',
						title       : FLBuilderStrings.tourAddRowsTitle,
						content     : FLBuilderStrings.tourAddRows,
						onShow      : function() {
							FLBuilderTour._dimSection( 'body' );
							FLBuilderTour._dimSection( '.fl-builder-bar' );
							FLBuilder._showPanel();
							$( '.fl-template-selector .fl-builder-settings-cancel' ).trigger( 'click' );
							$( '#fl-builder-blocks-rows .fl-builder-blocks-section-title' ).trigger( 'click' );
						}
					},
					{
						animation   : false,
						element     : '#fl-builder-blocks-basic .fl-builder-blocks-section-title',
						placement   : 'left',
						title       : FLBuilderStrings.tourAddContentTitle,
						content     : FLBuilderStrings.tourAddContent,
						onShow      : function() {
							FLBuilderTour._dimSection( 'body' );
							FLBuilderTour._dimSection( '.fl-builder-bar' );
							FLBuilder._showPanel();
							$( '#fl-builder-blocks-basic .fl-builder-blocks-section-title' ).trigger( 'click' );
							$( '.fl-row' ).eq( 0 ).trigger( 'mouseleave' );
							$( '.fl-module' ).eq( 0 ).trigger( 'mouseleave' );
						}
					},
					{
						animation   : false,
						element     : '.fl-row:first-of-type',
						placement   : 'top',
						title       : FLBuilderStrings.tourEditContentTitle,
						content     : FLBuilderStrings.tourEditContent,
						onShow      : function() {
							FLBuilderTour._dimSection( '.fl-builder-bar' );
							FLBuilder._closePanel();
							$( '.fl-row' ).eq( 0 ).trigger( 'mouseenter' );
							$( '.fl-module' ).eq( 0 ).trigger( 'mouseenter' );
						}
					},
					{
						animation   : false,
						element     : '.fl-row:first-of-type .fl-module-overlay .fl-block-overlay-actions',
						placement   : 'top',
						title       : FLBuilderStrings.tourEditContentTitle,
						content     : FLBuilderStrings.tourEditContent2,
						onShow      : function() {
							FLBuilderTour._dimSection( '.fl-builder-bar' );
							FLBuilder._closePanel();
							$( '.fl-row' ).eq( 0 ).trigger( 'mouseenter' );
							$( '.fl-module' ).eq( 0 ).trigger( 'mouseenter' );
						}
					},
					{
						animation   : false,
						element     : '.fl-builder-add-content-button',
						placement   : 'bottom',
						title       : FLBuilderStrings.tourAddContentButtonTitle,
						content     : FLBuilderStrings.tourAddContentButton,
						onShow      : function() {
							FLBuilderTour._dimSection( 'body' );
							$( '.fl-row' ).eq( 0 ).trigger( 'mouseleave' );
							$( '.fl-module' ).eq( 0 ).trigger( 'mouseleave' );  
						}
					},
					{
						animation   : false,
						element     : '.fl-builder-templates-button',
						placement   : 'bottom',
						title       : FLBuilderStrings.tourTemplatesButtonTitle,
						content     : FLBuilderStrings.tourTemplatesButton,
						onShow      : function() {
							FLBuilderTour._dimSection( 'body' );
						}
					},
					{
						animation   : false,
						element     : '.fl-builder-tools-button',
						placement   : 'bottom',
						title       : FLBuilderStrings.tourToolsButtonTitle,
						content     : FLBuilderStrings.tourToolsButton,
						onShow      : function() {
							FLBuilderTour._dimSection( 'body' );
						}
					},
					{
						animation   : false,
						element     : '.fl-builder-done-button',
						placement   : 'bottom',
						title       : FLBuilderStrings.tourDoneButtonTitle,
						content     : FLBuilderStrings.tourDoneButton,
						onShow      : function() {
							FLBuilderTour._dimSection( 'body' );
						}
					},
					{
						animation   : false,
						orphan      : true,
						backdrop    : true,
						title       : FLBuilderStrings.tourFinishedTitle,
						content     : FLBuilderStrings.tourFinished,
						template    : '<div class="popover" role="tooltip"> <div class="arrow"></div> <i class="fa fa-times" data-role="end"></i> <h3 class="popover-title"></h3> <div class="popover-content"></div> <div class="popover-navigation clearfix"> <button class="fl-builder-button fl-builder-button-primary fl-builder-tour-next" data-role="end">' + FLBuilderStrings.tourEnd + '</button> </div> </div>',
					}
				]
			};
			
			// Remove the first step if no templates.
			if( FLBuilderConfig.lite ) {
				config.steps.shift();
			}
			else if ( 'disabled' == FLBuilderConfig.enabledTemplates ) {
				config.steps.shift();
			}
			else if ( 'fl-builder-template' == FLBuilderConfig.postType ) {
				config.steps.shift();
			}
			
			return config;
		},
		
		/**
		 * Callback for when the tour starts.
		 *
		 * @since 1.4.9
		 * @access private
		 * @method _onStart
		 */
		_onStart: function()
		{
			var body = $( 'body' );
			
			body.append( '<div class="fl-builder-tour-mask"></div>' );
			body.on( 'fl-builder.template-selector-loaded', FLBuilderTour._templateSelectorLoaded );
			
			if ( 0 === $( '.fl-row' ).length && 'module' != FLBuilderConfig.userTemplateType ) {
				$( '.fl-builder-content' ).append( '<div class="fl-builder-tour-demo-content fl-row fl-row-fixed-width fl-row-bg-none"> <div class="fl-row-content-wrap"> <div class="fl-row-content fl-row-fixed-width fl-node-content"> <div class="fl-col-group"> <div class="fl-col" style="width:100%"> <div class="fl-col-content fl-node-content"> <div class="fl-module fl-module-rich-text" data-type="rich-text" data-name="Text Editor"> <div class="fl-module-content fl-node-content"> <div class="fl-rich-text"> <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vivamus pellentesque ut lorem non cursus. Sed mauris nunc, porttitor iaculis lorem a, sollicitudin lacinia sapien. Proin euismod orci lacus, et sollicitudin leo posuere ac. In hac habitasse platea dictumst. Maecenas elit magna, consequat in turpis suscipit, ultrices rhoncus arcu. Phasellus finibus sapien nec elit tempus venenatis. Maecenas tincidunt sapien non libero maximus, in aliquam felis tincidunt. Mauris mollis ultricies facilisis. Duis condimentum dignissim tortor sit amet facilisis. Aenean gravida lacus eu risus molestie egestas. Donec ut dolor dictum, fringilla metus malesuada, viverra nunc. Maecenas ut purus ac justo aliquet lacinia. Cras vestibulum elementum tincidunt. Maecenas mattis tortor neque, consectetur dignissim neque tempor nec.</p></div> </div> </div> </div> </div> </div> </div> </div> </div>' );
				FLBuilder._setupEmptyLayout();
				FLBuilder._highlightEmptyCols();
			}
		},
		
		/**
		 * Callback for when the tour is navigated
		 * to the previous step.
		 *
		 * @since 1.4.9
		 * @access private
		 * @method _onPrev
		 */
		_onPrev: function()
		{
			$( '.fl-builder-tour-dimmed' ).remove();
		},
		
		/**
		 * Callback for when the tour is navigated
		 * to the next step.
		 *
		 * @since 1.4.9
		 * @access private
		 * @method _onNext
		 */
		_onNext: function()
		{
			$( '.fl-builder-tour-dimmed' ).remove();
		},
		
		/**
		 * Callback for when the tour ends.
		 *
		 * @since 1.4.9
		 * @access private
		 * @method _onEnd
		 */
		_onEnd: function()
		{
			$( 'body' ).off( 'fl-builder.template-selector-loaded' );
			$( '.fl-builder-tour-mask' ).remove();
			$( '.fl-builder-tour-dimmed' ).remove();
			$( '.fl-builder-tour-demo-content' ).remove();

			FLBuilder._setupEmptyLayout();
			FLBuilder._highlightEmptyCols();
			FLBuilder._showPanel();
			FLBuilder._initTemplateSelector();
		},
		
		/**
		 * Dims a section of the page.
		 *
		 * @since 1.4.9
		 * @access private
		 * @method _dimSection
		 * @param {String} selector A CSS selector for the section to dim.
		 */
		_dimSection: function( selector )
		{
			$( selector ).find( '.fl-builder-tour-dimmed' ).remove();
			$( selector ).append( '<div class="fl-builder-tour-dimmed"></div>' );
		},
		
		/**
		 * Fires when the template selector loads
		 * and positions the popup.
		 *
		 * @since 1.4.9
		 * @access private
		 * @method _templateSelectorLoaded
		 */
		_templateSelectorLoaded: function()
		{
			var header = $( '.fl-builder-settings-lightbox .fl-lightbox-header' ),
				height = header.height(),
				top    = header.offset().top + 75;
			
			$( '.popover[class*=tour-]' ).css({
				top: ( top + height) + 'px',
				visibility: 'visible'
			});
		}
	};

})( jQuery );