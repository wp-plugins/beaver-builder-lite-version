(function($){

	/**
	 * The main builder interface class.
	 * 
	 * @since 1.0
	 * @class FLBuilder
	 */
	FLBuilder = {
	
		/**
		 * An instance of FLBuilderPreview for working
		 * with the current live preview.
		 *
		 * @since 1.3.3
		 * @property {FLBuilderPreview} preview
		 */
		preview                     : null,
	
		/**
		 * An instance of FLLightbox for displaying a list
		 * of actions a user can take such as publish or cancel.
		 *
		 * @since 1.0
		 * @access private
		 * @property {FLLightbox} _actionsLightbox
		 */
		_actionsLightbox            : null,
		
		/**
		 * A jQuery reference to a module element that should be
		 * added to a new row after it has been rendered.
		 *
		 * @since 1.0
		 * @access private
		 * @property {Object} _addModuleAfterRowRender
		 */
		_addModuleAfterRowRender    : null,
		
		/**
		 * An object that holds data for column resizing.
		 *
		 * @since 1.6.4
		 * @access private
		 * @property {Object} _colResizeData
		 */
		_colResizeData              : null,
		
		/**
		 * A flag for whether a column is being resized or not.
		 *
		 * @since 1.6.4
		 * @access private
		 * @property {Boolean} _colResizing
		 */
		_colResizing              	: false,
		
		/**
		 * The CSS class of the main content wrapper for the
		 * current layout that is being worked on.
		 *
		 * @since 1.0
		 * @access private
		 * @property {String} _contentClass
		 */
		_contentClass               : false,
		
		/**
		 * Whether dragging has been enabled or not.
		 *
		 * @since 1.0
		 * @access private
		 * @property {Boolean} _dragEnabled
		 */
		_dragEnabled                : false,
		
		/**
		 * Whether an element is currently being dragged or not.
		 *
		 * @since 1.0
		 * @access private
		 * @property {Boolean} _dragging
		 */
		_dragging                   : false,
		
		/**
		 * The URL to redirect to when a user leaves the builder.
		 *
		 * @since 1.0
		 * @access private
		 * @property {String} _exitUrl
		 */
		_exitUrl                    : null,
		
		/**
		 * An instance of FLLightbox for displaying settings.
		 *
		 * @since 1.0
		 * @access private
		 * @property {FLLightbox} _lightbox
		 */
		_lightbox                   : null,
		
		/**
		 * A timeout for refreshing the height of lightbox scrollbars
		 * in case the content changes from dynamic settings.
		 *
		 * @since 1.0
		 * @access private
		 * @property {Object} _lightboxScrollbarTimeout
		 */
		_lightboxScrollbarTimeout   : null,
		
		/**
		 * An array that's used to cache which module settings
		 * CSS and JS assets have already been loaded so they
		 * are only loaded once.
		 * 
		 * @since 1.0
		 * @access private
		 * @property {Array} _loadedModuleAssets
		 */
		_loadedModuleAssets         : [],
		
		/**
		 * An object used to store module settings helpers.
		 * 
		 * @since 1.0
		 * @access private
		 * @property {Object} _moduleHelpers
		 */
		_moduleHelpers              : {},
		
		/**
		 * An instance of wp.media used to select multiple photos.
		 * 
		 * @since 1.0
		 * @access private
		 * @property {Object} _multiplePhotoSelector
		 */
		_multiplePhotoSelector      : null,
		
		/**
		 * A jQuery reference to a row that a new column group
		 * should be added to once it's finished rendering.
		 * 
		 * @since 1.0
		 * @access private
		 * @property {Object} _newColGroupParent
		 */
		_newColGroupParent          : null,
		
		/**
		 * The position a column group should be added to within
		 * a row once it finishes rendering.
		 * 
		 * @since 1.0
		 * @access private
		 * @property {Number} _newColGroupPosition
		 */
		_newColGroupPosition        : 0,
		
		/**
		 * The position a row should be added to within
		 * the layout once it finishes rendering.
		 * 
		 * @since 1.0
		 * @access private
		 * @property {Number} _newRowPosition
		 */
		_newRowPosition             : 0,
		
		/**
		 * The ID of a template that the user has selected.
		 * 
		 * @since 1.0
		 * @access private
		 * @property {Number} _selectedTemplateId
		 */
		_selectedTemplateId         : null,
		
		/**
		 * The type of template that the user has selected.
		 * Possible values are "core" or "user".
		 * 
		 * @since 1.0
		 * @access private
		 * @property {String} _selectedTemplateType
		 */ 
		_selectedTemplateType       : null,
		
		/**
		 * An instance of wp.media used to select a single photo.
		 * 
		 * @since 1.0
		 * @access private
		 * @property {Object} _singlePhotoSelector
		 */ 
		_singlePhotoSelector        : null,
		
		/**
		 * An instance of wp.media used to select a single video.
		 * 
		 * @since 1.0
		 * @access private
		 * @property {Object} _singleVideoSelector
		 */ 
		_singleVideoSelector        : null,
		
		/**
		 * Whether the current AJAX update is silent or not. Silent
		 * updates occur without blocking the page with the loading
		 * overlay. If another AJAX request is made during a silent 
		 * update, the loading overlay will be shown and the data for
		 * the second request will be stored so it can be made when 
		 * the silent update completes.
		 * 
		 * @since 1.0
		 * @access private
		 * @property {Boolean} _silentUpdate
		 */ 
		_silentUpdate               : false,
		
		/**
		 * Data for an AJAX request that should be made once a silent
		 * update has completed.
		 * 
		 * @since 1.0
		 * @access private
		 * @property {Object} _silentUpdateCallbackData
		 */ 
		_silentUpdateCallbackData   : null,
	
		/**
		 * Initializes the builder interface.
		 *
		 * @since 1.0
		 * @access private
		 * @method _init
		 */
		_init: function()
		{
			FLBuilder._initJQueryReadyFix();
			FLBuilder._initGlobalErrorHandling();
			FLBuilder._initPostLock();
			FLBuilder._initClassNames();
			FLBuilder._initMediaUploader();
			FLBuilder._initOverflowFix();
			FLBuilder._initScrollbars();
			FLBuilder._initLightboxes();
			FLBuilder._initSortables();
			FLBuilder._initCoreTemplateSettings();
			FLBuilder._bindEvents();
			FLBuilder._bindOverlayEvents();
			FLBuilder._setupEmptyLayout();
			FLBuilder._highlightEmptyCols();
			FLBuilder._showTourOrTemplates();
		},
		
		/**
		 * Prevent errors thrown in jQuery's ready function
		 * from breaking subsequent ready calls. 
		 *
		 * @since 1.4.6
		 * @access private
		 * @method _initJQueryReadyFix
		 */
		_initJQueryReadyFix: function()
		{
			if ( FLBuilderConfig.debug ) {
				return;
			}
			
			jQuery.fn.oldReady = jQuery.fn.ready;
			
			jQuery.fn.ready = function( fn ) {
				return jQuery.fn.oldReady( function() {
					try {
						if ( 'function' == typeof fn ) {
							fn();
						}
					}
					catch ( e ){
						FLBuilder.logError( e );
					}
				});
			};
		},
		
		/**
		 * Try to prevent errors from third party plugins
		 * from breaking the builder.
		 *
		 * @since 1.4.6
		 * @access private
		 * @method _initGlobalErrorHandling
		 */
		_initGlobalErrorHandling: function()
		{
			if ( FLBuilderConfig.debug ) {
				return;
			}
			
			window.onerror = function( message, file, line, col, error ) {
				FLBuilder.logGlobalError( message, file, line, col, error );
				return true;
			};
		},
		
		/**
		 * Send a wp.heartbeat request to lock editing of this
		 * post so it can only be edited by the current user.
		 *
		 * @since 1.0.6
		 * @access private
		 * @method _initPostLock
		 */
		_initPostLock: function()
		{
			if(typeof wp.heartbeat != 'undefined') {
			
				wp.heartbeat.interval(30);
				
				wp.heartbeat.enqueue('fl_builder_post_lock', {
					post_id: $('#fl-post-id').val()
				});
			}
		},
		
		/**
		 * Initializes html and body classes as well as the
		 * builder content class for this post.
		 *
		 * @since 1.0
		 * @access private
		 * @method _initClassNames
		 */
		_initClassNames: function()
		{
			$('html').addClass('fl-builder-edit');
			$('body').addClass('fl-builder');
			
			if(FLBuilderConfig.simpleUi) {
				$('body').addClass('fl-builder-simple');
			}
			
			FLBuilder._contentClass = '.fl-builder-content-' + FLBuilderConfig.postId;
		},
		
		/**
		 * Initializes the WordPress media uploader so any files
		 * uploaded will be attached to the current post.
		 *
		 * @since 1.2.2
		 * @access private
		 * @method _initMediaUploader
		 */
		_initMediaUploader: function()
		{
			wp.media.model.settings.post.id = $('#fl-post-id').val();
		},
		
		/**
		 * Third party themes that set their content wrappers to
		 * overflow:hidden break builder overlays. We set them
		 * to overflow:visible while editing.
		 *
		 * @since 1.0
		 * @access private
		 * @method _initOverflowFix
		 */
		_initOverflowFix: function()
		{
			$(FLBuilder._contentClass).parents().css('overflow', 'visible');
		},
		
		/**
		 * Initializes Nano Scroller scrollbars for the 
		 * builder interface.
		 *
		 * @since 1.0
		 * @access private
		 * @method _initScrollbars
		 */
		_initScrollbars: function()
		{
			$('.fl-nanoscroller').nanoScroller({
				alwaysVisible: true,
				preventPageScrolling: true,
				paneClass: 'fl-nanoscroller-pane',
				sliderClass: 'fl-nanoscroller-slider',
				contentClass: 'fl-nanoscroller-content'
			});
		},
		
		/**
		 * Initializes the lightboxes for the builder interface.
		 *
		 * @since 1.0
		 * @access private
		 * @method _initLightboxes
		 */
		_initLightboxes: function()
		{
			/* Main builder lightbox */
			FLBuilder._lightbox = new FLLightbox({
				className: 'fl-builder-lightbox fl-builder-settings-lightbox'
			});
			
			FLBuilder._lightbox.on('close', FLBuilder._lightboxClosed);
			
			/* Actions lightbox */
			FLBuilder._actionsLightbox = new FLLightbox({
				className: 'fl-builder-actions-lightbox'
			});
		},
		
		/**
		 * Initializes jQuery sortables for drag and drop.
		 *
		 * @since 1.0
		 * @access private
		 * @method _initSortables
		 */
		_initSortables: function()
		{
			var defaults = {
				appendTo: 'body',
				cursor: 'move',
				cursorAt: {
					left: 25, 
					top: 20
				},
				distance: 1,
				helper: FLBuilder._blockDragHelper,
				start : FLBuilder._blockDragStart,
				sort: FLBuilder._blockDragSort,
				placeholder: 'fl-builder-drop-zone',
				tolerance: 'intersect'
			},
			rowConnections 		= '',
			moduleConnections 	= '';
			
			// Row Connections.
			if ( 'row' == FLBuilderConfig.userTemplateType )  {
				rowConnections = FLBuilder._contentClass + ' .fl-row-content';
			}
			else {
				rowConnections = FLBuilder._contentClass + ', ' + 
							  	 FLBuilder._contentClass + ' .fl-row:not(.fl-node-global) .fl-row-content';
			}
			
			// Module Connections.
			if ( 'row' == FLBuilderConfig.userTemplateType )  {
				moduleConnections = FLBuilder._contentClass + ' .fl-row-content, ' + 
							  		FLBuilder._contentClass + ' .fl-col-content';
			}
			else {
				moduleConnections = FLBuilder._contentClass + ', ' + 
							  		FLBuilder._contentClass + ' .fl-row:not(.fl-node-global) .fl-row-content, ' + 
							  		FLBuilder._contentClass + ' .fl-col:not(.fl-node-global) .fl-col-content';
			}
			
			// Row layouts from the builder panel.
			$('.fl-builder-rows').sortable($.extend({}, defaults, {
				connectWith: rowConnections,
				items: '.fl-builder-block-row',
				stop: FLBuilder._rowDragStop
			}));
			
			// Row templates from the builder panel.
			$('.fl-builder-row-templates').sortable($.extend({}, defaults, {
				connectWith: FLBuilder._contentClass,
				items: '.fl-builder-block-row-template',
				stop: FLBuilder._nodeTemplateDragStop
			}));
			
			// Saved rows from the builder panel.
			$('.fl-builder-saved-rows').sortable($.extend({}, defaults, {
				cancel: '.fl-builder-node-template-actions, .fl-builder-node-template-edit, .fl-builder-node-template-delete',
				connectWith: FLBuilder._contentClass,
				items: '.fl-builder-block-saved-row',
				stop: FLBuilder._nodeTemplateDragStop
			}));
			
			// Modules from the builder panel.
			$('.fl-builder-modules, .fl-builder-widgets').sortable($.extend({}, defaults, {
				connectWith: moduleConnections,
				items: '.fl-builder-block-module',
				stop: FLBuilder._moduleDragStop
			}));
			
			// Module templates from the builder panel.
			$('.fl-builder-module-templates').sortable($.extend({}, defaults, {
				connectWith: moduleConnections,
				items: '.fl-builder-block-module-template',
				stop: FLBuilder._nodeTemplateDragStop
			}));
			
			// Saved modules from the builder panel.
			$('.fl-builder-saved-modules').sortable($.extend({}, defaults, {
				cancel: '.fl-builder-node-template-actions, .fl-builder-node-template-edit, .fl-builder-node-template-delete',
				connectWith: moduleConnections,
				items: '.fl-builder-block-saved-module',
				stop: FLBuilder._nodeTemplateDragStop
			}));
			
			// Row position.
			$(FLBuilder._contentClass).sortable($.extend({}, defaults, {
				handle: '.fl-row-overlay .fl-block-overlay-actions .fl-block-move',
				helper: FLBuilder._rowDragHelper,
				items: '.fl-row',
				stop: FLBuilder._rowDragStop
			}));
			
			// Column group position.
			$(FLBuilder._contentClass + ' .fl-row-content').sortable($.extend({}, defaults, {
				handle: '.fl-row-overlay .fl-block-overlay-actions .fl-block-move',
				helper: FLBuilder._rowDragHelper,
				items: '.fl-col-group',
				stop: FLBuilder._rowDragStop
			}));
			
			// Module position.
			$(FLBuilder._contentClass + ' .fl-col-content').sortable($.extend({}, defaults, {
				connectWith: moduleConnections,
				handle: '.fl-module-overlay .fl-block-overlay-actions .fl-block-move',
				helper: FLBuilder._moduleDragHelper,
				items: '.fl-module',
				stop: FLBuilder._moduleDragStop
			}));
		},
		
		/**
		 * Binds most of the events for the builder interface.
		 *
		 * @since 1.0
		 * @access private
		 * @method _bindEvents
		 */
		_bindEvents: function()
		{
			/* Links */
			$('a').on('click', FLBuilder._preventDefault);
			$('.fl-page-nav .nav a').on('click', FLBuilder._headerLinkClicked);
			
			/* Heartbeat */
			$(document).on('heartbeat-tick', FLBuilder._initPostLock);
			
			/* Unload Warning */
			$(window).on('beforeunload', FLBuilder._warnBeforeUnload);
			
			/* Submenus */
			$('body').delegate('.fl-builder-has-submenu', 'click', FLBuilder._submenuParentClicked);
			$('body').delegate('.fl-builder-has-submenu a', 'click', FLBuilder._submenuChildClicked);
			$('body').delegate('.fl-builder-submenu', 'mouseenter', FLBuilder._submenuMouseenter);
			$('body').delegate('.fl-builder-submenu', 'mouseleave', FLBuilder._submenuMouseleave);
			
			/* Bar */
			$('.fl-builder-tools-button').on('click', FLBuilder._toolsClicked);
			$('.fl-builder-done-button').on('click', FLBuilder._doneClicked);
			$('.fl-builder-add-content-button').on('click', FLBuilder._showPanel);
			$('.fl-builder-templates-button').on('click', FLBuilder._changeTemplateClicked);
			$('.fl-builder-buy-button').on('click', FLBuilder._upgradeClicked);
			$('.fl-builder-upgrade-button').on('click', FLBuilder._upgradeClicked);
			$('.fl-builder-help-button').on('click', FLBuilder._helpButtonClicked);
			
			/* Panel */
			$('.fl-builder-panel-actions .fl-builder-panel-close').on('click', FLBuilder._closePanel);
			$('.fl-builder-blocks-section-title').on('click', FLBuilder._blockSectionTitleClicked);
			$('body').delegate('.fl-builder-node-template-actions', 'mousedown', FLBuilder._stopPropagation);
			$('body').delegate('.fl-builder-node-template-edit', 'mousedown', FLBuilder._stopPropagation);
			$('body').delegate('.fl-builder-node-template-delete', 'mousedown', FLBuilder._stopPropagation);
			$('body').delegate('.fl-builder-node-template-edit', 'click', FLBuilder._editNodeTemplateClicked);
			$('body').delegate('.fl-builder-node-template-delete', 'click', FLBuilder._deleteNodeTemplateClicked);
			
			/* Drag and Drop */
			$('body').delegate('.fl-builder-block', 'mousedown', FLBuilder._blockDragInit);
			$('body').on('mouseup', FLBuilder._blockDragCancel);
			
			/* Actions Lightbox */
			$('body').delegate('.fl-builder-actions .fl-builder-cancel-button', 'click', FLBuilder._cancelButtonClicked);
			
			/* Save Actions */
			$('body').delegate('.fl-builder-save-actions .fl-builder-publish-button', 'click', FLBuilder._publishButtonClicked);
			$('body').delegate('.fl-builder-save-actions .fl-builder-draft-button', 'click', FLBuilder._draftButtonClicked);
			$('body').delegate('.fl-builder-save-actions .fl-builder-discard-button', 'click', FLBuilder._discardButtonClicked);
			
			/* Tools Actions */
			$('body').delegate('.fl-builder-duplicate-page-button', 'click', FLBuilder._duplicatePageClicked);
			$('body').delegate('.fl-builder-save-user-template-button', 'click', FLBuilder._saveUserTemplateClicked);
			$('body').delegate('.fl-builder-global-settings-button', 'click', FLBuilder._globalSettingsClicked);
			$('body').delegate('.fl-builder-global-settings .fl-builder-settings-save', 'click', FLBuilder._saveGlobalSettingsClicked);
			
			/* Template Selector */
			$('body').delegate('.fl-template-category-select', 'change', FLBuilder._templateCategoryChanged);
			$('body').delegate('.fl-template-preview', 'click', FLBuilder._templateClicked);
			$('body').delegate('.fl-user-template', 'click', FLBuilder._userTemplateClicked);
			$('body').delegate('.fl-user-template-edit', 'click', FLBuilder._editUserTemplateClicked);
			$('body').delegate('.fl-user-template-delete', 'click', FLBuilder._deleteUserTemplateClicked);
			$('body').delegate('.fl-builder-template-replace-button', 'click', FLBuilder._templateReplaceClicked);
			$('body').delegate('.fl-builder-template-append-button', 'click', FLBuilder._templateAppendClicked);
			$('body').delegate('.fl-builder-template-actions .fl-builder-cancel-button', 'click', FLBuilder._templateCancelClicked);
			
			/* User Template Settings */
			$('body').delegate('.fl-builder-user-template-settings .fl-builder-settings-save', 'click', FLBuilder._saveUserTemplateSettings);
			
			/* Help Actions */
			$('body').delegate('.fl-builder-help-tour-button', 'click', FLBuilder._startHelpTour);
			$('body').delegate('.fl-builder-help-video-button', 'click', FLBuilder._watchVideoClicked);
			$('body').delegate('.fl-builder-knowledge-base-button', 'click', FLBuilder._viewKnowledgeBaseClicked);
			$('body').delegate('.fl-builder-forums-button', 'click', FLBuilder._visitForumsClicked);
			
			/* Welcome Actions */
			$('body').delegate('.fl-builder-no-tour-button', 'click', FLBuilder._noTourButtonClicked);
			$('body').delegate('.fl-builder-yes-tour-button', 'click', FLBuilder._yesTourButtonClicked);
			
			/* Alert Lightbox */
			$('body').delegate('.fl-builder-alert-close', 'click', FLBuilder._alertClose);
			
			/* Rows */
			$('body').delegate('.fl-row-overlay .fl-block-remove', 'click', FLBuilder._deleteRowClicked);
			$('body').delegate('.fl-row-overlay .fl-block-copy', 'click', FLBuilder._rowCopyClicked);
			$('body').delegate('.fl-row-overlay .fl-block-move', 'mousedown', FLBuilder._blockDragInit);
			$('body').delegate('.fl-row-overlay .fl-block-settings', 'click', FLBuilder._rowSettingsClicked);
			$('body').delegate('.fl-row-overlay', 'click', FLBuilder._rowSettingsClicked);
			$('body').delegate('.fl-builder-row-settings .fl-builder-settings-save', 'click', FLBuilder._saveSettings);
			
			/* Columns */
			$('body').delegate('.fl-col-overlay', 'click', FLBuilder._colSettingsClicked);
			$('body').delegate('.fl-builder-col-settings .fl-builder-settings-save', 'click', FLBuilder._saveSettings);
			$('body').delegate('.fl-col-overlay .fl-block-remove', 'click', FLBuilder._deleteColClicked);
			
			/* Columns Submenu */
			$('body').delegate('.fl-block-col-submenu .fl-block-col-edit', 'click', FLBuilder._colSettingsClicked);
			$('body').delegate('.fl-block-col-submenu .fl-block-col-delete', 'click', FLBuilder._deleteColClicked);
			$('body').delegate('.fl-block-col-submenu .fl-block-col-insert-before', 'click', FLBuilder._insertColBeforeClicked);
			$('body').delegate('.fl-block-col-submenu .fl-block-col-insert-after', 'click', FLBuilder._insertColAfterClicked);
			$('body').delegate('.fl-block-col-submenu .fl-block-col-reset', 'click', FLBuilder._resetColumnWidths);
			
			/* Modules */
			$('body').delegate('.fl-module-overlay .fl-block-remove', 'click', FLBuilder._deleteModuleClicked);
			$('body').delegate('.fl-module-overlay .fl-block-copy', 'click', FLBuilder._moduleCopyClicked);
			$('body').delegate('.fl-module-overlay .fl-block-move', 'mousedown', FLBuilder._blockDragInit);
			$('body').delegate('.fl-module-overlay .fl-block-settings', 'click', FLBuilder._moduleSettingsClicked);
			$('body').delegate('.fl-module-overlay', 'click', FLBuilder._moduleSettingsClicked);
			$('body').delegate('.fl-builder-module-settings .fl-builder-settings-save', 'click', FLBuilder._saveModuleClicked);
			
			/* Node Templates */
			$('body').delegate('.fl-builder-settings-save-as', 'click', FLBuilder._showNodeTemplateSettings);
			$('body').delegate('.fl-builder-node-template-settings .fl-builder-settings-save', 'click', FLBuilder._saveNodeTemplate);
			
			/* Settings */
			$('body').delegate('.fl-builder-settings-tabs a', 'click', FLBuilder._settingsTabClicked);
			$('body').delegate('.fl-builder-settings-cancel', 'click', FLBuilder._settingsCancelClicked);
			
			/* Tooltips */
			$('body').delegate('.fl-help-tooltip-icon', 'mouseover', FLBuilder._showHelpTooltip);
			$('body').delegate('.fl-help-tooltip-icon', 'mouseout', FLBuilder._hideHelpTooltip);
			
			/* Multiple Fields */
			$('body').delegate('.fl-builder-field-add', 'click', FLBuilder._addFieldClicked);
			$('body').delegate('.fl-builder-field-copy', 'click', FLBuilder._copyFieldClicked);
			$('body').delegate('.fl-builder-field-delete', 'click', FLBuilder._deleteFieldClicked);
			
			/* Select Fields */
			$('body').delegate('.fl-builder-settings-fields select', 'change', FLBuilder._settingsSelectChanged);
			
			/* Photo Fields */
			$('body').delegate('.fl-photo-field .fl-photo-select', 'click', FLBuilder._selectSinglePhoto);
			$('body').delegate('.fl-photo-field .fl-photo-edit', 'click', FLBuilder._selectSinglePhoto);
			$('body').delegate('.fl-photo-field .fl-photo-replace', 'click', FLBuilder._selectSinglePhoto);
			
			/* Multiple Photo Fields */
			$('body').delegate('.fl-multiple-photos-field .fl-multiple-photos-select', 'click', FLBuilder._selectMultiplePhotos);
			$('body').delegate('.fl-multiple-photos-field .fl-multiple-photos-edit', 'click', FLBuilder._selectMultiplePhotos);
			$('body').delegate('.fl-multiple-photos-field .fl-multiple-photos-add', 'click', FLBuilder._selectMultiplePhotos);
			
			/* Video Fields */
			$('body').delegate('.fl-video-field .fl-video-select', 'click', FLBuilder._selectSingleVideo);
			$('body').delegate('.fl-video-field .fl-video-replace', 'click', FLBuilder._selectSingleVideo);
			
			/* Icon Fields */
			$('body').delegate('.fl-icon-field .fl-icon-select', 'click', FLBuilder._selectIcon);
			$('body').delegate('.fl-icon-field .fl-icon-replace', 'click', FLBuilder._selectIcon);
			$('body').delegate('.fl-icon-field .fl-icon-remove', 'click', FLBuilder._removeIcon);
			
			/* Settings Form Fields */
			$('body').delegate('.fl-form-field .fl-form-field-edit', 'click', FLBuilder._formFieldClicked);
			$('body').delegate('.fl-form-field-settings .fl-builder-settings-save', 'click', FLBuilder._saveFormFieldClicked);
			
			/* Layout Fields */
			$('body').delegate('.fl-layout-field-option', 'click', FLBuilder._layoutFieldClicked);
			
			/* Links Fields */
			$('body').delegate('.fl-link-field-select', 'click', FLBuilder._linkFieldSelectClicked);
			$('body').delegate('.fl-link-field-search-cancel', 'click', FLBuilder._linkFieldSelectCancelClicked);
			
			/* Loop Builder Fields */
			$('body').delegate('.fl-loop-builder select[name=post_type]', 'change', FLBuilder._loopBuilderPostTypeChange);
		},
		
		/**
		 * Binds the events for overlays that appear when 
		 * mousing over a row, column or module.
		 *
		 * @since 1.0
		 * @access private
		 * @method _bindOverlayEvents
		 */
		_bindOverlayEvents: function()
		{
			var content = $(FLBuilder._contentClass);
			
			content.delegate('.fl-row', 'mouseenter', FLBuilder._rowMouseenter);
			content.delegate('.fl-row', 'mouseleave', FLBuilder._rowMouseleave);
			content.delegate('.fl-row-overlay', 'mouseleave', FLBuilder._rowMouseleave);
			content.delegate('.fl-col', 'mouseenter', FLBuilder._colMouseenter);
			content.delegate('.fl-col', 'mouseleave', FLBuilder._colMouseleave);
			content.delegate('.fl-module', 'mouseenter', FLBuilder._moduleMouseenter);
			content.delegate('.fl-module', 'mouseleave', FLBuilder._moduleMouseleave);
		},
		
		/**
		 * Unbinds the events for overlays that appear when 
		 * mousing over a row, column or module.
		 *
		 * @since 1.0
		 * @access private
		 * @method _destroyOverlayEvents
		 */
		_destroyOverlayEvents: function()
		{
			var content = $(FLBuilder._contentClass);
			
			content.undelegate('.fl-row', 'mouseenter', FLBuilder._rowMouseenter);
			content.undelegate('.fl-row', 'mouseleave', FLBuilder._rowMouseleave);
			content.undelegate('.fl-row-overlay', 'mouseleave', FLBuilder._rowMouseleave);
			content.undelegate('.fl-col', 'mouseenter', FLBuilder._colMouseenter);
			content.undelegate('.fl-col', 'mouseleave', FLBuilder._colMouseleave);
			content.undelegate('.fl-module', 'mouseenter', FLBuilder._moduleMouseenter);
			content.undelegate('.fl-module', 'mouseleave', FLBuilder._moduleMouseleave);
		},
		
		/**
		 * Prevents the default action for an event.
		 *
		 * @since 1.6.3
		 * @access private
		 * @method _preventDefault
		 * @param {Object} e The event object.
		 */
		_preventDefault: function( e )
		{
			e.preventDefault();
		},
		
		/**
		 * Prevents propagation of an event.
		 *
		 * @since 1.6.3
		 * @access private
		 * @method _stopPropagation
		 * @param {Object} e The event object.
		 */
		_stopPropagation: function( e )
		{
			e.stopPropagation();
		},
		
		/**
		 * Launches the builder for another page if a link in the
		 * builder theme header is clicked.
		 *
		 * @since 1.3.9
		 * @access private
		 * @method _headerLinkClicked
		 * @param {Object} e The event object.
		 */
		_headerLinkClicked: function(e)
		{
			var link = $(this),
				href = link.attr('href');
			
			e.preventDefault();
			
			if ( FLBuilderConfig.isUserTemplate )  {
				return;
			}
			
			FLBuilder._exitUrl = href.indexOf('?') > -1 ? href : href + '?fl_builder';
			FLBuilder._doneClicked();
		},
		
		/**
		 * Warns the user that their changes won't be saved if
		 * they leave the page while editing settings.
		 *
		 * @since 1.0.6
		 * @access private
		 * @method _warnBeforeUnload
		 * @return {String} The warning message.
		 */ 
		_warnBeforeUnload: function()
		{
			var rowSettings     = $('.fl-builder-row-settings').length > 0,
				colSettings     = $('.fl-builder-col-settings').length > 0,
				moduleSettings  = $('.fl-builder-module-settings').length > 0;
			
			if(rowSettings || colSettings || moduleSettings) {
				return FLBuilderStrings.unloadWarning;
			}
		},
		
		/* TipTips
		----------------------------------------------------------*/
		
		/**
		 * Initializes tooltip help messages.
		 *
		 * @since 1.1.9
		 * @access private
		 * @method _initTipTips
		 */
		_initTipTips: function()
		{
			$('.fl-tip').tipTip();
		},
		
		/**
		 * Removes all tooltip help messages from the screen.
		 *
		 * @since 1.1.9
		 * @access private
		 * @method _hideTipTips
		 */
		_hideTipTips: function()
		{
			$('#tiptip_holder').stop().remove();
		},
		
		/* Submenus
		----------------------------------------------------------*/
		
		/**
		 * Callback for when the parent of a submenu is clicked.
		 *
		 * @since 1.6.4
		 * @access private
		 * @method _submenuParentClicked
		 * @param {Object} e The event object.
		 */
		_submenuParentClicked: function( e )
		{
			var parent 	 = $( this ),
				submenu  = parent.find( '.fl-builder-submenu' );
				
			if ( parent.hasClass( 'fl-builder-submenu-open' ) ) {
				parent.removeClass( 'fl-builder-submenu-open' );
				parent.removeClass( 'fl-builder-submenu-right' );
			}
			else {
				
				if( parent.offset().left + submenu.width() > $( window ).width() ) {
					parent.addClass( 'fl-builder-submenu-right' );
				}
				
				parent.addClass( 'fl-builder-submenu-open' );
			}
			
			FLBuilder._hideTipTips();
			e.preventDefault();
			e.stopPropagation();
		},
		
		/**
		 * Callback for when the child of a submenu is clicked.
		 *
		 * @since 1.6.4
		 * @access private
		 * @method _submenuChildClicked
		 * @param {Object} e The event object.
		 */
		_submenuChildClicked: function( e )
		{
			$( this ).closest( '.fl-builder-submenu-open' ).removeClass( 'fl-builder-submenu-open' );
		},
		
		/**
		 * Callback for when the mouse enters a submenu.
		 *
		 * @since 1.6.4
		 * @access private
		 * @method _submenuMouseenter
		 * @param {Object} e The event object.
		 */
		_submenuMouseenter: function( e )
		{
			var menu 	= $( this ),
				timeout = menu.data( 'timeout' );
				
			if ( 'undefined' != typeof timeout ) {
				clearTimeout( timeout );
			}
		},
		
		/**
		 * Callback for when the mouse leaves a submenu.
		 *
		 * @since 1.6.4
		 * @access private
		 * @method _submenuMouseleave
		 * @param {Object} e The event object.
		 */
		_submenuMouseleave: function( e )
		{
			var menu 	= $( this ),
				timeout = setTimeout( function(){
					menu.closest( '.fl-builder-submenu-open' ).removeClass( 'fl-builder-submenu-open' );
				}, 500 );
			
			menu.data( 'timeout', timeout );
		},
		
		/* Bar
		----------------------------------------------------------*/
		
		/**
		 * Shows the actions lightbox when the tools button is clicked.
		 *
		 * @since 1.0
		 * @access private
		 * @method _toolsClicked
		 */
		_toolsClicked: function()
		{
			var buttons             = {},
				lite                = FLBuilderConfig.lite,
				enabledTemplates    = FLBuilderConfig.enabledTemplates;
				
			// Duplicate button
			if(FLBuilderConfig.isUserTemplate) {
				if ( typeof window.opener == 'undefined' || ! window.opener ) {
					buttons['duplicate-page'] = FLBuilderStrings.duplicateTemplate;
				}
			}
			else {
				buttons['duplicate-page'] = FLBuilderStrings.duplicatePage;
			}
			
			// Template buttons
			if(!lite && !FLBuilderConfig.isUserTemplate && (enabledTemplates == 'enabled' || enabledTemplates == 'user')) {
			
				buttons['save-user-template'] = FLBuilderStrings.saveTemplate;
				
				if('undefined' != typeof FLBuilderTemplateSettings) {
					buttons['save-template'] = FLBuilderStrings.saveCoreTemplate;
				}
			}
			
			// Global settings button 
			buttons['global-settings'] = FLBuilderStrings.editGlobalSettings;
				
			FLBuilder._showActionsLightbox({
				'className' : 'fl-builder-tools-actions',
				'title'     : FLBuilderStrings.actionsLightboxTitle,
				'buttons'   : buttons
			});
		},
		
		/**
		 * Shows the actions lightbox when the done button is clicked.
		 *
		 * @since 1.0
		 * @access private
		 * @method _doneClicked
		 */
		_doneClicked: function()
		{
			FLBuilder._showActionsLightbox({
				'className': 'fl-builder-save-actions',
				'title': FLBuilderStrings.actionsLightboxTitle,
				'buttons': {
					'publish': FLBuilderStrings.publish,
					'draft': FLBuilderStrings.draft,
					'discard': FLBuilderStrings.discard
				}
			});
		},
		
		/**
		 * Opens a new window with the upgrade URL when the 
		 * upgrade button is clicked.
		 *
		 * @since 1.0
		 * @access private
		 * @method _upgradeClicked
		 */
		_upgradeClicked: function()
		{
			window.open(FLBuilderConfig.upgradeUrl);
		},
		
		/**
		 * Shows the actions lightbox when the help button is clicked.
		 *
		 * @since 1.4.9
		 * @access private
		 * @method _helpButtonClicked
		 */
		_helpButtonClicked: function()
		{
			var buttons = {};
			
			if ( FLBuilderConfig.help.tour ) {
				buttons['help-tour'] = FLBuilderStrings.takeHelpTour;
			}
			if ( FLBuilderConfig.help.video ) {
				buttons['help-video'] = FLBuilderStrings.watchHelpVideo;
			}
			if ( FLBuilderConfig.help.knowledge_base ) {
				buttons['knowledge-base'] = FLBuilderStrings.viewKnowledgeBase;
			}
			if ( FLBuilderConfig.help.forums ) {
				buttons['forums'] = FLBuilderStrings.visitForums;
			}
			
			FLBuilder._showActionsLightbox({
				'className': 'fl-builder-help-actions',
				'title': FLBuilderStrings.actionsLightboxTitle,
				'buttons': buttons
			});
		},
		
		/* Panel
		----------------------------------------------------------*/
		
		/**
		 * Closes the builder's content panel.
		 *
		 * @since 1.0
		 * @access private
		 * @method _closePanel
		 */
		_closePanel: function()
		{
			$('.fl-builder-panel').stop(true, true).animate({ right: '-350px' }, 500, function(){ $(this).hide(); });
			$('.fl-builder-bar .fl-builder-add-content-button').stop(true, true).fadeIn();
		},
		
		/**
		 * Opens the builder's content panel.
		 *
		 * @since 1.0
		 * @access private
		 * @method _showPanel
		 */
		_showPanel: function()
		{
			$('.fl-builder-bar .fl-builder-add-content-button').stop(true, true).fadeOut();
			$('.fl-builder-panel').stop(true, true).show().animate({ right: '0' }, 500);
		},
		
		/**
		 * Opens or closes a section in the builder's content panel
		 * when a section title is clicked.
		 *
		 * @since 1.0
		 * @access private
		 * @method _blockSectionTitleClicked
		 */
		_blockSectionTitleClicked: function()
		{
			var title   = $(this),
				section = title.parent();
				
			if(section.hasClass('fl-active')) {
				section.removeClass('fl-active');
			}
			else {
				$('.fl-builder-blocks-section').removeClass('fl-active');
				section.addClass('fl-active');
			}
			
			FLBuilder._initScrollbars();
		},
		
		/* Save Actions
		----------------------------------------------------------*/
		
		/**
		 * Publishes the layout when the publish button is clicked.
		 *
		 * @since 1.0
		 * @access private
		 * @method _publishButtonClicked
		 */
		_publishButtonClicked: function()
		{
			FLBuilder.showAjaxLoader();
			
			FLBuilder.ajax({
				action: 'fl_builder_save',
				method: 'save_layout'
			}, FLBuilder._exit);
				
			FLBuilder._actionsLightbox.close();
		},
		
		/**
		 * Exits the builder when the save draft button is clicked.
		 *
		 * @since 1.0
		 * @access private
		 * @method _draftButtonClicked
		 */
		_draftButtonClicked: function()
		{
			FLBuilder.showAjaxLoader();
			
			FLBuilder.ajax({
				action: 'fl_builder_save',
				method: 'save_draft',
				render_assets: 0
			}, FLBuilder._exit);
			
			FLBuilder._actionsLightbox.close();
		},
		
		/**
		 * Clears changes to the layout when the discard draft button
		 * is clicked.
		 *
		 * @since 1.0
		 * @access private
		 * @method _discardButtonClicked
		 */
		_discardButtonClicked: function()
		{
			var result = confirm(FLBuilderStrings.discardMessage);
			
			if(result) {
			
				FLBuilder.showAjaxLoader();
				
				FLBuilder.ajax({
					action: 'fl_builder_save',
					method: 'clear_draft_layout'
				}, FLBuilder._exit);
					
				FLBuilder._actionsLightbox.close();
			}
		},
		
		/**
		 * Closes the actions lightbox when the cancel button is clicked.
		 *
		 * @since 1.0
		 * @access private
		 * @method _cancelButtonClicked
		 */
		_cancelButtonClicked: function()
		{
			FLBuilder._exitUrl = null;
			FLBuilder._actionsLightbox.close();
		},
		
		/**
		 * Redirects the user to the _exitUrl if defined, otherwise 
		 * it redirects the user to the current post without the 
		 * builder active. 
		 *
		 * @since 1.0
		 * @since 1.5.7 Closes the window if we're in a child window.
		 * @access private
		 * @method _exit
		 */
		_exit: function()
		{
			var href = window.location.href;
			
			if ( FLBuilderConfig.isUserTemplate && typeof window.opener != 'undefined' && window.opener ) {
				
				if ( typeof window.opener.FLBuilder != 'undefined' ) {
					window.opener.FLBuilder._updateLayout();
				}
				
				window.close();
			}
			else {
				
				if ( FLBuilder._exitUrl ) {
					href = FLBuilder._exitUrl;
				}
				else {
					href = href.replace('?fl_builder', '').replace('&fl_builder', '');
				}
				
				window.location.href = href;
			}
		},
		
		/* Tools Actions
		----------------------------------------------------------*/
		
		/**
		 * Duplicates the current post and builder layout.
		 *
		 * @since 1.0
		 * @access private
		 * @method _duplicatePageClicked
		 */
		_duplicatePageClicked: function()
		{
			FLBuilder._actionsLightbox.close();
			FLBuilder.showAjaxLoader();
			
			FLBuilder.ajax({
				action: 'fl_builder_save',
				method: 'duplicate_post'
			}, FLBuilder._duplicatePageComplete);
		},
		
		/**
		 * Redirects the user to the post edit screen of a
		 * duplicated post when duplication is complete.
		 *
		 * @since 1.0
		 * @access private
		 * @method _duplicatePageComplete
		 * @param {Number} The ID of the duplicated post.
		 */
		_duplicatePageComplete: function(response)
		{
			var adminUrl = $('#fl-admin-url').val();
			
			window.location.href = adminUrl + 'post.php?post='+ response +'&action=edit';
		},
		
		/**
		 * Shows the global builder settings lightbox when the global
		 * settings button is clicked.
		 *
		 * @since 1.0
		 * @access private
		 * @method _globalSettingsClicked
		 */       
		_globalSettingsClicked: function()
		{
			FLBuilder._actionsLightbox.close();
			FLBuilder._showLightbox(false);
			
			FLBuilder.ajax({
				action: 'fl_builder_render_global_settings'
			}, FLBuilder._globalSettingsLoaded);
		},

		/**
		 * Sets the lightbox content when the global settings have loaded.
		 *
		 * @since 1.0
		 * @access private
		 * @method _globalSettingsLoaded
		 * @param {String} html The HTML for the global settings form.
		 */  
		_globalSettingsLoaded: function(html)
		{
			FLBuilder._setSettingsFormContent(html);  
					  
			FLBuilder._initSettingsValidation({
				module_margins: {
					required: true,
					number: true
				},
				row_margins: {
					required: true,
					number: true
				},
				row_padding: {
					required: true,
					number: true
				},
				row_width: {
					required: true,
					number: true
				},
				responsive_breakpoint: {
					required: true,
					number: true
				}
			});
		},

		/**
		 * Saves the global settings when the save button is clicked.
		 *
		 * @since 1.0
		 * @access private
		 * @method _saveGlobalSettingsClicked
		 */       
		_saveGlobalSettingsClicked: function()
		{
			var form     = $(this).closest('.fl-builder-settings'),
				valid    = form.validate().form(),
				data     = form.serializeArray(),
				settings = {},
				i        = 0;
				
			if(valid) {
					 
				for( ; i < data.length; i++) {
					settings[data[i].name] = data[i].value;
				}
				
				FLBuilder.showAjaxLoader();
				
				FLBuilder.ajax({
					action: 'fl_builder_save',
					method: 'save_global_settings',
					settings: settings
				}, FLBuilder._updateLayout);
					
				FLBuilder._lightbox.close();
			}
		},
		
		/* Template Selector
		----------------------------------------------------------*/
		
		/**
		 * Shows the template selector when the builder is launched
		 * if the current layout is empty.
		 *
		 * @since 1.0
		 * @access private
		 * @method _initTemplateSelector
		 */
		_initTemplateSelector: function()
		{
			var rows = $(FLBuilder._contentClass).find('.fl-row');
			
			if(rows.length === 0) {
				FLBuilder._showTemplateSelector();
			}
		},
		
		/**
		 * Shows the template selector when the templates button
		 * has been clicked.
		 *
		 * @since 1.0
		 * @access private
		 * @method _changeTemplateClicked
		 */
		_changeTemplateClicked: function()
		{
			FLBuilder._actionsLightbox.close();
			FLBuilder._showTemplateSelector();
		},
		
		/**
		 * Shows the template selector lightbox.
		 *
		 * @since 1.0
		 * @access private
		 * @method _showTemplateSelector
		 */
		_showTemplateSelector: function()
		{
			if ( FLBuilderConfig.simpleUi ) {
				return;
			}
			if ( FLBuilderConfig.isUserTemplate ) {
				return;   
			}
			if ( 'disabled' == FLBuilderConfig.enabledTemplates ) {
				return;
			}
			if ( FLBuilderConfig.lite ) {
				return;    
			}
			
			FLBuilder._showLightbox( false );
			
			FLBuilder.ajax({
				action: 'fl_builder_render_template_selector'
			}, FLBuilder._templateSelectorLoaded );
		},
		
		/**
		 * Sets the lightbox content when the template selector has loaded.
		 *
		 * @since 1.0
		 * @access private
		 * @method _templateSelectorLoaded
		 * @param {String} html The HTML for the template selector.
		 */
		_templateSelectorLoaded: function( html )
		{
			FLBuilder._setLightboxContent( html );
			
			var select 			= $( '.fl-template-category-select' ),
				userTemplates 	= $( '.fl-user-template' );
			
			// Default to the user templates tab?
			if ( 'user' == FLBuilderConfig.enabledTemplates || userTemplates.length > 0 ) {
				select.val( 'fl-builder-settings-tab-yours' );
				$( '.fl-builder-settings-tab' ).removeClass( 'fl-active' );
				$( '#fl-builder-settings-tab-yours' ).addClass( 'fl-active' );
			}
			
			// Show the no templates message?
			if ( 0 === userTemplates.length ) {
				$( '.fl-user-templates-message' ).show();
			}
			
			$( 'body' ).trigger( 'fl-builder.template-selector-loaded' );
		},
		
		/**
		 * Callback to show a template category when the 
		 * select is changed.
		 *
		 * @since 1.5.7
		 * @access private
		 * @method _templateCategoryChanged
		 */
		_templateCategoryChanged: function()
		{
			$( '.fl-template-selector .fl-builder-settings-tab' ).hide();
			$( '#' + $( this ).val() ).show();
		},
		
		/**
		 * Callback for when a user clicks a core template in 
		 * the template selector.
		 *
		 * @since 1.0
		 * @access private
		 * @method _templateClicked
		 */
		_templateClicked: function()
		{
			var template = $(this),
				index    = template.closest('.fl-template-preview').attr('data-id');
			
			if($(FLBuilder._contentClass).children('.fl-row').length > 0) {
				
				if(index == 0) {
					if(confirm(FLBuilderStrings.changeTemplateMessage)) {
						FLBuilder._lightbox._node.hide();
						FLBuilder._applyTemplate(0, false, 'core');
					}
				}
				else {
					FLBuilder._selectedTemplateId = index;
					FLBuilder._selectedTemplateType = 'core';
					FLBuilder._showTemplateActions();
					FLBuilder._lightbox._node.hide();
				}                
			}
			else {
				FLBuilder._applyTemplate(index, false, 'core');
			}
		},
		
		/**
		 * Shows the actions lightbox for replacing and appending templates.
		 *
		 * @since 1.1.9
		 * @access private
		 * @method _showTemplateActions
		 */
		_showTemplateActions: function()
		{
			FLBuilder._showActionsLightbox({
				'className': 'fl-builder-template-actions',
				'title': FLBuilderStrings.actionsLightboxTitle,
				'buttons': {
					'template-replace': FLBuilderStrings.templateReplace,
					'template-append': FLBuilderStrings.templateAppend
				}
			});
		},
		
		/**
		 * Replaces the current layout with a template when the replace
		 * button is clicked.
		 *
		 * @since 1.1.9
		 * @access private
		 * @method _templateReplaceClicked
		 */
		_templateReplaceClicked: function()
		{
			if(confirm(FLBuilderStrings.changeTemplateMessage)) {
				FLBuilder._actionsLightbox.close();
				FLBuilder._applyTemplate(FLBuilder._selectedTemplateId, false, FLBuilder._selectedTemplateType);
			}
		},
		
		/**
		 * Append a template to the current layout when the append
		 * button is clicked.
		 *
		 * @since 1.1.9
		 * @access private
		 * @method _templateAppendClicked
		 */
		_templateAppendClicked: function()
		{
			FLBuilder._actionsLightbox.close();
			FLBuilder._applyTemplate(FLBuilder._selectedTemplateId, true, FLBuilder._selectedTemplateType);
		},
		
		/**
		 * Shows the template selector when the cancel button of
		 * the template actions lightbox is clicked.
		 *
		 * @since 1.1.9
		 * @access private
		 * @method _templateCancelClicked
		 */
		_templateCancelClicked: function()
		{
			FLBuilder._lightbox._node.show();
		},
		
		/**
		 * Applys a template to the current layout by either appending
		 * it or replacing the current layout with it.
		 *
		 * @since 1.1.9
		 * @access private
		 * @method _applyTemplate
		 * @param {Number} id The template id.
		 * @param {Boolean} append Whether the new template should be appended or not.
		 * @param {String} type The type of template. Either core or user.
		 */
		_applyTemplate: function(id, append, type)
		{
			append  = typeof append === 'undefined' || !append ? '0' : '1';
			type    = typeof type === 'undefined' ? 'core' : type;
			
			FLBuilder._lightbox.close();
			FLBuilder.showAjaxLoader();
		
			if(type == 'core') {
		
				FLBuilder.ajax({
					action: 'fl_builder_save',
					method: 'apply_template',
					template_id: id,
					append: append
				}, FLBuilder._updateLayout);
			}
			else {
			
				FLBuilder.ajax({
					action: 'fl_builder_save',
					method: 'apply_user_template',
					template_id: id,
					append: append
				}, FLBuilder._updateLayout);
			}
		},
		
		/* User Template Settings
		----------------------------------------------------------*/
		
		/**
		 * Shows the settings for saving a user defined template 
		 * when the save template button is clicked.
		 *
		 * @since 1.1.3
		 * @access private
		 * @method _saveUserTemplateClicked
		 */
		_saveUserTemplateClicked: function()
		{
			FLBuilder._actionsLightbox.close();
			FLBuilder._showLightbox(false);
			
			FLBuilder.ajax({
				action: 'fl_builder_render_user_template_settings'
			}, FLBuilder._userTemplateSettingsLoaded);
		},
		
		/**
		 * Sets the lightbox content when the user template settings are loaded.
		 *
		 * @since 1.1.9
		 * @access private
		 * @method _userTemplateSettingsLoaded
		 * @param {String} html The HTML for the user template settings form.
		 */  
		_userTemplateSettingsLoaded: function(html)
		{
			FLBuilder._setSettingsFormContent(html);  
					  
			FLBuilder._initSettingsValidation({
				name: {
					required: true
				}
			});
		},
		
		/**
		 * Saves user template settings when the save button is clicked.
		 *
		 * @since 1.1.9
		 * @access private
		 * @method _saveUserTemplateSettings
		 */
		_saveUserTemplateSettings: function()
		{
			var form     = $(this).closest('.fl-builder-settings'),
				valid    = form.validate().form(),
				settings = FLBuilder._getSettings(form);
				
			if(valid) {
					 
				FLBuilder.showAjaxLoader();
				
				FLBuilder.ajax({
					action: 'fl_builder_save',
					method: 'save_user_template',
					settings: settings
				}, FLBuilder._saveUserTemplateSettingsComplete);
					
				FLBuilder._lightbox.close();
			}
		},
		
		/**
		 * Shows a success alert when user template settings have saved.
		 *
		 * @since 1.1.9
		 * @access private
		 * @method _saveUserTemplateSettingsComplete
		 */
		_saveUserTemplateSettingsComplete: function()
		{
			FLBuilder.alert(FLBuilderStrings.templateSaved);
		},
		
		/**
		 * Callback for when a user clicks a user defined template in 
		 * the template selector.
		 *
		 * @since 1.1.3
		 * @access private
		 * @method _userTemplateClicked
		 */
		_userTemplateClicked: function()
		{
			var id = $(this).attr('data-id');
				
			if($(FLBuilder._contentClass).children('.fl-row').length > 0) {
			
				if(id == 'blank') {
					if(confirm(FLBuilderStrings.changeTemplateMessage)) {
						FLBuilder._lightbox._node.hide();
						FLBuilder._applyTemplate('blank', false, 'user');
					}
				}
				else {            
					FLBuilder._selectedTemplateId = id;
					FLBuilder._selectedTemplateType = 'user';
					FLBuilder._showTemplateActions();
					FLBuilder._lightbox._node.hide();
				}
			}
			else {
				FLBuilder._applyTemplate(id, false, 'user');
			}
		},
		
		/**
		 * Launches the builder in a new tab to edit a user
		 * defined template when the edit link is clicked.
		 *
		 * @since 1.1.3
		 * @access private
		 * @method _editUserTemplateClicked
		 * @param {Object} e The event object.
		 */
		_editUserTemplateClicked: function(e)
		{
			e.preventDefault();
			e.stopPropagation();
			
			window.open($(this).attr('href'));
		},
		
		/**
		 * Deletes a user defined template when the delete link is clicked.
		 *
		 * @since 1.1.3
		 * @access private
		 * @method _deleteUserTemplateClicked
		 * @param {Object} e The event object.
		 */
		_deleteUserTemplateClicked: function(e)
		{
			var template = $( this ).closest( '.fl-user-template' ),
				id		 = template.attr( 'data-id' ),
				all		 = $( '.fl-user-template[data-id=' + id + ']' ),
				parent   = null;
			
			if ( confirm( FLBuilderStrings.deleteTemplate ) ) {
				
				FLBuilder.ajax( {
					action: 'fl_builder_save',
					method: 'delete_user_template',
					template_id: id
				} );
			
				all.fadeOut( function() {
					
					template = $( this );
					parent 	 = template.closest( '.fl-user-template-category' );
					
					template.remove(); 
					
					if ( 0 === parent.find( '.fl-user-template' ).length ) {
						parent.remove();
					}
					if ( 1 === $( '.fl-user-template' ).length ) {
						$( '.fl-user-templates').hide();
						$( '.fl-user-templates-message').show();
					}
				});
			}
			
			e.stopPropagation();
		},
		
		/* Core Template Settings
		----------------------------------------------------------*/
		
		/**
		 * Initializes the settings for saving core templates.
		 *
		 * @since 1.0
		 * @access private
		 * @method _initCoreTemplateSettings
		 */
		_initCoreTemplateSettings: function()
		{
			if('undefined' != typeof FLBuilderTemplateSettings) {
				FLBuilderTemplateSettings.init();
			}
		},
		
		/* Help Actions
		----------------------------------------------------------*/
		
		/**
		 * Shows the getting started video when the watch video button
		 * is clicked.
		 *
		 * @since 1.4.9
		 * @access private
		 * @method _initCoreTemplateSettings
		 */
		_watchVideoClicked: function()
		{
			var template = wp.template( 'fl-video-lightbox' );

			FLBuilder._actionsLightbox.close();
			FLBuilder._lightbox.open( template( { video : FLBuilderConfig.help.video_embed } ) );
		},
		
		/**
		 * Opens a new window with the knowledge base URL when the 
		 * view knowledge base button is clicked.
		 *
		 * @since 1.4.9
		 * @access private
		 * @method _viewKnowledgeBaseClicked
		 */
		_viewKnowledgeBaseClicked: function()
		{
			FLBuilder._actionsLightbox.close();
			
			window.open( FLBuilderConfig.help.knowledge_base_url );
		},
		
		/**
		 * Opens a new window with the forums URL when the 
		 * visit forums button is clicked.
		 *
		 * @since 1.4.9
		 * @access private
		 * @method _visitForumsClicked
		 */
		_visitForumsClicked: function()
		{
			FLBuilder._actionsLightbox.close();
			
			window.open( FLBuilderConfig.help.forums_url );
		},
		
		/* Help Tour
		----------------------------------------------------------*/
		
		/**
		 * Shows the help tour or template selector when the builder
		 * is launched.
		 *
		 * @since 1.4.9
		 * @access private
		 * @method _showTourOrTemplates
		 */
		_showTourOrTemplates: function()
		{
			if ( ! FLBuilderConfig.simpleUi && ! FLBuilderConfig.isUserTemplate ) {
				if ( FLBuilderConfig.help.tour && FLBuilderConfig.newUser ) {
					FLBuilder._showTourLightbox();
				}
				else {
					FLBuilder._initTemplateSelector();
				}
			}
		},
		
		/**
		 * Shows the actions lightbox with a welcome message for new 
		 * users asking if they would like to take the tour.
		 *
		 * @since 1.4.9
		 * @access private
		 * @method _showTourLightbox
		 */
		_showTourLightbox: function()
		{
			var template = wp.template( 'fl-tour-lightbox' );

			FLBuilder._actionsLightbox.open( template() );
		},
		
		/**
		 * Closes the actions lightbox and shows the template selector 
		 * if a new user declines the tour.
		 *
		 * @since 1.4.9
		 * @access private
		 * @method _noTourButtonClicked
		 */
		_noTourButtonClicked: function()
		{
			FLBuilder._actionsLightbox.close();
			FLBuilder._initTemplateSelector();
		},
		
		/**
		 * Closes the actions lightbox and starts the tour when a new user
		 * decides to take the tour.
		 *
		 * @since 1.4.9
		 * @access private
		 * @method _yesTourButtonClicked
		 */
		_yesTourButtonClicked: function()
		{
			FLBuilder._actionsLightbox.close();
			FLBuilderTour.start();
		},
		
		/**
		 * Starts the help tour.
		 *
		 * @since 1.4.9
		 * @access private
		 * @method _startHelpTour
		 */
		_startHelpTour: function()
		{
			FLBuilder._actionsLightbox.close();
			FLBuilderTour.start();
		},
		
		/* Layout
		----------------------------------------------------------*/
		
		/**
		 * Shows a message to drop a row or module to get started 
		 * if the layout is empty.
		 *
		 * @since 1.0
		 * @access private
		 * @method _setupEmptyLayout
		 */
		_setupEmptyLayout: function()
		{
			var content = $(FLBuilder._contentClass);
			
			if ( FLBuilderConfig.isUserTemplate && 'module' == FLBuilderConfig.userTemplateType ) {
				return;
			}
			else {
				content.removeClass('fl-builder-empty');
				content.find('.fl-builder-empty-message').remove();
				
				if(content.children('.fl-row').length === 0) {
					content.addClass('fl-builder-empty');
					content.append('<span class="fl-builder-empty-message">'+ FLBuilderStrings.emptyMessage +'</span>');
					FLBuilder._initSortables();
				}
			}
		},
		
		/**
		 * Sends an AJAX request to render the layout and is typically
		 * used as a callback to many of the builder's save operations.
		 *
		 * @since 1.0
		 * @access private
		 * @method _updateLayout
		 */
		_updateLayout: function()
		{
			FLBuilder.showAjaxLoader();
			
			FLBuilder.ajax({
				action: 'fl_builder_render_layout'
			}, FLBuilder._renderLayout);
		},
		
		/**
		 * Removes the current layout and renders a new layout using
		 * the provided data.
		 *
		 * @since 1.0
		 * @access private
		 * @method _renderLayout
		 * @param {Object} data The layout data. May also be a JSON encoded string.
		 * @param {String} data.html The HTML for the new layout.
		 * @param {String} data.css The URL for the layout CSS.
		 * @param {String} data.js The URL for the layout JavaScript.
		 * @param {Function} callback A function to call when the layout has finished rendering.
		 */
		_renderLayout: function(data, callback)
		{
			data = typeof data == 'string' ? JSON.parse(data) : data;
		
			var post    = $('#fl-post-id').val(),
				head    = $('head').eq(0),
				body    = $('body').eq(0),
				content = $(FLBuilder._contentClass),
				loader  = $('<img src="' + data.css + '" />'),
				oldCss  = $('link[href*="/cache/' + post + '"]'),
				oldJs   = $('script[src*="/cache/' + post + '"]'),
				newCss  = $('<link rel="stylesheet" id="fl-builder-layout-' + post + '-css"  href="'+ data.css +'" />'),
				newJs   = $('<script src="'+ data.js +'"></script>');
				
			// Image onerror hack to check if the stylesheet has been loaded.
			loader.on('error', function() 
			{
				// Remove the loader.
				loader.remove();
				
				// Add the new layout css.
				if(oldCss.length > 0) {
					oldCss.after(newCss);
				}
				else {
					head.append(newCss);    
				}
				
				// Set a quick timeout to ensure the css has taken effect.
				setTimeout(function()
				{
					// Set the body height so the page doesn't scroll.
					body.height(body.height());
					
					// Remove the old content and assets.
					content.empty();
					oldCss.remove();
					oldJs.remove();
					
					// Add the new content.
					content.append( FLBuilder._renderLayoutCleanContent( data.html ) );
					
					// Add the new layout js.
					setTimeout(function(){
						head.append(newJs);
					}, 50);

					// Send the layout rendered event.
					$( FLBuilder._contentClass ).trigger( 'fl-builder.layout-rendered' );
					
					// Remove action overlays so they can reset.
					FLBuilder._removeAllOverlays();
					
					// Hide the loader.
					FLBuilder.hideAjaxLoader();
					
					// Run the callback.
					if(typeof callback != 'undefined') {
						callback();
					}
				
				}, 250);
			});
			
			body.append(loader);
		},
		
		/**
		 * Removes scripts that are already on the page from
		 * new HTML content that is going to be rendered.
		 *
		 * @since 1.0
		 * @access private
		 * @method _renderLayoutCleanContent
		 * @param {String} html The new HTML content to clean.
		 * @return {String} The cleaned HTML content.
		 */
		_renderLayoutCleanContent: function( html )
		{
			var cleaned = $( '<div id="fl-cleaned-content">' + html + '</div>' ),
				src     = '',
				script  = null;
			
			cleaned.find( 'script' ).each( function() {
				
				src     = $( this ).attr( 'src' );
				script  = $( 'script[src="' + src + '"]' );
				
				if ( script.length > 0 ) {
					$( this ).remove();
				}
			});
			
			return cleaned.html();
		},
		
		/**
		 * Called by the layout's JavaScript file once it's loaded 
		 * to finish rendering the layout.
		 *
		 * @since 1.0
		 * @access private
		 * @method _renderLayoutComplete
		 */
		_renderLayoutComplete: function()
		{
			FLBuilder._setupEmptyLayout();
			FLBuilder._highlightEmptyCols();
			FLBuilder._initSortables();
			FLBuilder._resizeLayout();
			FLBuilder._initMediaElements();
			FLBuilderLayout.init();
			
			// Reset the body height.
			$('body').height('auto');
		},
		
		/**
		 * Trigger the resize event on the window so elements
		 * in the layout that rely on JavaScript know to resize.
		 *
		 * @since 1.0
		 * @access private
		 * @method _resizeLayout
		 */
		_resizeLayout: function()
		{
			$(window).trigger('resize'); 
				
			if(typeof YUI !== 'undefined') {
				YUI().use('node-event-simulate', function(Y) {
					Y.one(window).simulate("resize");
				});
			}
		},

		/**
		 * Initializes MediaElements.js audio and video players.
		 *
		 * @since 1.0
		 * @access private
		 * @method _initMediaElements
		 */
		_initMediaElements: function()
		{
			var settings = {};
			
			if(typeof $.fn.mediaelementplayer != 'undefined') {
			
				if(typeof _wpmejsSettings !== 'undefined') {
					settings.pluginPath = _wpmejsSettings.pluginPath;
				}
				
				$('.wp-audio-shortcode, .wp-video-shortcode').mediaelementplayer(settings);                
			}
		},
		
		/* Generic Drag and Drop
		----------------------------------------------------------*/
		
		/**
		 * Returns a helper element for a drag operation.
		 *
		 * @since 1.0
		 * @access private
		 * @method _blockDragHelper
		 * @param {Object} e The event object.
		 * @param {Object} item The item being dragged.
		 * @return {Object} The helper element.
		 */
		_blockDragHelper: function (e, item) 
		{
			var helper = item.clone();
			
			item.clone().insertAfter(item);
			helper.addClass('fl-builder-block-drag-helper');
			
			return helper;
		},
		
		/**
		 * Initializes a drag operation.
		 *
		 * @since 1.0
		 * @access private
		 * @method _blockDragInit
		 * @param {Object} e The event object.
		 */
		_blockDragInit: function(e)
		{
			var target      = $(e.target),
				item        = null,
				initialPos  = 0,
				noHighlight = 'row' == FLBuilderConfig.userTemplateType ? '' : ':not(.fl-node-global)';
				
			// Set the _dragEnabled flag.
			FLBuilder._dragEnabled = true;
			
			// Set the item to a module instance.  
			if(target.closest('.fl-module').length > 0) {
				item = target.closest('.fl-module');
			}
			// Set the item to a row instance.
			else if(target.closest('.fl-row').length > 0) {
				item = target.closest('.fl-row');
			}
			// Set the item to the first visible row.
			else if(target.hasClass('fl-builder-block-row') || target.hasClass('fl-builder-block-saved-row')) {
				$('.fl-row').each(function(){
					if(item === null && $(this).offset().top - $(window).scrollTop() > 0) {
						item = $(this);
					}
				});
			}
			// Set the item to the first visible module.
			else if(target.hasClass('fl-builder-block-module') || target.hasClass('fl-builder-block-saved-module')) {
				
				$('.fl-module').each(function(){
					if(item === null && $(this).offset().top - $(window).scrollTop() > 0) {
						item = $(this);
					}
				});
			}
			
			// Get the initial scroll position of the item.
			if(item !== null) {
				initialPos = item.offset().top - $(window).scrollTop();
			}
			else {
				item = target;
			}
			
			// Hide the empty message.
			$('.fl-builder-empty-message').hide();
			
			// Highlight rows.
			$(FLBuilder._contentClass + ' .fl-row' + noHighlight).addClass('fl-row-highlight');
			
			// Highlight modules.
			if(item.hasClass('fl-module') || item.hasClass('fl-builder-block-module') || item.hasClass('fl-builder-block-saved-module')) {
				$(FLBuilder._contentClass + ' .fl-col' + noHighlight).addClass('fl-col-highlight');
			}
			
			// Setup the UI for dragging.
			FLBuilder._disableGlobalRows();
			FLBuilder._closePanel();
			FLBuilder._destroyOverlayEvents();
			FLBuilder._removeAllOverlays();
			
			// Scroll to the row or module that was dragged.            
			if(initialPos > 0) {
				scrollTo(0, item.offset().top - initialPos);
			}
		},
		
		/**
		 * Callback that fires when dragging starts.
		 *
		 * @since 1.0
		 * @access private
		 * @method _blockDragStart
		 * @param {Object} e The event object.
		 * @param {Object} ui An object with additional info for the drag.
		 */
		_blockDragStart: function(e, ui)
		{
			FLBuilder._dragging = true;
			
			// Refresh sortables.
			$(FLBuilder._contentClass).sortable('refreshPositions');
			$(FLBuilder._contentClass + ' .fl-row-content').sortable('refreshPositions');
			$(FLBuilder._contentClass + ' .fl-col-content').sortable('refreshPositions');
		},
		
		/**
		 * Callback that fires when an element that is being
		 * dragged is sorted.
		 *
		 * @since 1.0
		 * @access private
		 * @method _blockDragSort
		 * @param {Object} e The event object.
		 * @param {Object} ui An object with additional info for the drag.
		 */
		_blockDragSort: function(e, ui)
		{
			if(typeof ui.placeholder === 'undefined') {
				return;
			}
			
			var parent = ui.placeholder.parent(),
				title  = FLBuilderStrings.insert;
			
			// Find the placeholder title.
			if(parent.hasClass('fl-col-content')) {
				if(ui.item.hasClass('fl-builder-block-module')) {
					title = ui.item.find('.fl-builder-block-title').text();
				}
				else if(ui.item.hasClass('fl-builder-block-saved-module') || ui.item.hasClass('fl-builder-block-module-template')) {
					title = ui.item.find('.fl-builder-block-title').text();
				}
				else {
					title = ui.item.attr('data-name');
				}
			}
			else if(parent.hasClass('fl-row-content')) {
				if(ui.item.hasClass('fl-builder-block-row')) {
					title = ui.item.find('.fl-builder-block-title').text();
				}
				else {
					title = FLBuilderStrings.newColumn;
				}
			}
			else if(parent.hasClass('fl-builder-content')) {
				if(ui.item.hasClass('fl-builder-block-row')) {
					title = ui.item.find('.fl-builder-block-title').text();
				}
				else if(ui.item.hasClass('fl-builder-block-saved-row')) {
					title = ui.item.find('.fl-builder-block-title').text();
				}
				else if(ui.item.hasClass('fl-row')) {
					title = FLBuilderStrings.row;
				}
				else {
					title = FLBuilderStrings.newRow;
				}
			}
			
			// Set the placeholder title.
			ui.placeholder.html(title);
			
			// Add the global class?
			if ( ui.item.hasClass( 'fl-node-global' ) || ui.item.hasClass( 'fl-builder-block-global' ) ) {
				ui.placeholder.addClass( 'fl-builder-drop-zone-global' );
			}
			else {
				ui.placeholder.removeClass( 'fl-builder-drop-zone-global' );
			}
		},
		
		/**
		 * Cleans up when a drag operation has stopped.
		 *
		 * @since 1.0
		 * @access private
		 * @method _blockDragStop
		 * @param {Object} e The event object.
		 * @param {Object} ui An object with additional info for the drag.
		 */
		_blockDragStop: function(e, ui)
		{
			var parent     = ui.item.parent(),
				initialPos = parent.offset().top - $(window).scrollTop();

			// Show the panel? 
			if(parent.hasClass('fl-builder-blocks-section-content')) {
				FLBuilder._showPanel();
			}
			
			// Finish dragging. 
			FLBuilder._dragEnabled = false;
			FLBuilder._dragging = false;
			FLBuilder._bindOverlayEvents();
			FLBuilder._highlightEmptyCols();
			FLBuilder._enableGlobalRows();
			$('.fl-builder-empty-message').show();
			
			// Scroll the page back to where it was. 
			scrollTo(0, parent.offset().top - initialPos);
		},
		
		/**
		 * Cleans up when a drag operation has canceled.
		 *
		 * @since 1.0
		 * @access private
		 * @method _blockDragCancel
		 */
		_blockDragCancel: function()
		{
			if(FLBuilder._dragEnabled && !FLBuilder._dragging) {
				FLBuilder._dragEnabled = false;
				FLBuilder._dragging = false;
				FLBuilder._bindOverlayEvents();
				FLBuilder._highlightEmptyCols();
				FLBuilder._enableGlobalRows();
				$('.fl-builder-empty-message').show();
			}
		},
		
		/**
		 * Removes all node overlays and hides any tooltip helpies.
		 *
		 * @since 1.0
		 * @access private
		 * @method _removeAllOverlays
		 */
		_removeAllOverlays: function()
		{
			FLBuilder._removeRowOverlays();
			FLBuilder._removeColOverlays();
			FLBuilder._removeModuleOverlays();
			FLBuilder._hideTipTips();
		},
		
		/**
		 * Appends a node action overlay to the layout.
		 *
		 * @since 1.6.3.3
		 * @access private
		 * @method _appendOverlay
		 * @param {Object} node A jQuery reference to the node this overlay is associated with.
		 * @param {Object} template A rendered wp.template. 
		 */
		_appendOverlay: function( node, template )
		{
			var overlayPos 	= 0,
				overlay 	= null,
				isRow		= node.hasClass( 'fl-row' ),
				content		= isRow ? node.find( '> .fl-row-content-wrap' ) : node.find( '> .fl-node-content' ),
				margins 	= {
					'top' 		: parseInt( content.css( 'margin-top' ), 10 ),
					'bottom' 	: parseInt( content.css( 'margin-bottom' ), 10 )
				};
				
			// Append the template.
			node.append( template );
			
			// Add the active class to the node.
			node.addClass( 'fl-block-overlay-active' );
			
			// Init TipTips
			FLBuilder._initTipTips();
			
			// Get a reference to the overlay.
			overlay = node.find( '> .fl-block-overlay' );
			
			// Adjust the overlay positions to account for negative margins.
			if ( margins.top < 0 ) {
				overlayPos = parseInt( overlay.css( 'top' ), 10 );
				overlayPos = isNaN( overlayPos ) ? 0 : overlayPos;
				overlay.css( 'top', ( margins.top + overlayPos ) + 'px' );
			}
			if ( margins.bottom < 0 ) {
				overlayPos = parseInt( overlay.css( 'bottom' ), 10 );
				overlayPos = isNaN( overlayPos ) ? 0 : overlayPos;
				overlay.css( 'bottom', ( margins.bottom + overlayPos ) + 'px' );
			}
			
			// Put row action headers on the bottom if they're hidden.
			if ( isRow && overlay.offset().top < 43 ) {
				overlay.addClass( 'fl-row-overlay-header-bottom' );
			}
		},
		
		/* Rows
		----------------------------------------------------------*/
		
		/**
		 * Adds a dashed border to empty columns.
		 *
		 * @since 1.0
		 * @access private
		 * @method _highlightEmptyCols
		 */
		_highlightEmptyCols: function()
		{
			var noHighlight = 'row' == FLBuilderConfig.userTemplateType ? '' : ':not(.fl-node-global)';
				rows 		= $(FLBuilder._contentClass + ' .fl-row' + noHighlight),
				cols 		= $(FLBuilder._contentClass + ' .fl-col' + noHighlight);
			
			rows.removeClass('fl-row-highlight');
			cols.removeClass('fl-col-highlight');
			
			cols.each(function(){
				
				var col = $(this);
				
				if(col.find('.fl-module').length === 0) {
					col.addClass('fl-col-highlight');
				}
			});
		},
		
		/**
		 * Removes all row overlays from the page.
		 *
		 * @since 1.0
		 * @access private
		 * @method _removeRowOverlays
		 */
		_removeRowOverlays: function()
		{
			$('.fl-row').removeClass('fl-block-overlay-active');
			$('.fl-row-overlay').remove();
		},
		
		/**
		 * Removes all row overlays from the page.
		 *
		 * @since 1.0
		 * @access private
		 * @method _removeRowOverlays
		 */
		_disableGlobalRows: function()
		{
			if ( 'row' == FLBuilderConfig.userTemplateType ) {
				return;
			}
			
			var rows = $('.fl-row.fl-node-global');
			
			rows.addClass( 'fl-node-disabled' );
			rows.append( '<div class="fl-node-disabled-overlay"></div>' );
		},
		
		/**
		 * Removes all row overlays from the page.
		 *
		 * @since 1.0
		 * @access private
		 * @method _removeRowOverlays
		 */
		_enableGlobalRows: function()
		{
			if ( 'row' == FLBuilderConfig.userTemplateType ) {
				return;
			}
			
			$( '.fl-node-disabled' ).removeClass( 'fl-node-disabled' );
			$( '.fl-node-disabled-overlay' ).remove();
		},
		
		/**
		 * Shows an overlay with actions when the mouse enters a row.
		 *
		 * @since 1.0
		 * @access private
		 * @method _rowMouseenter
		 */
		_rowMouseenter: function()
		{
			var row      = $( this ),
				template = wp.template( 'fl-row-overlay' );

			if ( ! row.hasClass( 'fl-block-overlay-active' ) ) {
				FLBuilder._appendOverlay( row, template( { 
					global : row.hasClass( 'fl-node-global' ), 
					node   : row.attr('data-node')
				} ) );
			}
		},
		
		/**
		 * Removes overlays when the mouse leaves a row.
		 *
		 * @since 1.0
		 * @access private
		 * @method _rowMouseleave
		 * @param {Object} e The event object.
		 */
		_rowMouseleave: function(e)
		{
			var toElement       = $(e.toElement) || $(e.relatedTarget),
				isOverlay       = toElement.hasClass('fl-row-overlay'),
				isOverlayChild  = toElement.closest('.fl-row-overlay').length > 0,
				isTipTip        = toElement.is('#tiptip_holder'),
				isTipTipChild   = toElement.closest('#tiptip_holder').length > 0;
			
			if(isOverlay || isOverlayChild || isTipTip || isTipTipChild) {
				return;
			}
			
			FLBuilder._removeRowOverlays();
		},
		
		/**
		 * Returns a helper element for row drag operations.
		 *
		 * @since 1.0
		 * @access private
		 * @method _rowDragHelper
		 * @return {Object} The helper element.
		 */
		_rowDragHelper: function()
		{
			return $('<div class="fl-builder-block-drag-helper" style="width: 190px; height: 45px;">' + FLBuilderStrings.row + '</div>');
		},
		
		/**
		 * Callback for when a row drag operation completes.
		 *
		 * @since 1.0
		 * @access private
		 * @method _rowDragStop
		 * @param {Object} e The event object.
		 * @param {Object} ui An object with additional info for the drag.
		 */
		_rowDragStop: function(e, ui)
		{
			var item   = ui.item,
				parent = item.parent();
				
			FLBuilder._blockDragStop(e, ui);

			// A row was dropped back into the row list.
			if(parent.hasClass('fl-builder-rows')) {
				item.remove();
				return;
			}
			// Add a new row.
			else if(item.hasClass('fl-builder-block')) {
			
				// A row was dropped into another row.
				if(parent.hasClass('fl-row-content')) {
					FLBuilder._addColGroup(
						item.closest('.fl-row').attr('data-node'),
						item.attr('data-cols'), 
						parent.children('.fl-col-group, .fl-builder-block').index(item)
					);
				}
				// A row was dropped into the main layout.
				else {  
					FLBuilder._addRow(
						item.attr('data-cols'), 
						parent.children('.fl-row, .fl-builder-block').index(item)
					);
				}

				// Remove the helper.
				item.remove();
				
				// Show the builder panel.
				FLBuilder._showPanel();
				
				// Show the module list.
				$('.fl-builder-modules').siblings('.fl-builder-blocks-section-title').eq(0).trigger('click');
			}
			// Reorder a row.
			else {
				FLBuilder._reorderRow(
					item.attr('data-node'), 
					parent.children('.fl-row').index(item)
				);
			}
		},
		
		/**
		 * Reorders a row within the layout.
		 *
		 * @since 1.0
		 * @access private
		 * @method _reorderRow
		 * @param {String} node_id The node ID of the row.
		 * @param {Number} position The new position.
		 */     
		_reorderRow: function(node_id, position)
		{
			FLBuilder.ajax({
				action: 'fl_builder_save',
				method: 'reorder_node',
				node_id: node_id,
				position: position,
				silent: true
			}); 
		},
		
		/**
		 * Adds a new row to the layout.
		 *
		 * @since 1.0
		 * @access private
		 * @method _addRow
		 * @param {String} cols The type of column layout to use.
		 * @param {Number} position The position of the new row.
		 */     
		_addRow: function(cols, position)
		{
			FLBuilder.showAjaxLoader();
		
			FLBuilder._newRowPosition = position;
			
			FLBuilder.ajax({
				action: 'fl_builder_render_new_row',
				cols: cols,
				position: position
			}, FLBuilder._addRowComplete);
		},
		
		/**
		 * Adds the HTML for a new row to the layout when the AJAX
		 * add operation is complete. Adds a module if one is queued
		 * to go in the new row.
		 *
		 * @since 1.0
		 * @access private
		 * @method _addRowComplete
		 * @param {String} response The HTML for the new row.
		 */     
		_addRowComplete: function(response)
		{
			var content = $(FLBuilder._contentClass),
				rows    = content.find('.fl-row'),
				row     = $(response),
				module  = null,
				form    = null;
				
			if(rows.length === 0 || rows.length == FLBuilder._newRowPosition) {
				content.append(row);
			}
			else {
				rows.eq(FLBuilder._newRowPosition).before(row);
			}
			
			FLBuilder._setupEmptyLayout();
			FLBuilder._highlightEmptyCols();
			FLBuilder._initSortables();
			
			// Add a module to the newly created row.
			if(FLBuilder._addModuleAfterRowRender !== null) {
			
				// Add an existing module. 
				if(FLBuilder._addModuleAfterRowRender.hasClass('fl-module')) {
					module = FLBuilder._addModuleAfterRowRender;
					row.find('.fl-col-content').eq(0).append(module);
					FLBuilder._reorderModule(module);
				}
				
				FLBuilder._highlightEmptyCols();
				FLBuilder._addModuleAfterRowRender = null;
			}
		},
		
		/**
		 * Callback for when the delete row button is clicked.
		 *
		 * @since 1.0
		 * @access private
		 * @method _deleteRowClicked
		 * @param {Object} e The event object.
		 */
		_deleteRowClicked: function( e )
		{
			var nodeId = $(this).closest('.fl-row-overlay').attr('data-node'),
				row    = $('.fl-row[data-node='+ nodeId +']'),
				result = null;

			if(!row.find('.fl-module').length) {
				FLBuilder._deleteRow(row);
			} 
			else {
				result = confirm(FLBuilderStrings.deleteRowMessage);
				
				if(result) {
					FLBuilder._deleteRow(row);
				}
			}
			
			FLBuilder._removeAllOverlays();
			e.stopPropagation();
		},
		
		/**
		 * Deletes a row.
		 *
		 * @since 1.0
		 * @access private
		 * @method _deleteRow
		 * @param {Object} row A jQuery reference of the row to delete.
		 */
		_deleteRow: function(row)
		{
			FLBuilder.ajax({
				action: 'fl_builder_save',
				method: 'delete_node',
				node_id: row.attr('data-node'),
				silent: true
			});
			
			row.empty();
			row.remove();
			FLBuilder._setupEmptyLayout();
			FLBuilder._removeRowOverlays();
		},
		
		/**
		 * Duplicates a row.
		 *
		 * @since 1.3.8
		 * @access private
		 * @method _rowCopyClicked
		 * @param {Object} e The event object.
		 */ 
		_rowCopyClicked: function(e)
		{
			var nodeId = $(this).closest('.fl-row-overlay').attr('data-node');
			
			FLBuilder.showAjaxLoader();
			
			FLBuilder._removeAllOverlays();
			
			FLBuilder.ajax({
				action: 'fl_builder_save',
				method: 'copy_row',
				node_id: nodeId
			}, FLBuilder._updateLayout);
			
			e.stopPropagation();
		},
		
		/**
		 * Shows the settings lightbox and loads the row settings
		 * when the row settings button is clicked.
		 *
		 * @since 1.0
		 * @access private
		 * @method _rowSettingsClicked
		 */
		_rowSettingsClicked: function( e )
		{
			var button = $( this ),
				nodeId = button.closest( '.fl-row-overlay' ).attr( 'data-node' ),
				global = button.closest( '.fl-block-overlay-global' ).length > 0;
			
			if ( global && 'row' != FLBuilderConfig.userTemplateType ) {
				if ( FLBuilderConfig.userCanEditGlobalTemplates ) {
					window.open( $( '.fl-row[data-node="' + nodeId + '"]' ).attr( 'data-template-url' ) );
				}
			}
			else if ( button.hasClass( 'fl-block-settings' ) ) {
				
				FLBuilder._closePanel();
				FLBuilder._showLightbox();
				
				FLBuilder.ajax({
					action: 'fl_builder_render_row_settings',
					node_id: nodeId
				}, FLBuilder._rowSettingsLoaded);
			}
			
			e.stopPropagation();
		},
		
		/**
		 * Sets the lightbox content when the row settings have 
		 * loaded and creates a new preview.
		 *
		 * @since 1.0
		 * @access private
		 * @method _rowSettingsLoaded
		 * @param {String} response The HTML for the row settings form.
		 */
		_rowSettingsLoaded: function(response)
		{
			FLBuilder._setSettingsFormContent(response);
			
			FLBuilder.preview = new FLBuilderPreview({ type : 'row' });
		},
		
		/* Columns
		----------------------------------------------------------*/
		
		/**
		 * Shows an overlay with actions when the mouse enters a column.
		 *
		 * @since 1.1.9
		 * @access private
		 * @method _colMouseenter
		 */
		_colMouseenter: function()
		{
			var col 	 	  	= $( this ),
				global		  	= col.hasClass( 'fl-node-global' ),
				parentGlobal  	= col.parents( '.fl-node-global' ).length > 0,
				numCols		  	= col.parents( '.fl-col-group' ).find( '.fl-col' ).length,
				first   		= 0 === col.index(),
				last    		= numCols === col.index() + 1,
				template 		= wp.template( 'fl-col-overlay' );

			if ( FLBuilderConfig.simpleUi ) {
				return;
			}
			else if ( global && parentGlobal && 'row' != FLBuilderConfig.userTemplateType ) {
				return;
			}
			else if ( col.find( '.fl-module' ).length > 0 ) {
				return;
			}
			else if ( ! col.hasClass( 'fl-block-overlay-active' ) ) {
				
				// Remove existing overlays.
				FLBuilder._removeColOverlays();
				FLBuilder._removeModuleOverlays();
				
				// Append the template.
				FLBuilder._appendOverlay( col, template( {
					global	: global,
					numCols	: numCols,
					first   : first,
					last   	: last
				} ) );
				
				// Init column resizing.
				FLBuilder._initColDragResizing();
			}
			
			$( 'body' ).addClass( 'fl-block-overlay-muted' );
		},
		
		/**
		 * Removes overlays when the mouse leaves a column.
		 *
		 * @since 1.1.9
		 * @access private
		 * @method _colMouseleave
		 * @param {Object} e The event object.
		 */
		_colMouseleave: function(e)
		{
			var col             = $(this),
				toElement       = $(e.toElement) || $(e.relatedTarget),
				hasModules      = col.find('.fl-module').length > 0,
				isTipTip        = toElement.is('#tiptip_holder'),
				isTipTipChild   = toElement.closest('#tiptip_holder').length > 0;
			
			if(hasModules || isTipTip || isTipTipChild) {
				return;
			}
			
			FLBuilder._removeColOverlays();
		},
		
		/**
		 * Removes all column overlays from the page.
		 *
		 * @since 1.6.4
		 * @access private
		 * @method _removeColOverlays
		 */
		_removeColOverlays: function()
		{
			var cols = $( '.fl-col' );
			
			cols.removeClass('fl-block-overlay-active');
			cols.find('.fl-col-overlay').remove();
			$('body').removeClass('fl-block-overlay-muted');
		},
		
		/**
		 * Shows the settings lightbox and loads the column settings
		 * when the column settings button is clicked.
		 *
		 * @since 1.1.9
		 * @access private
		 * @method _colSettingsClicked
		 * @param {Object} e The event object.
		 */
		_colSettingsClicked: function(e)
		{
			var nodeId = $(this).closest('.fl-col').attr('data-node');
			
			if ( FLBuilder._colResizing ) {
				return;
			}
			
			FLBuilder._closePanel();
			FLBuilder._showLightbox();
			
			FLBuilder.ajax({
				action: 'fl_builder_render_column_settings',
				node_id: nodeId
			}, FLBuilder._colSettingsLoaded);
			
			e.stopPropagation();
		},
		
		/**
		 * Sets the lightbox content when the column settings have 
		 * loaded and creates a new preview.
		 *
		 * @since 1.1.9
		 * @access private
		 * @method _colSettingsLoaded
		 * @param {String} response The HTML for the column settings form.
		 */
		_colSettingsLoaded: function(response)
		{
			FLBuilder._setSettingsFormContent(response);
			
			var settings = $('.fl-builder-col-settings'),
				nodeId   = settings.data('node'),
				col      = $('.fl-col[data-node="' + nodeId + '"]');
				
			if(col.siblings('.fl-col').length === 0) {
				$(settings).find('#fl-builder-settings-section-general').css('display', 'none');
			}
			
			FLBuilder.preview = new FLBuilderPreview({ type : 'col' });
		},
		
		/**
		 * Callback for when the delete column button is clicked.
		 *
		 * @since 1.0
		 * @access private
		 * @method _deleteColClicked
		 * @param {Object} e The event object.
		 */
		_deleteColClicked: function( e )
		{
			var button = $( this ),
				col    = button.closest( '.fl-col' ),
				module = button.closest( '.fl-module' ),
				result = true;
				
			if ( module.length > 0 ) {
				result = confirm( FLBuilderStrings.deleteColumnMessage );	
			}
			if ( result ) {
				FLBuilder._deleteCol( col );
				FLBuilder._removeAllOverlays();
			}
			
			e.stopPropagation();
		},
		
		/**
		 * Deletes a column.
		 *
		 * @since 1.0
		 * @access private
		 * @method _deleteCol
		 * @param {Object} col A jQuery reference of the column to delete.
		 */
		_deleteCol: function(col)
		{
			var row   = col.closest('.fl-row'),
				group = col.closest('.fl-col-group'),
				cols  = null,
				width = 0;
				
			col.remove();
			rowCols   = row.find('.fl-col');
			groupCols = group.find('.fl-col');
			
			if(0 === rowCols.length && 'row' != FLBuilderConfig.userTemplateType) {
				FLBuilder._deleteRow(row);
			}
			else {
				
				if(0 === groupCols.length) {
					group.remove();
				}
				else {
					
					if ( 6 === groupCols.length ) {
						width = 16.65;
					}
					else if ( 7 === groupCols.length ) {
						width = 14.28;
					}
					else {
						width = Math.round( 100 / groupCols.length * 100 ) / 100;
					}
					
					groupCols.css('width', width + '%');
				}
			
				FLBuilder.ajax({
					action          : 'fl_builder_save',
					method          : 'delete_col',
					node_id         : col.attr('data-node'),
					new_width       : width,
					silent          : true
				});
			}
		},
		
		/**
		 * Inserts a column before another column when the insert
		 * column before link is clicked.
		 *
		 * @since 1.6.4
		 * @access private
		 * @method _insertColBeforeClicked
		 * @param {Object} e The event object.
		 */
		_insertColBeforeClicked: function( e )
		{
			FLBuilder._insertCol( $( this ).closest( '.fl-col' ), 'before' );
			
			e.stopPropagation();
		},
		
		/**
		 * Inserts a column after another column when the insert
		 * column after link is clicked.
		 *
		 * @since 1.6.4
		 * @access private
		 * @method _insertColAfterClicked
		 * @param {Object} e The event object.
		 */
		_insertColAfterClicked: function( e )
		{
			FLBuilder._insertCol( $( this ).closest( '.fl-col' ), 'after' );
			
			e.stopPropagation();
		},
		
		/**
		 * Inserts a column before or after another column.
		 *
		 * @since 1.6.4
		 * @access private
		 * @method _insertCol
		 * @param {Object} col A jQuery reference of the column to insert before or after.
		 * @param {String} insert Either before or after.
		 */
		_insertCol: function( col, insert )
		{
			FLBuilder.showAjaxLoader();
			FLBuilder._removeAllOverlays();
			
			FLBuilder.ajax( {
				action          : 'fl_builder_save',
				method          : 'insert_col',
				node_id         : col.attr('data-node'),
				insert 			: insert
			}, FLBuilder._updateLayout );
		},
		
		/**
		 * Adds a new column group to the layout.
		 *
		 * @since 1.0
		 * @access private
		 * @method _addColGroup
		 * @param {String} nodeId The node ID of the parent row.
		 * @param {String} cols The type of column layout to use.
		 * @param {Number} position The position of the new column group.
		 */
		_addColGroup: function(nodeId, cols, position)
		{
			FLBuilder.showAjaxLoader();
			
			FLBuilder._newColGroupParent = $('.fl-node-' + nodeId + ' .fl-row-content');
			FLBuilder._newColGroupPosition = position;
			
			FLBuilder.ajax({
				action      : 'fl_builder_render_new_column_group',
				cols        : cols,
				node_id     : nodeId,
				position    : position
			}, FLBuilder._addColGroupComplete);
		},
		
		/**
		 * Adds the HTML for a new column group to the layout when 
		 * the AJAX add operation is complete. Adds a module if one 
		 * is queued to go in the new column group.
		 *
		 * @since 1.0
		 * @access private
		 * @method _addColGroupComplete
		 * @param {String} response The HTML for the new column group.
		 */     
		_addColGroupComplete: function(response)
		{
			var rowContent  = FLBuilder._newColGroupParent,
				groups      = rowContent.find('.fl-col-group'),
				group       = $(response),
				col         = group.find('.fl-col-content').eq(0),
				module      = null,
				form        = null;
			  
			if(groups.length === 0 || groups.length == FLBuilder._newColGroupPosition) {
				rowContent.append(group);
			}
			else {
				groups.eq(FLBuilder._newColGroupPosition).before(group);
			}

			// Add a module to the newly created column group.
			if(FLBuilder._addModuleAfterRowRender !== null) {
			
				// Add an existing module. 
				if(FLBuilder._addModuleAfterRowRender.hasClass('fl-module')) {
					module = FLBuilder._addModuleAfterRowRender;
					col.append(module);
					FLBuilder._reorderModule(module);
				}
				
				FLBuilder._addModuleAfterRowRender = null;
			}
			
			FLBuilder._highlightEmptyCols();
			FLBuilder._initSortables();
		},
		
		/**
		 * Initializes draggables for column resizing.
		 *
		 * @since 1.6.4
		 * @access private
		 * @method _initColDragResizing
		 */
		_initColDragResizing: function()
		{
			$( '.fl-block-col-resize' ).draggable( {
				axis 	: 'x',
				start 	: FLBuilder._colDragResizeStart,
				drag	: FLBuilder._colDragResize,
				stop 	: FLBuilder._colDragResizeStop
			} );
		},
		
		/**
		 * Fires when dragging for a column resize starts.
		 *
		 * @since 1.6.4
		 * @access private
		 * @method _colDragResizeStart
		 * @param {Object} e The event object.
		 * @param {Object} ui An object with additional info for the drag.
		 */
		_colDragResizeStart: function( e, ui )
		{
			// Setup resize vars.
			var handle 		= $( ui.helper ),
				direction 	= '',
				group		= handle.closest( '.fl-col-group' ),
				cols 		= group.find( '.fl-col' ),
				col 		= handle.closest( '.fl-col' ),
				sibling 	= null,
				availWidth  = 100,
				i 			= 0;
			
			// Find the direction and sibling.
			if ( handle.hasClass( 'fl-block-col-resize-e' ) ) {
				direction = 'e';
				sibling = col.next( '.fl-col' );
			}
			else {
				direction = 'w';
				sibling = col.prev( '.fl-col' );
			}
			
			// Find the available width.
			for ( ; i < cols.length; i++ ) {
				
				if ( cols.eq( i ).data( 'node' ) == col.data( 'node' ) ) {
					continue;
				}
				if ( cols.eq( i ).data( 'node' ) == sibling.data( 'node' ) ) {
					continue;
				}
				
				availWidth -= parseFloat( cols.eq( i )[ 0 ].style.width );
			}
			
			// Build the resize data object.
			FLBuilder._colResizeData = {
				handle			: handle,
				feedbackLeft	: handle.find( '.fl-block-col-resize-feedback-left' ),
				feedbackRight	: handle.find( '.fl-block-col-resize-feedback-right' ),
				direction		: direction,
				groupWidth		: group.outerWidth(),
				col 			: col,
				colWidth 		: parseFloat( col[ 0 ].style.width ) / 100,
				sibling 		: sibling,
				offset  		: ui.position.left,
				availWidth		: availWidth
			};
			
			// Set the resizing flag.
			FLBuilder._colResizing = true;
			
			// Close the builder panel and destroy overlay events.
			FLBuilder._closePanel();
			FLBuilder._destroyOverlayEvents();
		},
		
		/**
		 * Fires when dragging for a column resize is in progress.
		 *
		 * @since 1.6.4
		 * @access private
		 * @method _colDragResize
		 * @param {Object} e The event object.
		 * @param {Object} ui An object with additional info for the drag.
		 */
		_colDragResize: function( e, ui )
		{
			// Setup resize vars.
			var data 			= FLBuilder._colResizeData,
				change 			= ( data.offset - ui.position.left ) / data.groupWidth,
				colWidth 		= 'e' == data.direction ? ( data.colWidth - change ) * 100 : ( data.colWidth + change ) * 100,
				colRound 		= Math.round( colWidth * 100 ) / 100,
				siblingWidth	= data.availWidth - colWidth,
				siblingRound	= Math.round( siblingWidth * 100 ) / 100,
				minRound		= 10,
				maxRound		= Math.round( ( data.availWidth - 10 ) * 100 ) / 100;
			
			// Set the min/max width if needed.
			if ( colRound < 10 ) {
				colRound 		= minRound;
				siblingRound 	= maxRound;
			}
			else if ( siblingRound < 10 ) {
				colRound 		= maxRound;
				siblingRound 	= minRound;
			}
			
			// Set the feedback values.
			if ( 'e' == data.direction ) {
				data.feedbackLeft.html( colRound.toFixed( 1 ) + '%'  ).show();
				data.feedbackRight.html( siblingRound.toFixed( 1 ) + '%'  ).show();
			}
			else {
				data.feedbackLeft.html( siblingRound.toFixed( 1 ) + '%'  ).show();
				data.feedbackRight.html( colRound.toFixed( 1 ) + '%'  ).show();
			}
			
			// Set the width attributes.
			data.col.css( 'width', colRound + '%' );
			data.sibling.css( 'width', siblingRound + '%' );
		},
		
		/**
		 * Fires when dragging for a column resize stops.
		 *
		 * @since 1.6.4
		 * @access private
		 * @method _colDragResizeStop
		 * @param {Object} e The event object.
		 * @param {Object} ui An object with additional info for the drag.
		 */
		_colDragResizeStop: function( e, ui )
		{
			var data = FLBuilder._colResizeData;
			
			// Hide the feedback divs.
			FLBuilder._colResizeData.feedbackLeft.hide();
			FLBuilder._colResizeData.feedbackRight.hide();
			
			// Save the resize data.
			FLBuilder.ajax({
				action			: 'fl_builder_save',
				method			: 'resize_cols',
				col_id			: data.col.data( 'node' ),
				col_width		: parseFloat( data.col[ 0 ].style.width ),
				sibling_id		: data.sibling.data( 'node' ),
				sibling_width	: parseFloat( data.sibling[ 0 ].style.width ),
				silent			: true
			});
			
			// Reset the resize data.
			FLBuilder._colResizeData = null;
			
			// Rebind overlay events.
			FLBuilder._bindOverlayEvents();
			
			// Set the resizing flag to false with a timeout so other events get the right value.
			setTimeout( function() { FLBuilder._colResizing = false; }, 50 );
		},
		
		/**
		 * Resets the widths of all columns in a group.
		 *
		 * @since 1.6.4
		 * @access private
		 * @method _resetColumnWidths
		 * @param {Object} e The event object.
		 */
		_resetColumnWidths: function( e )
		{
			var group = $( this ).closest( '.fl-col-group' ),
				cols  = group.find( '.fl-col' ),
				width = 0;
			
			// Get the new width.
			if ( 6 === cols.length ) {
				width = 16.65;
			}
			else if ( 7 === cols.length ) {
				width = 14.28;
			}
			else {
				width = Math.round( 100 / cols.length * 100 ) / 100;
			}
			
			// Apply it to the columns.
			cols.css('width', width + '%');
			
			// Save the resize data.
			FLBuilder.ajax({
				action		: 'fl_builder_save',
				method		: 'reset_col_widths',
				group_id	: group.data( 'node' ),
				silent		: true
			});
			
			e.stopPropagation();
		},
		
		/* Modules
		----------------------------------------------------------*/
		
		/**
		 * Shows an overlay with actions when the mouse enters a module.
		 *
		 * @since 1.0
		 * @access private
		 * @method _moduleMouseenter
		 */
		_moduleMouseenter: function()
		{
			var module        = $( this ),
				moduleName    = module.attr( 'data-name' ),
				global		  = module.hasClass( 'fl-node-global' ),
				parentGlobal  = module.parents( '.fl-node-global' ).length > 0,
				numCols		  = module.parents( '.fl-col-group' ).find( '.fl-col' ).length,
				parentCol	  = module.parents( '.fl-col' ),
				parentFirst   = 0 === parentCol.index(),
				parentLast    = numCols === parentCol.index() + 1,
				template	  = wp.template( 'fl-module-overlay' );
				
			// Remove existing overlays.
			FLBuilder._removeColOverlays();
			FLBuilder._removeModuleOverlays();
			
			// Don't show if this is a global row in a standard layout.
			if ( global && parentGlobal && 'row' != FLBuilderConfig.userTemplateType ) {
				return;
			}
			// Show the overlay.
			else if ( ! module.hasClass( 'fl-block-overlay-active' ) ) {

				// Adjust the height if needed.
				if ( module.outerHeight( true ) < 20 ) {
					module.addClass( 'fl-module-adjust-height' );
				}
				
				// Append the template.
				FLBuilder._appendOverlay( module, template( { 
					global 		: global, 
					moduleName	: moduleName,
					numCols		: numCols,
					parentFirst : parentFirst,
					parentLast  : parentLast
				} ) );
				
				// Init column resizing.
				FLBuilder._initColDragResizing();
			}
			
			$( 'body' ).addClass( 'fl-block-overlay-muted' );
		},
		
		/**
		 * Removes overlays when the mouse leaves a module.
		 *
		 * @since 1.0
		 * @access private
		 * @method _moduleMouseleave
		 * @param {Object} e The event object.
		 */
		_moduleMouseleave: function(e)
		{
			var module          = $(this),
				toElement       = $(e.toElement) || $(e.relatedTarget),
				isTipTip        = toElement.is('#tiptip_holder'),
				isTipTipChild   = toElement.closest('#tiptip_holder').length > 0;
			
			if(isTipTip || isTipTipChild) {
				return;
			}
			
			FLBuilder._removeModuleOverlays();
		},
		
		/**
		 * Removes all module overlays from the page.
		 *
		 * @since 1.6.4
		 * @access private
		 * @method _removeModuleOverlays
		 */
		_removeModuleOverlays: function()
		{
			var modules = $('.fl-module');
			
			modules.removeClass('fl-module-adjust-height');
			modules.removeClass('fl-block-overlay-active');
			modules.find('.fl-module-overlay').remove();
			$('body').removeClass('fl-block-overlay-muted');
		},
		
		/**
		 * Returns a helper element for module drag operations.
		 *
		 * @since 1.0
		 * @access private
		 * @method _moduleDragHelper
		 * @param {Object} e The event object.
		 * @param {Object} item The element being dragged.
		 * @return {Object} The helper element.
		 */
		_moduleDragHelper: function(e, item)
		{   
			return $('<div class="fl-builder-block-drag-helper">' + item.attr('data-name') + '</div>');
		},
		
		/**
		 * Callback for when a module drag operation completes.
		 *
		 * @since 1.0
		 * @access private
		 * @method _moduleDragStop
		 * @param {Object} e The event object.
		 * @param {Object} ui An object with additional info for the drag.
		 */
		_moduleDragStop: function(e, ui)
		{
			var item     = ui.item,
				parent   = item.parent(),
				position = 0,
				parentId = 0;
			
			FLBuilder._blockDragStop(e, ui);
			
			// A module was dropped back into the module list.
			if(parent.hasClass('fl-builder-modules') || parent.hasClass('fl-builder-widgets')) {
				item.remove();
				return;
			}
			// A new module was dropped.
			else if(item.hasClass('fl-builder-block')) {
			
				// A new module was dropped into a row position.
				if(parent.hasClass('fl-builder-content')) {
					position = parent.children('.fl-row, .fl-builder-block').index(item);
					parentId = 0;
				}
				// A new module was dropped into a column position.
				else if(parent.hasClass('fl-row-content')) {
					position = parent.children('.fl-col-group, .fl-builder-block').index(item);
					parentId = item.closest('.fl-row').attr('data-node');
				}
				// A new module was dropped into a column.
				else {
					position = parent.children('.fl-module, .fl-builder-block').index(item);
					parentId = item.closest('.fl-col').attr('data-node');
				}
				
				// Add the new module.
				FLBuilder._addModule(parentId, item.attr('data-type'), position, item.attr('data-widget'))
				
				// Remove the drag helper.
				ui.item.remove();
			}
			// A module was dropped into the main layout.
			else if(parent.hasClass('fl-builder-content')) {
				position = parent.children('.fl-row, .fl-module').index(item);
				FLBuilder._addModuleAfterRowRender = item;
				FLBuilder._addRow('1-col', position);
				item.remove();
			}
			// A module was dropped into a column position.
			else if(parent.hasClass('fl-row-content')) {
				position = parent.children('.fl-col-group, .fl-module').index(item);
				FLBuilder._addModuleAfterRowRender = item;
				FLBuilder._addColGroup(item.closest('.fl-row').attr('data-node'), '1-col', position);
				item.remove();
			}
			// A module was dropped into another column.
			else {
				FLBuilder._reorderModule(item);
			}
			
			FLBuilder._resizeLayout();
		},
		
		/**
		 * Reorders a module within a column.
		 *
		 * @since 1.0
		 * @access private
		 * @method _reorderModule
		 * @param {Object} module The module element being dragged.
		 */
		_reorderModule: function(module)
		{
			var newParent = module.closest('.fl-col').attr('data-node'),
				oldParent = module.attr('data-parent'),
				node_id   = module.attr('data-node'),
				position  = module.index();
				 
			if(newParent == oldParent) {
				FLBuilder.ajax({
					action: 'fl_builder_save',
					method: 'reorder_node',
					node_id: node_id,
					position: position,
					silent: true
				});
			}
			else {
				module.attr('data-parent', newParent);
			
				FLBuilder.ajax({
					action: 'fl_builder_save',
					method: 'move_node',
					new_parent: newParent,
					node_id: node_id,
					position: position,
					silent: true
				});
			}
		},
		
		/**
		 * Callback for when the delete module button is clicked.
		 *
		 * @since 1.0
		 * @access private
		 * @method _deleteModuleClicked
		 * @param {Object} e The event object.
		 */
		_deleteModuleClicked: function(e)
		{
			var module = $(this).closest('.fl-module'),
				result = confirm(FLBuilderStrings.deleteModuleMessage);
			
			if(result) {
				FLBuilder._deleteModule(module);
				FLBuilder._removeAllOverlays();
			}
			
			e.stopPropagation();
		},
		
		/**
		 * Deletes a module.
		 *
		 * @since 1.0
		 * @access private
		 * @method _deleteModule
		 * @param {Object} module A jQuery reference of the module to delete.
		 */
		_deleteModule: function(module)
		{
			var row = module.closest('.fl-row');

			FLBuilder.ajax({
				action: 'fl_builder_save',
				method: 'delete_node',
				node_id: module.attr('data-node'),
				silent: true
			});
			
			module.empty();
			module.remove();
			row.removeClass('fl-block-overlay-muted');
			FLBuilder._highlightEmptyCols();
			FLBuilder._removeAllOverlays();
		},
		
		/**
		 * Duplicates a module.
		 *
		 * @since 1.0
		 * @access private
		 * @method _moduleCopyClicked
		 * @param {Object} e The event object.
		 */ 
		_moduleCopyClicked: function(e)
		{
			var module = $(this).closest('.fl-module');
			
			FLBuilder.showAjaxLoader();
			FLBuilder._removeAllOverlays();
			
			FLBuilder.ajax({
				action: 'fl_builder_save',
				method: 'copy_module',
				node_id: module.attr('data-node')
			}, FLBuilder._updateLayout);
			
			e.stopPropagation();
		},
		
		/**
		 * Shows the settings lightbox and loads the module settings
		 * when the module settings button is clicked.
		 *
		 * @since 1.0
		 * @access private
		 * @method _moduleSettingsClicked
		 * @param {Object} e The event object.
		 */ 
		_moduleSettingsClicked: function(e)
		{
			var button   = $( this ),
				nodeId   = button.closest( '.fl-module' ).attr( 'data-node' ),
				parentId = button.closest( '.fl-col' ).attr( 'data-node' ),
				type     = button.closest( '.fl-module' ).attr( 'data-type' ),
				global 	 = button.closest( '.fl-block-overlay-global' ).length > 0;
			
			e.stopPropagation();
			
			if ( FLBuilder._colResizing ) {
				return;
			}
			if ( global && ! FLBuilderConfig.userCanEditGlobalTemplates ) {
				return;
			}
			
			FLBuilder._showModuleSettings(nodeId, parentId, type);
		},
		
		/**
		 * Shows the lightbox and loads the settings for a module.
		 *
		 * @since 1.0
		 * @access private
		 * @method _showModuleSettings
		 * @param {String} nodeId The node ID for the module.
		 * @param {String} parentId The node ID for the module's parent.
		 * @param {String} type The type of module.
		 */
		_showModuleSettings: function(nodeId, parentId, type)
		{
			FLBuilder._closePanel();
			FLBuilder._showLightbox();
			
			FLBuilder.ajax({
				action: 'fl_builder_render_module_settings',
				node_id: nodeId,
				type: type,
				parent_id: parentId
			}, FLBuilder._moduleSettingsLoaded);
		},
		
		/**
		 * Sets the lightbox content when the module settings have 
		 * loaded and creates a new preview.
		 *
		 * @since 1.0
		 * @access private
		 * @method _moduleSettingsLoaded
		 * @param {Object} data Preview layout data. Can also be a JSON encoded string.
		 */ 
		_moduleSettingsLoaded: function(data)
		{
			var preview   = typeof data == 'string' ? null : data.layout,
				content   = typeof data == 'string' ? data : data.settings,
				html      = $('<div>'+ content +'</div>'),
				link      = html.find('link.fl-builder-settings-css'),
				script    = html.find('script.fl-builder-settings-js'),
				form      = html.find('.fl-builder-settings'),
				type      = form.attr('data-type'),
				helper    = null;
			
			// Append the settings css and js?
			if($.inArray(type, FLBuilder._loadedModuleAssets) > -1) {
				link.remove();
				script.remove();
			}
			else {
				$('head').append(link);
				$('head').append(script);
				FLBuilder._loadedModuleAssets.push(type);
			}
			
			// Set the content.
			FLBuilder._setSettingsFormContent(html);
			
			// Create a new preview.
			FLBuilder.preview = new FLBuilderPreview({ 
				type    : 'module',
				layout  : preview
			});
			
			// Init the settings form helper.
			helper = FLBuilder._moduleHelpers[type];
			
			if(typeof helper !== 'undefined') {
				FLBuilder._initSettingsValidation(helper.rules);
				helper.init();
			}
		},
		
		/**
		 * Validates the module settings and saves them if 
		 * the form is valid.
		 *
		 * @since 1.0
		 * @access private
		 * @method _saveModuleClicked
		 */ 
		_saveModuleClicked: function()
		{
			var form      = $(this).closest('.fl-builder-settings'),
				type      = form.attr('data-type'),
				id        = form.attr('data-node'),
				helper    = FLBuilder._moduleHelpers[type],
				valid     = true;
			
			if(typeof helper !== 'undefined') {
				
				form.find('label.error').remove();
				form.validate().hideErrors();
				valid = form.validate().form();
				
				if(valid) {
					valid = helper.submit();
				}
			}
			if(valid) {
				FLBuilder._saveSettings();
				FLBuilder._lightbox.close();
			}
			else {
				FLBuilder._toggleSettingsTabErrors();
			}
		},
		
		/**
		 * Adds a new module to a column and loads the settings.
		 *
		 * @since 1.0
		 * @access private
		 * @method _addModule
		 * @param {String} parentId The node id of the new module's parent.
		 * @param {String} type The type of module to add.
		 * @param {Number} position The position of the new module within its parent.
		 * @param {String} widget The type of widget if this module is a widget.
		 */ 
		_addModule: function(parentId, type, position, widget)
		{
			FLBuilder.showAjaxLoader();
			
			FLBuilder.ajax({
				action          : 'fl_builder_render_new_module_settings',
				parent_id       : parentId,
				type            : type,
				position        : position,
				node_preview    : 1,
				widget          : typeof widget === 'undefined' ? '' : widget
			}, FLBuilder._addModuleComplete);
		},
		
		/**
		 * Shows the settings lightbox and sets the content when
		 * the module settings have finished loading.
		 *
		 * @since 1.0
		 * @access private
		 * @method _addModuleComplete
		 * @param {String} response The JSON encoded response.
		 */ 
		_addModuleComplete: function(response)
		{
			var data = JSON.parse(response);
			
			FLBuilder._showLightbox();
			FLBuilder._moduleSettingsLoaded(data);
			
			$('.fl-builder-module-settings').data('new-module', '1');
		},
		
		/**
		 * Registers a helper class for a module's settings.
		 *
		 * @since 1.0
		 * @method registerModuleHelper
		 * @param {String} type The type of module.
		 * @param {Object} obj The module helper.
		 */ 
		registerModuleHelper: function(type, obj)
		{
			var defaults = {
				rules: {},
				init: function(){},
				submit: function(){ return true; },
				preview: function(){}
			};
			
			FLBuilder._moduleHelpers[type] = $.extend({}, defaults, obj);
		},
		
		/**
		 * Deprecated. Use the public method registerModuleHelper instead.
		 *
		 * @since 1.0
		 * @access private
		 * @method _registerModuleHelper
		 * @param {String} type The type of module.
		 * @param {Object} obj The module helper.
		 */ 
		_registerModuleHelper: function(type, obj)
		{
			FLBuilder.registerModuleHelper(type, obj);
		},

		/* Node Templates
		----------------------------------------------------------*/

		/**
		 * Saves a node's settings and shows the node template settings
		 * when the Save As button is clicked.
		 *
		 * @since 1.6.3
		 * @access private
		 * @method _showNodeTemplateSettings
		 * @param {Object} e An event object.
		 */ 
		_showNodeTemplateSettings: function( e )
		{
			var form = $( '.fl-builder-settings-lightbox .fl-builder-settings' );
				
			FLBuilder._saveSettings();
			
			FLBuilder.ajax( {
				action  : 'fl_builder_render_node_template_settings',
				node_id : form.attr( 'data-node' )
			}, FLBuilder._nodeTemplateSettingsLoaded );
		},
		
		/**
		 * Sets the lightbox content when the node template settings have loaded.
		 *
		 * @since 1.6.3
		 * @access private
		 * @method _nodeTemplateSettingsLoaded
		 * @param {String} response The HTML for the settings form.
		 */
		_nodeTemplateSettingsLoaded: function( response )
		{
			FLBuilder._showLightbox( false );
			FLBuilder._setSettingsFormContent( response );
			
			FLBuilder._initSettingsValidation({
				name: {
					required: true
				}
			});
		},
		
		/**
		 * Saves a node as a template when the save button is clicked.
		 *
		 * @since 1.6.3
		 * @access private
		 * @method _saveNodeTemplate
		 */ 
		_saveNodeTemplate: function()
		{
			var form  = $( '.fl-builder-settings-lightbox .fl-builder-settings' ),
				valid = form.validate().form();
				
			if ( valid ) {
					 
				FLBuilder.showAjaxLoader();
				
				FLBuilder.ajax({
					action	 : 'fl_builder_save',
					method	 : 'save_node_template',
					node_id  : form.attr( 'data-node' ),
					settings : FLBuilder._getSettings( form )
				}, FLBuilder._saveNodeTemplateComplete);
					
				FLBuilder._lightbox.close();
			}
		},
		
		/**
		 * Callback for when a node template has been saved.
		 *
		 * @since 1.6.3
		 * @access private
		 * @method _saveNodeTemplateComplete
		 */ 
		_saveNodeTemplateComplete: function( response )
		{
			var data 		= JSON.parse( response ),
				panel 		= $( '.fl-builder-saved-' + data.type + 's' ),
				blocks 		= panel.find( '.fl-builder-block' ),
				block   	= null,
				text    	= '',
				name    	= data.name.toLowerCase(),
				i			= 0,
				template 	= wp.template( 'fl-node-template-block' );
			
			// Show the success alert.
			if ( 'row' == data.type ) {
				FLBuilder.alert( FLBuilderStrings.rowTemplateSaved );
			}
			else if ( 'module' == data.type ) {
				FLBuilder.alert( FLBuilderStrings.moduleTemplateSaved );
			}
			
			// Update the layout for global templates.			
			if ( data.global ) {
				FLBuilder._updateLayout();
			}
			
			// Add the new template to the builder panel.
			if ( 0 === blocks.length ) {
				panel.append( template( data ) );
			}
			else {
				
				for ( ; i < blocks.length; i++ ) {
					
					block = blocks.eq( i );
					text  = block.text().toLowerCase().trim();
					
					if ( 0 === i && name < text ) {
						panel.prepend( template( data ) );
						break;
					}
					else if ( name < text ) {
						block.before( template( data ) );
						break;
					}
					else if ( blocks.length - 1 === i ) {
						panel.append( template( data ) );
						break;
					}
				}
			}
			
			// Remove the no templates placeholder.
			panel.find( '.fl-builder-block-no-node-templates' ).remove();
		},
		
		/**
		 * Callback for when a node template drag from the 
		 * builder panel has stopped.
		 *
		 * @since 1.6.3
		 * @access private
		 * @method _nodeTemplateDragStop
		 * @param {Object} e The event object.
		 * @param {Object} ui An object with additional info for the drag.
		 */ 
		_nodeTemplateDragStop: function( e, ui )
		{
			var item   		= ui.item,
				parent 		= item.parent(),
				parentId	= null,
				position 	= 0,
				action 		= '',
				method 		= '',
				callback	= null;
			
			// Stop the drag.
			FLBuilder._blockDragStop(e, ui);
			
			// A saved row was dropped.
			if ( item.hasClass( 'fl-builder-block-saved-row' ) || item.hasClass( 'fl-builder-block-row-template' ) ) {
				position = parent.children('.fl-row, .fl-builder-block').index( item );
				parentId = null;
				action	 = 'fl_builder_save';
				method	 = 'apply_node_template';
				callback = FLBuilder._updateLayout;
			}
			// A saved module was dropped.
			else if ( item.hasClass( 'fl-builder-block-saved-module' ) || item.hasClass( 'fl-builder-block-module-template' ) ) {
				
				action	 = 'fl_builder_render_module_template_settings';
				callback = FLBuilder._addModuleComplete;
				
				// Dropped into a row position.
				if ( parent.hasClass( 'fl-builder-content' ) ) {
					position = parent.children( '.fl-row, .fl-builder-block' ).index( item );
					parentId = 0;
				}
				// Dropped into a column position.
				else if ( parent.hasClass( 'fl-row-content' ) ) {
					position = parent.children( '.fl-col-group, .fl-builder-block' ).index( item );
					parentId = item.closest( '.fl-row' ).attr( 'data-node' );
				}
				// Dropped into a column.
				else {
					position = parent.children( '.fl-module, .fl-builder-block' ).index( item );
					parentId = item.closest( '.fl-col' ).attr( 'data-node' );
				}
			}
			
			// Show the loader.
			FLBuilder.showAjaxLoader();
			
			// Apply and render the node template.
			FLBuilder.ajax({
				action	 	 : action,
				method 	     : method,
				template_id  : item.attr( 'data-id' ),
				parent_id    : parentId,
				position 	 : position
			}, callback );
			
			// Remove the helper.
			item.remove();
		},
		
		/**
		 * Launches the builder in a new tab to edit a user
		 * defined node template when the edit link is clicked.
		 *
		 * @since 1.6.3
		 * @access private
		 * @method _editUserTemplateClicked
		 * @param {Object} e The event object.
		 */
		_editNodeTemplateClicked: function( e )
		{
			e.preventDefault();
			e.stopPropagation();
			
			window.open( $( this ).attr( 'href' ) );
		},
		
		/**
		 * Fires when the delete node template icon is clicked in the builder's panel.
		 *
		 * @since 1.6.3
		 * @access private
		 * @method _deleteNodeTemplateClicked
		 * @param {Object} e The event object.
		 */
		_deleteNodeTemplateClicked: function( e )
		{
			var button 		= $( e.target ),
				section 	= button.closest( '.fl-builder-blocks-section' ),
				panel   	= section.find( '.fl-builder-blocks-section-content' ),
				blocks  	= panel.find( '.fl-builder-block' ),
				block  		= button.closest( '.fl-builder-block' ),
				global 		= block.hasClass( 'fl-builder-block-global' ),
				callback 	= global ? FLBuilder._updateLayout : undefined,
				message     = global ? FLBuilderStrings.deleteGlobalTemplate : FLBuilderStrings.deleteTemplate;
			
			if ( confirm( message ) ) {
				
				// Delete the UI block. 
				block.remove();
				
				// Add the no templates placeholder?
				if ( 1 === blocks.length ) {
					if ( block.hasClass( 'fl-builder-block-saved-row' ) ) {
						panel.append( '<span class="fl-builder-block-no-node-templates">' + FLBuilderStrings.noSavedRows + '</span>' );
					}
					else {
						panel.append( '<span class="fl-builder-block-no-node-templates">' + FLBuilderStrings.noSavedModules + '</span>' );
					}
				}
			
				// Delete the template.
				FLBuilder.ajax({
					action	 	 : 'fl_builder_save',
					method 	     : 'delete_node_template',
					template_id  : block.attr( 'data-id' ),
					silent		 : block.hasClass( 'fl-builder-block-global' ) ? false : true
				}, callback);
			}
		},

		/* Settings
		----------------------------------------------------------*/
		
		/**
		 * Initializes logic for settings forms.
		 *
		 * @since 1.0
		 * @access private
		 * @method _initSettingsForms
		 */ 
		_initSettingsForms: function()
		{
			FLBuilder._initColorPickers();
			FLBuilder._initSelectFields();
			FLBuilder._initMultipleFields();
			FLBuilder._initAutoSuggestFields();
			FLBuilder._initLinkFields();
			FLBuilder._initFontFields();
		},
		
		/**
		 * Sets the content for the settings lightbox.
		 *
		 * @since 1.0
		 * @access private
		 * @method _setSettingsFormContent
		 * @param {String} html The HTML content for the lightbox.
		 */ 
		_setSettingsFormContent: function(html)
		{
			FLBuilder._setLightboxContent(html);
			FLBuilder._initSettingsForms();
		},
		
		/**
		 * Shows the content for a settings form tab when it is clicked.
		 *
		 * @since 1.0
		 * @access private
		 * @method _settingsTabClicked
		 * @param {Object} e The event object.
		 */ 
		_settingsTabClicked: function(e)
		{
			var tab  = $(this),
				form = tab.closest('.fl-builder-settings'),
				id   = tab.attr('href').split('#').pop();
			
			form.find('.fl-builder-settings-tab').removeClass('fl-active');
			form.find('#' + id).addClass('fl-active');
			form.find('.fl-builder-settings-tabs .fl-active').removeClass('fl-active');
			$(this).addClass('fl-active');
			e.preventDefault();
		},
		
		/**
		 * Reverts an active preview and hides the lightbox when 
		 * the cancel button of a settings lightbox is clicked.
		 *
		 * @since 1.0
		 * @access private
		 * @method _settingsCancelClicked
		 * @param {Object} e The event object.
		 */ 
		_settingsCancelClicked: function(e)
		{
			var moduleSettings = $('.fl-builder-module-settings'),
				existingNodes  = null,
				previewModule  = null,
				previewCol     = null,
				existingCol    = null;
			
			if(moduleSettings.length > 0 && typeof moduleSettings.data('new-module') != 'undefined') {
			
				existingNodes = $(FLBuilder.preview.state.html);
				previewModule = $('.fl-node-' + moduleSettings.data('node'));
				previewCol    = previewModule.closest('.fl-col');
				existingCol   = existingNodes.find('.fl-node-' + previewCol.data('node'));
				
				if(existingCol.length > 0) {
					FLBuilder._deleteModule(previewModule);
				}
				else {
					FLBuilder._deleteCol(previewCol);
				}
			}
			
			FLLightbox.closeParent(this);
			
			if(FLBuilder.preview) {
				FLBuilder.preview.revert();
				FLBuilder.preview = null;
			}
		},
		
		/**
		 * Initializes validation logic for a settings form.
		 *
		 * @since 1.0
		 * @access private
		 * @method _initSettingsValidation
		 * @param {Object} rules The validation rules object.
		 * @param {Object} messages Custom messages to show for invalid fields.
		 */ 
		_initSettingsValidation: function(rules, messages)
		{
			var form = $('.fl-builder-settings').last();
			
			form.validate({
				ignore: [],
				rules: rules,
				messages: messages,
				errorPlacement: FLBuilder._settingsErrorPlacement
			});
		},
		
		/**
		 * Places a validation error after the invalid field.
		 *
		 * @since 1.0
		 * @access private
		 * @method _settingsErrorPlacement
		 * @param {Object} error The error element.
		 * @param {Object} element The invalid field.
		 */ 
		_settingsErrorPlacement: function(error, element)
		{
			error.appendTo(element.parent());
		},
		
		/**
		 * Resets all tab error icons and then shows any for tabs
		 * that have fields with errors.
		 *
		 * @since 1.0
		 * @access private
		 * @method _toggleSettingsTabErrors
		 */ 
		_toggleSettingsTabErrors: function()
		{
			var form      = $('.fl-builder-settings:visible'),
				tabs      = form.find('.fl-builder-settings-tab'),
				tab       = null,
				tabErrors = null,
				i         = 0;
			
			for( ; i < tabs.length; i++) {
				
				tab = tabs.eq(i);
				tabErrors = tab.find('label.error');
				tabLink = form.find('.fl-builder-settings-tabs a[href*='+ tab.attr('id') +']');
				tabLink.find('.fl-error-icon').remove();
				tabLink.removeClass('error');
				
				if(tabErrors.length > 0) {
					tabLink.append('<span class="fl-error-icon"></span>');
					tabLink.addClass('error');
				}
			}
		},
		
		/**
		 * Returns an object with key/value pairs for all fields
		 * within a settings form.
		 *
		 * @since 1.0
		 * @access private
		 * @method _getSettings
		 * @param {Object} form The settings form element.
		 * @return {Object} The settings object.
		 */ 
		_getSettings: function(form)
		{
			FLBuilder._updateEditorFields();
			
			var data     	= form.serializeArray(),
				i        	= 0,
				k        	= 0,
				value	 	= '',
				name     	= '',
				key      	= '',
				keys      	= [],
				matches	 	= [],
				settings 	= {};
			
			// Loop through the form data.
			for ( i = 0; i < data.length; i++ ) {
				
				value = data[ i ].value.replace( /\r/gm, '' );
				
				// Don't save text editor textareas.
				if ( data[ i ].name.indexOf( 'flrich' ) > -1 ) {
					continue;
				}
				// Support foo[]... setting keys.
				else if ( data[ i ].name.indexOf( '[' ) > -1 ) {
					
					name 	= data[ i ].name.replace( /\[(.*)\]/, '' );
					key  	= data[ i ].name.replace( name, '' );
					keys	= [];
					matches = key.match( /\[[^\]]*\]/g );
					
					// Remove [] from the keys.
					for ( k = 0; k < matches.length; k++ ) {
						
						if ( '[]' == matches[ k ] ) {
							continue;
						}
						
						keys.push( matches[ k ].replace( /\[|\]/g, '' ) );
					}
					
					// foo[][key][key]
					if ( key.match( /\[\]\[[^\]]*\]\[[^\]]+\]/ ) ) {
						
						if ( 'undefined' == typeof settings[ name ] ) {
							settings[ name ] = {};
						}
						if ( 'undefined' == typeof settings[ name ][ keys[ 0 ] ] ) {
							settings[ name ][ keys[ 0 ] ] = {};
						}
						if ( 'undefined' == typeof settings[ name ][ keys[ 0 ] ][ keys[ 1 ] ] ) {
							settings[ name ][ keys[ 0 ] ][ keys[ 1 ] ] = {};
						}
						
						settings[ name ][ keys[ 0 ] ][ keys[ 1 ] ] = value;
						
					}
					// foo[][key][]
					else if ( key.match( /\[\]\[[^\]]*\]\[\]/ ) ) {
						
						if ( 'undefined' == typeof settings[ name ] ) {
							settings[ name ] = {};
						}
						if ( 'undefined' == typeof settings[ name ][ keys[ 0 ] ] ) {
							settings[ name ][ keys[ 0 ] ] = [];
						}
						
						settings[ name ][ keys[ 0 ] ].push( value );
					}
					// foo[][key]
					else if ( key.match( /\[\]\[[^\]]*\]/ ) ) {
						
						if ( 'undefined' == typeof settings[ name ] ) {
							settings[ name ] = {};
						}
						
						settings[ name ][ keys[ 0 ] ] = value;
						
					}
					// foo[]
					else if ( key.match( /\[\]/ ) ) {
						
						if ( 'undefined' == typeof settings[ name ] ) {
							settings[ name ] = [];
						}
						
						settings[ name ].push( value );
					}
				}
				// Standard name/value pair.
				else {
					settings[ data[ i ].name ] = value;
				}
			}
			
			// Update auto suggest values.
			for ( key in settings ) {
				
				if ( 'undefined' != typeof settings[ 'as_values_' + key ] ) {
					
					settings[ key ] = $.grep( 
						settings[ 'as_values_' + key ].split( ',' ), 
						function( n ) { 
							return n != ''; 
						}
					).join( ',' );
					
					try {
						delete settings[ 'as_values_' + key ];
					}
					catch( e ) {}
				}
			}
			
			// Return the settings.
			return settings;
		},
		
		/**
		 * Saves the settings for the current settings form, shows
		 * the loader and hides the lightbox.
		 *
		 * @since 1.0
		 * @access private
		 * @method _saveSettings
		 */ 
		_saveSettings: function()
		{
			var form     = $('.fl-builder-settings-lightbox .fl-builder-settings'),
				nodeId   = form.attr('data-node'),
				settings = FLBuilder._getSettings(form);
		
			// Show the loader.
			FLBuilder.showAjaxLoader();
			
			// Make the AJAX call.
			FLBuilder.ajax({
				action          : 'fl_builder_save',
				method          : 'save_settings',
				node_id         : nodeId,
				settings        : settings
			}, FLBuilder._saveSettingsComplete);
			
			// Close the lightbox.
			FLBuilder._lightbox.close();
		},
		
		/**
		 * Renders a new layout when the settings for the current 
		 * form have finished saving. 
		 *
		 * @since 1.0
		 * @access private
		 * @method _saveSettingsComplete
		 * @param {String} response The layout data from the server.
		 */ 
		_saveSettingsComplete: function(response)
		{
			FLBuilder._renderLayout(response, function() {
				if(FLBuilder.preview) {
					FLBuilder.preview.clear();
					FLBuilder.preview = null;
				}
			});
		},
		
		/* Tooltips
		----------------------------------------------------------*/
		
		/**
		 * Shows a help tooltip in the settings lightbox.
		 *
		 * @since 1.0
		 * @access private
		 * @method _showHelpTooltip
		 */ 
		_showHelpTooltip: function()
		{
			$(this).siblings('.fl-help-tooltip-text').fadeIn();
		},
		
		/**
		 * Hides a help tooltip in the settings lightbox.
		 *
		 * @since 1.0
		 * @access private
		 * @method _hideHelpTooltip
		 */ 
		_hideHelpTooltip: function()
		{
			$(this).siblings('.fl-help-tooltip-text').fadeOut();
		},
		
		/* Auto Suggest Fields
		----------------------------------------------------------*/
		
		/**
		 * Initializes all auto suggest fields within a settings form.
		 *
		 * @since 1.2.3
		 * @access private
		 * @method _initAutoSuggestFields
		 */ 
		_initAutoSuggestFields: function()
		{
			$('.fl-suggest-field').each(FLBuilder._initAutoSuggestField);
		},
		
		/**
		 * Initializes a single auto suggest field.
		 *
		 * @since 1.2.3
		 * @access private
		 * @method _initAutoSuggestField
		 */ 
		_initAutoSuggestField: function()
		{
			var field = $(this);
				
			field.autoSuggest(FLBuilder._ajaxUrl({ 
				'fl_action'         : 'fl_builder_autosuggest',
				'fl_as_action'      : field.data('action'),
				'fl_as_action_data' : field.data('action-data')
			}), {
				asHtmlID                    : field.attr('name'),
				selectedItemProp            : 'name',
				searchObjProps              : 'name',
				minChars                    : 3,
				keyDelay                    : 1000,
				fadeOut                     : false,
				usePlaceholder              : true,
				emptyText                   : FLBuilderStrings.noResultsFound,
				showResultListWhenNoMatch   : true,
				preFill                     : field.data('value'),
				queryParam                  : 'fl_as_query',
				afterSelectionAdd           : FLBuilder._updateAutoSuggestField,
				afterSelectionRemove        : FLBuilder._updateAutoSuggestField
			});
		},
		
		/**
		 * Updates the value of an auto suggest field.
		 *
		 * @since 1.2.3
		 * @access private
		 * @method _initAutoSuggestField
		 * @param {Object} element The auto suggest field.
		 * @param {Object} item The current selection.
		 * @param {Array} selections An array of selected values.
		 */ 
		_updateAutoSuggestField: function(element, item, selections)
		{
			$(this).siblings('.as-values').val(selections.join(',')).trigger('change');
		},
		
		/* Multiple Fields
		----------------------------------------------------------*/
		
		/**
		 * Initializes all multiple fields in a settings form.
		 *
		 * @since 1.0
		 * @access private
		 * @method _initMultipleFields
		 */ 
		_initMultipleFields: function()
		{
			var multiples = $('.fl-builder-field-multiples'),
				multiple  = null,
				fields    = null,
				i         = 0,
				cursorAt  = FLBuilderConfig.isRtl ? { left: 10 } : { right: 10 };
				
			for( ; i < multiples.length; i++) {
			
				multiple = multiples.eq(i);
				fields = multiple.find('.fl-builder-field-multiple');
				
				if(fields.length === 1) {
					fields.eq(0).find('.fl-builder-field-actions').addClass('fl-builder-field-actions-single');
				}
				else {
					fields.find('.fl-builder-field-actions').removeClass('fl-builder-field-actions-single');
				}
			}
			
			$('.fl-builder-field-multiples').sortable({
				items: '.fl-builder-field-multiple',
				cursor: 'move',
				cursorAt: cursorAt,
				distance: 5,
				opacity: 0.5,
				helper: FLBuilder._fieldDragHelper,
				placeholder: 'fl-builder-field-dd-zone',
				stop: FLBuilder._fieldDragStop,
				tolerance: 'pointer'
			});
		},
		
		/**
		 * Adds a new multiple field to the list when the add
		 * button is clicked.
		 *
		 * @since 1.0
		 * @access private
		 * @method _addFieldClicked
		 */ 
		_addFieldClicked: function()
		{
			var button      = $(this),
				fieldName   = button.attr('data-field'),
				fieldRow    = button.closest('tr').siblings('tr[data-field='+ fieldName +']').last(),
				clone       = fieldRow.clone(),
				index       = parseInt(fieldRow.find('label span.fl-builder-field-index').html(), 10) + 1;
				
			clone.find('th label span.fl-builder-field-index').html(index);
			clone.find('.fl-form-field-preview-text').html('');
			clone.find('input, textarea, select').val('');
			fieldRow.after(clone);
			FLBuilder._initMultipleFields();
		},
		
		/**
		 * Copies a multiple field and adds it to the list when 
		 * the copy button is clicked.
		 *
		 * @since 1.0
		 * @access private
		 * @method _copyFieldClicked
		 */ 
		_copyFieldClicked: function()
		{
			var button      = $(this),
				row         = button.closest('tr'),
				clone       = row.clone(),
				index       = parseInt(row.find('label span.fl-builder-field-index').html(), 10) + 1;
				
			clone.find('th label span.fl-builder-field-index').html(index);
			row.after(clone);
			FLBuilder._renumberFields(row.parent());
			FLBuilder._initMultipleFields();
			FLBuilder.preview.delayPreview();
		},
		
		/**
		 * Deletes a multiple field from the list when the
		 * delete button is clicked.
		 *
		 * @since 1.0
		 * @access private
		 * @method _deleteFieldClicked
		 */ 
		_deleteFieldClicked: function()
		{
			var row     = $(this).closest('tr'),
				parent  = row.parent(),
				result  = confirm(FLBuilderStrings.deleteFieldMessage);
			
			if(result) {
				row.remove();
				FLBuilder._renumberFields(parent);
				FLBuilder._initMultipleFields();
				FLBuilder.preview.delayPreview();
			}
		},
		
		/**
		 * Renumbers the labels for a list of multiple fields.
		 *
		 * @since 1.0
		 * @access private
		 * @method _renumberFields
		 * @param {Object} table A table element with multiple fields.
		 */ 
		_renumberFields: function(table)
		{
			var rows = table.find('.fl-builder-field-multiple'),
				i    = 0;
				
			for( ; i < rows.length; i++) {
				rows.eq(i).find('th label span.fl-builder-field-index').html(i + 1);
			}
		},
		
		/**
		 * Returns an element for multiple field drag operations.
		 *
		 * @since 1.0
		 * @access private
		 * @method _fieldDragHelper
		 * @return {Object} The helper element.
		 */ 
		_fieldDragHelper: function()
		{
			return $('<div class="fl-builder-field-dd-helper"></div>');
		},
		
		/**
		 * Renumbers and triggers a preview when a multiple field
		 * has finished dragging.
		 *
		 * @since 1.0
		 * @access private
		 * @method _fieldDragStop
		 * @param {Object} e The event object.
		 * @param {Object} ui An object with additional info for the drag.
		 */ 
		_fieldDragStop: function(e, ui)
		{
			FLBuilder._renumberFields(ui.item.parent());
			
			FLBuilder.preview.delayPreview();
		},
		
		/* Select Fields
		----------------------------------------------------------*/
		
		/**
		 * Initializes select fields for a settings form.
		 *
		 * @since 1.0
		 * @access private
		 * @method _initSelectFields
		 */ 
		_initSelectFields: function()
		{
			$('.fl-builder-settings:visible').find('.fl-builder-settings-fields select').trigger('change');
		},
		
		/**
		 * Callback for when a settings form select has been changed.
		 * If toggle data is present, other fields will be toggled
		 * when this select changes.
		 *
		 * @since 1.0
		 * @access private
		 * @method _settingsSelectChanged
		 */ 
		_settingsSelectChanged: function()
		{
			var select  = $(this),
				toggle  = select.attr('data-toggle'),
				hide    = select.attr('data-hide'),
				trigger = select.attr('data-trigger'),
				val     = select.val(),
				i       = 0,
				k       = 0;
			
			// TOGGLE sections, fields or tabs.
			if(typeof toggle !== 'undefined') {
			
				toggle = JSON.parse(toggle);
				
				for(i in toggle) {
					FLBuilder._settingsSelectToggle(toggle[i].fields, 'hide', '#fl-field-');
					FLBuilder._settingsSelectToggle(toggle[i].sections, 'hide', '#fl-builder-settings-section-');
					FLBuilder._settingsSelectToggle(toggle[i].tabs, 'hide', 'a[href*=fl-builder-settings-tab-', ']');
				}
				
				if(typeof toggle[val] !== 'undefined') {
					FLBuilder._settingsSelectToggle(toggle[val].fields, 'show', '#fl-field-');
					FLBuilder._settingsSelectToggle(toggle[val].sections, 'show', '#fl-builder-settings-section-');
					FLBuilder._settingsSelectToggle(toggle[val].tabs, 'show', 'a[href*=fl-builder-settings-tab-', ']');
				}
			}
			
			// HIDE sections, fields or tabs.
			if(typeof hide !== 'undefined') {
			
				hide = JSON.parse(hide);
				
				if(typeof hide[val] !== 'undefined') {
					FLBuilder._settingsSelectToggle(hide[val].fields, 'hide', '#fl-field-');
					FLBuilder._settingsSelectToggle(hide[val].sections, 'hide', '#fl-builder-settings-section-');
					FLBuilder._settingsSelectToggle(hide[val].tabs, 'hide', 'a[href*=fl-builder-settings-tab-', ']');
				}
			}
			
			// TRIGGER select inputs.
			if(typeof trigger !== 'undefined') {
			
				trigger = JSON.parse(trigger);
				
				if(typeof trigger[val] !== 'undefined') {
					if(typeof trigger[val].fields !== 'undefined') {
						for(i = 0; i < trigger[val].fields.length; i++) {
							$('#fl-field-' + trigger[val].fields[i]).find('select').trigger('change');
						}
					}
				}
			}
		},
		
		/**
		 * @since 1.0
		 * @access private
		 * @method _settingsSelectToggle
		 * @param {Array} inputArray
		 * @param {Function} func
		 * @param {String} prefix
		 * @param {String} suffix
		 */ 
		_settingsSelectToggle: function(inputArray, func, prefix, suffix)
		{
			var i 		= 0,
				suffix 	= 'undefined' == typeof suffix ? '' : suffix;
			
			if(typeof inputArray !== 'undefined') {
				for( ; i < inputArray.length; i++) {
					$(prefix + inputArray[i] + suffix)[func]();
				}
			}
		},
		
		/* Color Pickers
		----------------------------------------------------------*/
		
		/**
		 * Initializes color picker fields for a settings form.
		 *
		 * @since 1.0
		 * @access private
		 * @method _initColorPickers
		 */ 
		_initColorPickers: function()
		{

			var colorPresets 	   = FLBuilderConfig.colorPresets ? FLBuilderConfig.colorPresets : [];
			FLBuilder.colorPicker  = new FLBuilderColorPicker({
				elements: '.fl-color-picker .fl-color-picker-value',
		    	presets: colorPresets,
				labels: {
					colorPresets 		: FLBuilderStrings.colorPresets,
					colorPicker 		: FLBuilderStrings.colorPicker,
					placeholder			: FLBuilderStrings.placeholder,
					removePresetConfirm	: FLBuilderStrings.removePresetConfirm,
					noneColorSelected	: FLBuilderStrings.noneColorSelected,
					alreadySaved		: FLBuilderStrings.alreadySaved,
					noPresets			: FLBuilderStrings.noPresets,
					presetAdded			: FLBuilderStrings.presetAdded,
				}
		    });

			$( FLBuilder.colorPicker ).on( 'presetRemoved presetAdded', function( event, data ){
	    		FLBuilder.ajax({
				    action: 'fl_builder_save',
				    method: 'save_color_presets',
				   presets: data.presets
				});

	    	});

		},
				
		/* Single Photo Fields
		----------------------------------------------------------*/
		
		/**
		 * Shows the single photo selector.
		 *
		 * @since 1.0
		 * @access private
		 * @method _selectSinglePhoto
		 */ 
		_selectSinglePhoto: function()
		{
			if(FLBuilder._singlePhotoSelector === null) {
				FLBuilder._singlePhotoSelector = wp.media({
					title: FLBuilderStrings.selectPhoto,
					button: { text: FLBuilderStrings.selectPhoto },
					library : { type : 'image' },
					multiple: false
				});
			}
			
			FLBuilder._singlePhotoSelector.once('open', $.proxy(FLBuilder._singlePhotoOpened, this));
			FLBuilder._singlePhotoSelector.once('select', $.proxy(FLBuilder._singlePhotoSelected, this));
			FLBuilder._singlePhotoSelector.open();
		},
		
		/**
		 * Callback for when the single photo selector is shown.
		 *
		 * @since 1.0
		 * @access private
		 * @method _singlePhotoOpened
		 */ 
		_singlePhotoOpened: function()
		{
			var selection   = FLBuilder._singlePhotoSelector.state().get('selection'),
				wrap        = $(this).closest('.fl-photo-field'),
				photoField  = wrap.find('input[type=hidden]'),
				photo       = photoField.val(),
				attachment  = null;
				
			if($(this).hasClass('fl-photo-replace')) {
				selection.reset();
				wrap.addClass('fl-photo-empty');
				photoField.val('');
			}
			else if(photo != '') {           
				attachment = wp.media.attachment(photo);
				attachment.fetch();
				selection.add(attachment ? [attachment] : []);
			}
			else {
				selection.reset();
			}
		},
		
		/**
		 * Callback for when a single photo is selected.
		 *
		 * @since 1.0
		 * @access private
		 * @method _singlePhotoSelected
		 */ 
		_singlePhotoSelected: function()
		{
			var photo      = FLBuilder._singlePhotoSelector.state().get('selection').first().toJSON(),
				wrap       = $(this).closest('.fl-photo-field'),
				photoField = wrap.find('input[type=hidden]'),
				preview    = wrap.find('.fl-photo-preview img'),
				srcSelect  = wrap.find('select');
				
			photoField.val(photo.id);
			preview.attr('src', FLBuilder._getPhotoSrc(photo));
			wrap.removeClass('fl-photo-empty');
			wrap.find('label.error').remove();
			srcSelect.show();
			srcSelect.html(FLBuilder._getPhotoSizeOptions(photo));
			srcSelect.trigger('change');
		},
		
		/**
		 * Returns the src URL for a photo.
		 *
		 * @since 1.0
		 * @access private
		 * @method _getPhotoSrc
		 * @param {Object} photo A photo data object.
		 * @return {String} The src URL for a photo.
		 */ 
		_getPhotoSrc: function(photo)
		{
			if(typeof photo.sizes === 'undefined') {
				return photo.url;
			}
			else if(typeof photo.sizes.thumbnail !== 'undefined') {
				return photo.sizes.thumbnail.url;
			}
			else {
				return photo.sizes.full.url;
			}
		},
		
		/**
		 * Builds the options for a photo size select.
		 *
		 * @since 1.0
		 * @access private
		 * @method _getPhotoSizeOptions
		 * @param {Object} photo A photo data object.
		 * @return {String} The HTML for the photo size options.
		 */ 
		_getPhotoSizeOptions: function(photo)
		{
			var html     = '',
				size     = null,
				selected = null,
				titles = {
					full      : FLBuilderStrings.fullSize,
					large     : FLBuilderStrings.large,
					medium    : FLBuilderStrings.medium,
					thumbnail : FLBuilderStrings.thumbnail
				};
				
			if(typeof photo.sizes === 'undefined') {
				html += '<option value="' + photo.url + '">' + FLBuilderStrings.fullSize + '</option>';
			}
			else {
				
				for(size in photo.sizes) {
					selected = size == 'full' ? ' selected="selected"' : '';
					html += '<option value="' + photo.sizes[size].url + '"' + selected + '>' + titles[size]  + ' - ' + photo.sizes[size].width + ' x ' + photo.sizes[size].height + '</option>';
				}
			}
			
			return html;
		},
		
		/* Multiple Photo Fields
		----------------------------------------------------------*/
		
		/**
		 * Shows the multiple photo selector.
		 *
		 * @since 1.0
		 * @access private
		 * @method _selectMultiplePhotos
		 */ 
		_selectMultiplePhotos: function()
		{
			var wrap           = $(this).closest('.fl-multiple-photos-field'),
				photosField    = wrap.find('input[type=hidden]'),
				photosFieldVal = photosField.val(),
				content        = photosFieldVal == '' ? '[gallery ids="-1"]' : '[gallery ids="'+ JSON.parse(photosFieldVal).join() +'"]',
				shortcode      = wp.shortcode.next('gallery', content).shortcode,
				defaultPostId  = wp.media.gallery.defaults.id,
				attachments    = null, 
				selection      = null;

			if(_.isUndefined(shortcode.get('id')) && !_.isUndefined(defaultPostId)) {
				shortcode.set('id', defaultPostId);
			}

			attachments = wp.media.gallery.attachments(shortcode);

			selection = new wp.media.model.Selection(attachments.models, {
				props: attachments.props.toJSON(),
				multiple: true
			});

			selection.gallery = attachments.gallery;

			// Fetch the query's attachments, and then break ties from the
			// query to allow for sorting.
			selection.more().done(function() {
				// Break ties with the query.
				selection.props.set({ query: false });
				selection.unmirror();
				selection.props.unset('orderby');
			});

			// Destroy the previous gallery frame.
			if(FLBuilder._multiplePhotoSelector) {
				FLBuilder._multiplePhotoSelector.dispose();
			}
			
			// Store the current gallery frame.
			FLBuilder._multiplePhotoSelector = wp.media({
				frame:     'post',
				state:     $(this).hasClass('fl-multiple-photos-edit') ? 'gallery-edit' : 'gallery-library',
				title:     wp.media.view.l10n.editGalleryTitle,
				editing:   true,
				multiple:  true,
				selection: selection
			}).open();
			
			$(FLBuilder._multiplePhotoSelector.views.view.el).addClass('fl-multiple-photos-lightbox');
			FLBuilder._multiplePhotoSelector.once('update', $.proxy(FLBuilder._multiplePhotosSelected, this));
		},
		
		/**
		 * Callback for when multiple photos have been selected.
		 *
		 * @since 1.0
		 * @access private
		 * @method _multiplePhotosSelected
		 * @param {Object} data The photo data object.
		 */ 
		_multiplePhotosSelected: function(data)
		{
			var wrap        = $(this).closest('.fl-multiple-photos-field'),
				photosField = wrap.find('input[type=hidden]'),
				count       = wrap.find('.fl-multiple-photos-count'),
				photos      = [],
				i           = 0;
			
			for( ; i < data.models.length; i++) {
				photos.push(data.models[i].id);
			}
				
			if(photos.length == 1) {
				count.html('1 ' + FLBuilderStrings.photoSelected);
			}
			else {
				count.html(photos.length + ' ' + FLBuilderStrings.photosSelected);
			}
		 
			wrap.removeClass('fl-multiple-photos-empty');
			wrap.find('label.error').remove();
			photosField.val(JSON.stringify(photos)).trigger('change');
		},
		
		/* Single Video Fields
		----------------------------------------------------------*/
		
		/**
		 * Shows the single video selector.
		 *
		 * @since 1.0
		 * @access private
		 * @method _selectSingleVideo
		 */ 
		_selectSingleVideo: function()
		{
			if(FLBuilder._singleVideoSelector === null) {
			
				FLBuilder._singleVideoSelector = wp.media({
					title: FLBuilderStrings.selectVideo,
					button: { text: FLBuilderStrings.selectVideo },
					library : { type : 'video' },
					multiple: false
				}); 
			}
			
			FLBuilder._singleVideoSelector.once('select', $.proxy(FLBuilder._singleVideoSelected, this));
			FLBuilder._singleVideoSelector.open();
		},
		
		/**
		 * Callback for when a single video is selected.
		 *
		 * @since 1.0
		 * @access private
		 * @method _singleVideoSelected
		 */ 
		_singleVideoSelected: function()
		{
			var video      = FLBuilder._singleVideoSelector.state().get('selection').first().toJSON(),
				wrap       = $(this).closest('.fl-video-field'),
				image      = wrap.find('.fl-video-preview-img img'),
				filename   = wrap.find('.fl-video-preview-filename'),
				videoField = wrap.find('input[type=hidden]');
			
			image.attr('src', video.icon);
			filename.html(video.filename);
			wrap.removeClass('fl-video-empty');
			wrap.find('label.error').remove();
			videoField.val(video.id).trigger('change');
		},
		
		/* Icon Fields
		----------------------------------------------------------*/
		
		/**
		 * Shows the icon selector.
		 *
		 * @since 1.0
		 * @access private
		 * @method _selectIcon
		 */ 
		_selectIcon: function()
		{
			var self = this;
			
			FLIconSelector.open(function(icon){
				FLBuilder._iconSelected.apply(self, [icon]);
			});
		},
		
		/**
		 * Callback for when an icon is selected.
		 *
		 * @since 1.0
		 * @access private
		 * @method _iconSelected
		 * @param {String} icon The selected icon's CSS classname.
		 */ 
		_iconSelected: function(icon)
		{
			var wrap       = $(this).closest('.fl-icon-field'),
				iconField  = wrap.find('input[type=hidden]'),
				iconTag    = wrap.find('i'),
				oldIcon    = iconTag.attr('data-icon');
				
			iconField.val(icon).trigger('change');
			iconTag.removeClass(oldIcon);
			iconTag.addClass(icon);
			iconTag.attr('data-icon', icon);
			wrap.removeClass('fl-icon-empty');
			wrap.find('label.error').remove();
		},
		
		/**
		 * Callback for when a selected icon is removed.
		 *
		 * @since 1.0
		 * @access private
		 * @method _removeIcon
		 */ 
		_removeIcon: function()
		{
			var wrap       = $(this).closest('.fl-icon-field'),
				iconField  = wrap.find('input[type=hidden]'),
				iconTag    = wrap.find('i');
				
			iconField.val('').trigger('change');
			iconTag.removeClass();
			iconTag.attr('data-icon', '');
			wrap.addClass('fl-icon-empty');
		},
		
		/* Settings Form Fields
		----------------------------------------------------------*/

		/**
		 * Shows the settings for a nested form field when the
		 * edit link is clicked.
		 *
		 * @since 1.0
		 * @access private
		 * @method _formFieldClicked
		 */  
		_formFieldClicked: function()
		{
			var link                = $(this),
				linkLightboxId      = link.closest('.fl-lightbox-wrap').attr('data-instance-id'),
				linkLightbox        = FLLightbox._instances[linkLightboxId],
				linkLightboxLeft    = linkLightbox._node.find('.fl-lightbox').css('left'),
				linkLightboxTop     = linkLightbox._node.find('.fl-lightbox').css('top'),
				form                = link.closest('.fl-builder-settings'),
				type                = link.attr('data-type'),
				settings            = link.siblings('input').val(),
				helper              = FLBuilder._moduleHelpers[type],
				lightbox            = new FLLightbox({
										  className: 'fl-builder-lightbox fl-form-field-settings',
										  destroyOnClose: true
									  });

			link.closest('.fl-builder-lightbox').hide();
			link.attr('id', 'fl-' + lightbox._id);
			lightbox.open('<div class="fl-builder-lightbox-loading"></div>');
			lightbox.draggable({ handle: '.fl-lightbox-header' });
			$('body').undelegate('.fl-builder-settings-cancel', 'click', FLBuilder._settingsCancelClicked);
			
			lightbox._node.find('.fl-lightbox').css({
				'left': linkLightboxLeft,
				'top': Number(parseInt(linkLightboxTop) + 233) + 'px'
			});
			
			FLBuilder.ajax({
				action: 'fl_builder_render_settings_form',
				type: type,
				settings: settings.replace(/&#39;/g, "'")
			}, 
			function(response) 
			{
				lightbox.setContent(response); 
				lightbox._node.find('form.fl-builder-settings').attr('data-type', type); 
				lightbox._node.find('.fl-builder-settings-cancel').on('click', FLBuilder._closeFormFieldLightbox);
				FLBuilder._initSettingsForms();
				
				if(typeof helper !== 'undefined') {
					FLBuilder._initSettingsValidation(helper.rules);
					helper.init();
				}
				
				lightbox._node.find('.fl-lightbox').css({
					'left': linkLightboxLeft,
					'top': linkLightboxTop
				});
			});
		},
		
		/**
		 * Closes the settings lightbox for a nested form field when the
		 * cancel or save button is clicked.
		 *
		 * @since 1.0
		 * @access private
		 * @method _closeFormFieldLightbox
		 */ 
		_closeFormFieldLightbox: function()
		{
			var instanceId          = $(this).closest('.fl-lightbox-wrap').attr('data-instance-id'),
				lightbox            = FLLightbox._instances[instanceId],
				linkLightbox        = $('.fl-builder-settings-lightbox'),
				linkLightboxForm    = linkLightbox.find('form'),
				linkLightboxLeft    = lightbox._node.find('.fl-lightbox').css('left');
				linkLightboxTop     = lightbox._node.find('.fl-lightbox').css('top');
				boxHeight           = 0,
				win                 = $(window),
				winHeight           = win.height();
			
			lightbox._node.find('.fl-lightbox-content').html('<div class="fl-builder-lightbox-loading"></div>');
			boxHeight = lightbox._node.find('.fl-lightbox').height();
			
			if(winHeight - 80 > boxHeight) {
				lightbox._node.find('.fl-lightbox').css('top', ((winHeight - boxHeight)/2 - 40) + 'px');
			}
			else {
				lightbox._node.find('.fl-lightbox').css('top', '0px');
			}
			
			lightbox.on('close', function() 
			{
				linkLightbox.show();
				linkLightbox.find('label.error').remove();
				linkLightboxForm.validate().hideErrors();
				FLBuilder._toggleSettingsTabErrors();
				
				linkLightbox.find('.fl-lightbox').css({
					'left': linkLightboxLeft,
					'top': linkLightboxTop
				});
			});
			
			setTimeout(function()
			{
				lightbox.close();
				$('body').delegate('.fl-builder-settings-cancel', 'click', FLBuilder._settingsCancelClicked);
			}, 500);
		},
		
		/**
		 * Saves the settings for a nested form field when the
		 * save button is clicked.
		 *
		 * @since 1.0
		 * @access private
		 * @method _saveFormFieldClicked
		 * @return {Boolean} Whether the save was successful or not.
		 */  
		_saveFormFieldClicked: function()
		{
			var form          = $(this).closest('.fl-builder-settings'),
				lightboxId    = $(this).closest('.fl-lightbox-wrap').attr('data-instance-id'),
				type          = form.attr('data-type'),
				settings      = FLBuilder._getSettings(form),
				helper        = FLBuilder._moduleHelpers[type],
				link          = $('.fl-builder-settings #fl-' + lightboxId),
				preview       = link.parent().attr('data-preview-text'),
				previewText   = settings[preview],
				selectPreview = $( 'select[name="' + preview + '"]' ),
				tmp           = document.createElement('div'),
				valid         = true;
				
			if ( selectPreview.length > 0 ) {
				previewText = selectPreview.find( 'option[value="' + settings[ preview ] + '"]' ).text();
			}  
			if(typeof helper !== 'undefined') {
				
				form.find('label.error').remove();
				form.validate().hideErrors();
				valid = form.validate().form();
				
				if(valid) {
					valid = helper.submit();
				}
			}
			if(valid) {
			
				if(typeof preview !== 'undefined') {
				
					if(previewText.indexOf('fa fa-') > -1) {
						previewText = '<i class="' + previewText + '"></i>';
					}
					else if(previewText.length > 35) {
						tmp.innerHTML = previewText;
						previewText = (tmp.textContent || tmp.innerText || '').replace(/^(.{35}[^\s]*).*/, "$1")  + '...';
					}
				
					link.siblings('.fl-form-field-preview-text').html(previewText);
				}
				
				link.siblings('input').val(JSON.stringify(settings)).trigger('change');
				
				FLBuilder._closeFormFieldLightbox.apply(this);
				
				return true;
			}
			else {
				FLBuilder._toggleSettingsTabErrors();
				return false;
			}
		},
		
		/* Layout Fields
		----------------------------------------------------------*/

		/**
		 * Callback for when the item of a layout field is clicked.
		 *
		 * @since 1.0
		 * @access private
		 * @method _layoutFieldClicked
		 */ 
		_layoutFieldClicked: function()
		{
			var option = $(this);
			
			option.siblings().removeClass('fl-layout-field-option-selected');
			option.addClass('fl-layout-field-option-selected');
			option.siblings('input').val(option.attr('data-value'));
		},
		
		/* Link Fields
		----------------------------------------------------------*/
		
		/**
		 * Initializes all link fields in a settings form.
		 *
		 * @since 1.3.9
		 * @access private
		 * @method _initLinkFields
		 */ 
		_initLinkFields: function()
		{
			$('.fl-link-field').each(FLBuilder._initLinkField);
		},
		
		/**
		 * Initializes a single link field in a settings form.
		 *
		 * @since 1.3.9
		 * @access private
		 * @method _initLinkFields
		 */ 
		_initLinkField: function()
		{
			var wrap        = $(this),
				searchInput = wrap.find('.fl-link-field-search-input');
				
			searchInput.autoSuggest(FLBuilder._ajaxUrl({ 
				'fl_action'         : 'fl_builder_autosuggest',
				'fl_as_action'      : 'fl_as_links'
			}), {
				asHtmlID                    : searchInput.attr('name'),
				selectedItemProp            : 'name',
				searchObjProps              : 'name',
				minChars                    : 3,
				keyDelay                    : 1000,
				fadeOut                     : false,
				usePlaceholder              : true,
				emptyText                   : FLBuilderStrings.noResultsFound,
				showResultListWhenNoMatch   : true,
				queryParam                  : 'fl_as_query',
				selectionLimit              : 1,
				afterSelectionAdd           : FLBuilder._updateLinkField
			});
		},
		
		/**
		 * Updates the value of a link field when a link has been 
		 * selected from the auto suggest menu.
		 *
		 * @since 1.3.9
		 * @access private
		 * @method _updateLinkField
		 * @param {Object} element The auto suggest field.
		 * @param {Object} item The current selection.
		 * @param {Array} selections An array of selected values.
		 */ 
		_updateLinkField: function(element, item, selections)
		{
			var wrap        = element.closest('.fl-link-field'),
				search      = wrap.find('.fl-link-field-search'),
				searchInput = wrap.find('.fl-link-field-search-input'),
				field       = wrap.find('.fl-link-field-input');
			
			field.val(item.value).trigger('keyup');
			searchInput.autoSuggest('remove', item.value);
			search.hide();
		},

		/**
		 * Shows the auto suggest input for a link field.
		 *
		 * @since 1.3.9
		 * @access private
		 * @method _linkFieldSelectClicked
		 */ 
		_linkFieldSelectClicked: function()
		{
			$(this).parent().find('.fl-link-field-search').show();
		},

		/**
		 * Hides the auto suggest input for a link field.
		 *
		 * @since 1.3.9
		 * @access private
		 * @method _linkFieldSelectCancelClicked
		 */ 
		_linkFieldSelectCancelClicked: function()
		{
			$(this).parent().hide();
		},

		/* Font Fields
		----------------------------------------------------------*/
		
		/**
		 * Initializes all font fields in a settings form.
		 *
		 * @since  1.6.3
		 * @access private
		 * @method _initFontFields
		 */ 
		_initFontFields: function(){
			$('.fl-font-field').each( FLBuilder._initFontField );
		},

		/**
		 * Initializes a single font field in a settings form.
		 *
		 * @since  1.6.3
		 * @access private
		 * @method _initFontFields
		 */ 
		_initFontField: function(){
			var wrap   = $(this),
				font   = wrap.find( '.fl-font-field-font' );
	
			font.on( 'change', function(){
				FLBuilder._getFontWeights( font );
			} );

		},

		/**
		 * Renders the correct weights list for a respective font.
		 *
		 * @since  1.6.3
		 * @acces  private
		 * @method _getFontWeights
		 * @param  {Object} currentFont The font field element.
		 */
		_getFontWeights: function( currentFont ){
			var selectWeight = currentFont.next( '.fl-font-field-weight' ),
				font         = currentFont.val(),
				weightMap    = {
					'default' : 'Default',
					'100': 'Thin 100',
					'200': 'Extra-Light 200',
					'300': 'Light 300',
					'400': 'Normal 400',
					'500': 'Medium 500',
					'600': 'Semi-Bold 600',
					'700': 'Bold 700',
					'800': 'Extra-Bold 800',
					'900': 'Ultra-Bold 900'
				},
				weights      = {};

				selectWeight.html('');

				if ( 'undefined' != typeof FLBuilderFontFamilies.system[ font ] ) {
					weights = FLBuilderFontFamilies.system[ font ].weights;
				}
				else if ( 'undefined' != typeof FLBuilderFontFamilies.google[ font ] ) {
					weights = FLBuilderFontFamilies.google[ font ];
				} else {
					weights = FLBuilderFontFamilies.default[ font ];
				}

			$.each( weights, function( key, value ){
				selectWeight.append( '<option value="' + value + '">' + weightMap[ value ] + '</option>' );
			} );

		},
		
		/* Editor Fields
		----------------------------------------------------------*/
		
		/**
		 * Used to init pre WP 3.9 editors from field.php.
		 *
		 * @since 1.0
		 * @method initEditorField
		 */  
		initEditorField: function(id)
		{
			var newEditor = tinyMCEPreInit.mceInit['flhiddeneditor'];
			
			newEditor['elements'] = id;
			tinyMCEPreInit.mceInit[id] = newEditor;
		},

		/**
		 * Updates all editor fields within a settings form.
		 *
		 * @since 1.0
		 * @access private
		 * @method _updateEditorFields
		 */  
		_updateEditorFields: function()
		{
			var wpEditors = $('.fl-builder-settings textarea.wp-editor-area');
			
			wpEditors.each(FLBuilder._updateEditorField);
		},

		/**
		 * Updates a single editor field within a settings form. 
		 * Creates a hidden textarea with the editor content so 
		 * this field can be saved.
		 *
		 * @since 1.0
		 * @access private
		 * @method _updateEditorField
		 */  
		_updateEditorField: function()
		{
			var textarea  = $( this ),
				wrap      = textarea.closest( '.wp-editor-wrap' ),
				id        = textarea.attr( 'id' ),
				setting   = textarea.closest( '.fl-editor-field' ).attr( 'id' ),
				editor    = typeof tinyMCE == 'undefined' ? false : tinyMCE.get( id ),
				hidden    = textarea.siblings( 'textarea[name="' + setting + '"]' );
			
			// Add a hidden textarea if we don't have one.
			if ( 0 === hidden.length ) {
				hidden = $( '<textarea name="' + setting + '"></textarea>' ).hide();
				textarea.after( hidden );
			}
			
			// Update the hidden textarea content.
			if ( editor && wrap.hasClass( 'tmce-active' ) ) {
				hidden.val( editor.getContent() );
			}
			else if ( 'undefined' != typeof switchEditors ) {
				hidden.val( switchEditors.wpautop( textarea.val() ) );
			}
			else {
				hidden.val( textarea.val() );
			}
		},
		
		/* Loop Builder Fields
		----------------------------------------------------------*/

		/**
		 * Callback for when the post type of a loop builder changes.
		 *
		 * @since 1.2.3
		 * @access private
		 * @method _loopBuilderPostTypeChange
		 */ 
		_loopBuilderPostTypeChange: function()
		{
			var val = $(this).val();
			
			$('.fl-loop-builder-filter').hide();
			$('.fl-loop-builder-' + val + '-filter').show();
		},
		
		/* AJAX
		----------------------------------------------------------*/

		/**
		 * Frontend AJAX for the builder interface.
		 *
		 * @since 1.0
		 * @method ajax
		 * @param {Object} data The data for the AJAX request.
		 * @param {Function} callback A function to call when the request completes.
		 */   
		ajax: function(data, callback)
		{
			var key;
			
			// Show the loader and save the data for
			// later if a silent update is running.
			if(FLBuilder._silentUpdate) {
				FLBuilder.showAjaxLoader();
				FLBuilder._silentUpdateCallbackData = [data, callback];
				return;
			}
			
			// This request is silent, set the flag to true
			// so we know incase another ajax request is made
			// before this one finishes.
			else if(data.silent === true) {
				FLBuilder._silentUpdate = true;
			}
			
			// Send the post id to the server. 
			data.post_id = $('#fl-post-id').val();
			
			// Tell the server that the builder is active.
			data.fl_builder = 1;
			
			// Append the builder namespace to the action.
			data.fl_action = data.action;
			
			// Store the data in a single variable to avoid conflicts.
			data = { fl_builder_data: data };
			
			// Do the ajax call.
			return $.post(FLBuilder._ajaxUrl(), data, function(response) {

				FLBuilder._ajaxComplete();
			
				if(typeof callback !== 'undefined') {
					callback.call(this, response);
				}
			});
		},

		/**
		 * Callback for when an AJAX request is complete. Runs a
		 * queued AJAX request if a silent update was in progress 
		 * when the last request was made.
		 *
		 * @since 1.0
		 * @access private
		 * @method _ajaxComplete
		 */   
		_ajaxComplete: function()
		{
			var data, callback;
			
			// Set the silent update flag to false
			// so other ajax requests can run.
			FLBuilder._silentUpdate = false;
			
			// Do an ajax request that was stopped 
			// by a silent ajax request.
			if(FLBuilder._silentUpdateCallbackData !== null) {
				FLBuilder.showAjaxLoader();
				data = FLBuilder._silentUpdateCallbackData[0];
				callback = FLBuilder._silentUpdateCallbackData[1];
				FLBuilder._silentUpdateCallbackData = null;
				FLBuilder.ajax(data, callback);
			}
			
			// We're done, hide the loader incase it's showing.
			else {
				FLBuilder.hideAjaxLoader();
			}
		},

		/**
		 * Returns a URL for an AJAX request.
		 *
		 * @since 1.0
		 * @access private
		 * @method _ajaxUrl
		 * @param {Object} params An object with key/value pairs for the AJAX query string.
		 * @return {String} The AJAX URL. 
		 */   
		_ajaxUrl: function(params)
		{
			var url     = window.location.href.split( '#' ).shift(),
				param   = null;
			
			if(typeof params !== 'undefined') {
			
				for(param in params) {
					url += url.indexOf('?') > -1 ? '&' : '?';
					url += param + '=' + params[param];
				}
			}
		
			return url;
		},

		/**
		 * Shows the AJAX loading overlay.
		 *
		 * @since 1.0
		 * @method showAjaxLoader
		 */   
		showAjaxLoader: function()
		{
			if( 0 === $( '.fl-builder-lightbox-loading' ).length ) {
				$( '.fl-builder-loading' ).show();
			}
		},

		/**
		 * Hides the AJAX loading overlay.
		 *
		 * @since 1.0
		 * @method hideAjaxLoader
		 */   
		hideAjaxLoader: function()
		{
			$( '.fl-builder-loading' ).hide();
		},
		
		/* Lightboxes
		----------------------------------------------------------*/
		
		/**
		 * Shows the settings lightbox.
		 *
		 * @since 1.0
		 * @access private
		 * @method _showLightbox
		 * @param {Boolean} draggable Whether the lightbox should be draggable or not.
		 */  
		_showLightbox: function(draggable)
		{
			draggable = typeof draggable === 'undefined' ? true : draggable;
			
			FLBuilder._lightbox.open('<div class="fl-builder-lightbox-loading"></div>');
			
			if(draggable) {
				FLBuilder._lightbox.draggable({
					handle: '.fl-lightbox-header'
				});
			}
			else {
				FLBuilder._lightbox.draggable(false);
			}
			
			FLBuilder._removeAllOverlays();
			FLBuilder._initLightboxScrollbars();
		},
		
		/**
		 * Set the content for the settings lightbox.
		 *
		 * @since 1.0
		 * @access private
		 * @method _setLightboxContent
		 * @param {String} content The HTML content for the lightbox.
		 */  
		_setLightboxContent: function(content)
		{
			FLBuilder._lightbox.setContent(content);
		},
		
		/**
		 * Initializes the scrollbars for the settings lightbox.
		 *
		 * @since 1.0
		 * @access private
		 * @method _initLightboxScrollbars
		 */  
		_initLightboxScrollbars: function()
		{
			FLBuilder._initScrollbars();
			FLBuilder._lightboxScrollbarTimeout = setTimeout(FLBuilder._initLightboxScrollbars, 500);
		},
		
		/**
		 * Callback to clean things up when the settings lightbox
		 * is closed.
		 *
		 * @since 1.0
		 * @access private
		 * @method _lightboxClosed
		 */  
		_lightboxClosed: function()
		{
			FLBuilder._lightbox.empty();
			clearTimeout(FLBuilder._lightboxScrollbarTimeout);
		},
		
		/**
		 * Shows the actions lightbox.
		 *
		 * @since 1.0
		 * @access private
		 * @method _showActionsLightbox
		 * @param {Object} settings An object with settings for the lightbox buttons.
		 */
		_showActionsLightbox: function(settings)
		{
			var template = wp.template( 'fl-actions-lightbox' );

			FLBuilder._actionsLightbox.open( template( settings ) );
		},
		
		/* Alert Lightboxes
		----------------------------------------------------------*/
		
		/**
		 * Shows the alert lightbox with a message.
		 *
		 * @since 1.0
		 * @method alert
		 * @param {String} message The message to show.
		 */
		alert: function(message)
		{
			var alert = new FLLightbox({
					className: 'fl-builder-lightbox fl-builder-alert-lightbox',
					destroyOnClose: true
				}),
				template = wp.template( 'fl-alert-lightbox' );
			
			alert.open( template( { message : message } ) );
		},
		
		/**
		 * Closes the alert lightbox when a child element is clicked.
		 *
		 * @since 1.0
		 * @access private
		 * @method _alertClose
		 */
		_alertClose: function()
		{
			FLLightbox.closeParent(this);
		},
		
		/* Console Logging
		----------------------------------------------------------*/
		
		/**
		 * Logs a message in the console if the console is available.
		 *
		 * @since 1.4.6
		 * @method log
		 * @param {String} message The message to log.
		 */
		log: function( message )
		{
			if ( 'undefined' == typeof window.console || 'undefined' == typeof window.console.log ) {
				return;
			}
			
			console.log( message );
		},
		
		/**
		 * Logs an error in the console if the console is available.
		 *
		 * @since 1.4.6
		 * @method logError
		 * @param {String} error The error to log.
		 */
		logError: function( error )
		{
			var message = null;
			
			if ( 'undefined' == typeof error ) {
				return;
			}
			else if ( 'undefined' != typeof error.stack ) {
				message = error.stack;
			}
			else if ( 'undefined' != typeof error.message ) {
				message = error.message;
			}
			
			if ( message ) {
				FLBuilder.log( '************************************************************************' );
				FLBuilder.log( FLBuilderStrings.errorMessage );
				FLBuilder.log( message );
				FLBuilder.log( '************************************************************************' );
			}
		},
		
		/**
		 * Logs a global error in the console if the console is available.
		 *
		 * @since 1.4.6
		 * @method logGlobalError
		 * @param {String} message
		 * @param {String} file
		 * @param {String} line
		 * @param {String} col
		 * @param {String} error
		 */
		logGlobalError: function( message, file, line, col, error )
		{
			FLBuilder.log( '************************************************************************' );
			FLBuilder.log( FLBuilderStrings.errorMessage );
			FLBuilder.log( FLBuilderStrings.globalErrorMessage.replace( '{message}', message ).replace( '{line}', line ).replace( '{file}', file ) );
			
			if ( 'undefined' != typeof error && 'undefined' != typeof error.stack ) {
				FLBuilder.log( error.stack );
				FLBuilder.log( '************************************************************************' );
			}
		}
	};

	/* Start the party!!! */
	$(function(){
		FLBuilder._init();
	});

})(jQuery);