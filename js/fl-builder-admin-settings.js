(function($){

	/**
	 * Helper class for dealing with the builder's admin
	 * settings page.
	 *
	 * @class FLBuilderAdminSettings
	 * @since 1.0
	 */
	FLBuilderAdminSettings = {
		
		/**
		 * An instance of wp.media used for uploading icons.
		 *
		 * @since 1.4.6
		 * @access private
		 * @property {Object} _iconUploader
		 */
		_iconUploader: null,
	
		/**
		 * Initializes the builder's admin settings page.
		 *
		 * @since 1.0
		 * @method init
		 */ 
		init: function()
		{
			this._bind();
			this._initNav();
			this._initOverrides();
			this._initHelpButtonSettings();
		},
		
		/**
		 * Binds events for the builder's admin settings page.
		 *
		 * @since 1.0
		 * @access private
		 * @method _bind
		 */
		_bind: function()
		{
			$('.fl-settings-nav a').on('click', FLBuilderAdminSettings._navClicked);
			$('.fl-module-all-cb').on('click', FLBuilderAdminSettings._moduleAllCheckboxClicked);
			$('.fl-module-cb').on('click', FLBuilderAdminSettings._moduleCheckboxClicked);
			$('.fl-override-ms-cb').on('click', FLBuilderAdminSettings._overrideCheckboxClicked);
			$('input[name=fl-upload-icon]').on('click', FLBuilderAdminSettings._showIconUploader);
			$('.fl-delete-icon-set').on('click', FLBuilderAdminSettings._deleteCustomIconSet);
			$('#uninstall-form').on('submit', FLBuilderAdminSettings._uninstallFormSubmit);
			$('input[name=fl-help-button-enabled]').on('click', FLBuilderAdminSettings._initHelpButtonSettings);
			$('input[name=fl-help-video-enabled]').on('click', FLBuilderAdminSettings._initHelpButtonSettings);
			$('input[name=fl-knowledge-base-enabled]').on('click', FLBuilderAdminSettings._initHelpButtonSettings);
			$('input[name=fl-forums-enabled]').on('click', FLBuilderAdminSettings._initHelpButtonSettings);
		},
		
		/**
		 * Initializes the nav for the builder's admin settings page.
		 *
		 * @since 1.0
		 * @access private
		 * @method _initNav
		 */
		_initNav: function()
		{
			var links  = $('.fl-settings-nav a'),
				hash   = window.location.hash,
				active = hash == '' ? [] : links.filter('[href~='+ hash +']');
				
			$('a.fl-active').removeClass('fl-active');
			$('.fl-settings-form').hide();
				
			if(hash == '' || active.length === 0) {
				active = links.eq(0);
			}
			
			active.addClass('fl-active');
			$('#fl-'+ active.attr('href').split('#').pop() +'-form').fadeIn();
		},
		
		/**
		 * Fires when a nav item is clicked.
		 *
		 * @since 1.0
		 * @access private
		 * @method _navClicked
		 */
		_navClicked: function()
		{
			if($(this).attr('href').indexOf('#') > -1) {
				$('a.fl-active').removeClass('fl-active');
				$('.fl-settings-form').hide();
				$(this).addClass('fl-active');
				$('#fl-'+ $(this).attr('href').split('#').pop() +'-form').fadeIn();
			}
		},
		
		/**
		 * Fires when the "all" checkbox in the list of enabled
		 * modules is clicked.
		 *
		 * @since 1.0
		 * @access private
		 * @method _moduleAllCheckboxClicked
		 */
		_moduleAllCheckboxClicked: function()
		{
			if($(this).is(':checked')) {
				$('.fl-module-cb').prop('checked', true);
			}
		},
		
		/**
		 * Fires when a checkbox in the list of enabled
		 * modules is clicked.
		 *
		 * @since 1.0
		 * @access private
		 * @method _moduleCheckboxClicked
		 */
		_moduleCheckboxClicked: function()
		{
			var allChecked = true;
					
			$('.fl-module-cb').each(function() {
				
				if(!$(this).is(':checked')) {
					allChecked = false;
				}
			});
			
			if(allChecked) {
				$('.fl-module-all-cb').prop('checked', true);
			}
			else {
				$('.fl-module-all-cb').prop('checked', false);
			}
		},
		
		/**
		 * Initializes the checkboxes for overriding network settings.
		 *
		 * @since 1.0
		 * @access private
		 * @method _initOverrides
		 */
		_initOverrides: function()
		{
			$('.fl-override-ms-cb').each(FLBuilderAdminSettings._initOverride);
		},
		
		/**
		 * Initializes a checkbox for overriding network settings.
		 *
		 * @since 1.0
		 * @access private
		 * @method _initOverride
		 */
		_initOverride: function()
		{
			var cb      = $(this),
				content = cb.closest('.fl-settings-form').find('.fl-settings-form-content');
				
			if(this.checked) {
				content.show();
			}
			else {
				content.hide();
			}
		},
		
		/**
		 * Fired when a network override checkbox is clicked.
		 *
		 * @since 1.0
		 * @access private
		 * @method _overrideCheckboxClicked
		 */
		_overrideCheckboxClicked: function()
		{
			var cb      = $(this),
				content = cb.closest('.fl-settings-form').find('.fl-settings-form-content');
				
			if(this.checked) {
				content.show();
			}
			else {
				content.hide();
			}
		},
		
		/**
		 * Initializes the the help button settings.
		 *
		 * @since 1.4.9
		 * @access private
		 * @method _initHelpButtonSettings
		 */
		_initHelpButtonSettings: function()
		{
			if ( 0 === $( '#fl-help-button-form' ).length ) {
				return;
			}
			
			var enabled  = $( 'input[name=fl-help-button-enabled]' )[ 0 ].checked,
				tour     = $('input[name=fl-help-tour-enabled]')[ 0 ].checked,
				video    = $('input[name=fl-help-video-enabled]')[ 0 ].checked,
				kb       = $('input[name=fl-knowledge-base-enabled]')[ 0 ].checked,
				forums   = $('input[name=fl-forums-enabled]')[ 0 ].checked;
			
			$( '.fl-help-button-settings' ).toggle( enabled );
			$( '.fl-help-video-embed' ).toggle( video );
			$( '.fl-knowledge-base-url' ).toggle( kb );
			$( '.fl-forums-url' ).toggle( forums );
		},
		
		/**
		 * Shows the media library lightbox for uploading icons.
		 *
		 * @since 1.4.6
		 * @access private
		 * @method _showIconUploader
		 */
		_showIconUploader: function()
		{
			if(FLBuilderAdminSettings._iconUploader === null) {
				FLBuilderAdminSettings._iconUploader = wp.media({
					title: FLBuilderAdminSettingsStrings.selectFile,
					button: { text: FLBuilderAdminSettingsStrings.selectFile },
					library : { type : 'application/zip' },
					multiple: false
				});
			}
			
			FLBuilderAdminSettings._iconUploader.once('select', $.proxy(FLBuilderAdminSettings._iconFileSelected, this));
			FLBuilderAdminSettings._iconUploader.open();
		},
		
		/**
		 * Callback for when an icon set file is selected.
		 *
		 * @since 1.4.6
		 * @access private
		 * @method _iconFileSelected
		 */
		_iconFileSelected: function()
		{
			var file = FLBuilderAdminSettings._iconUploader.state().get('selection').first().toJSON();
			
			$( 'input[name=fl-new-icon-set]' ).val( file.id );
			$( '#icons-form' ).submit();
		},
		
		/**
		 * Fires when the delete link for an icon set is clicked.
		 *
		 * @since 1.4.6
		 * @access private
		 * @method _deleteCustomIconSet
		 */
		_deleteCustomIconSet: function()
		{
			var set = $( this ).data( 'set' );
			
			$( 'input[name=fl-delete-icon-set]' ).val( set );
			$( '#icons-form' ).submit();
		},
		
		/**
		 * Fires when the uninstall button is clicked.
		 *
		 * @since 1.0
		 * @access private
		 * @method _uninstallFormSubmit
		 * @return {Boolean}
		 */
		_uninstallFormSubmit: function()
		{
			var result = prompt(FLBuilderAdminSettingsStrings.uninstall, '');
			
			if(result == 'uninstall') {
				return true;
			}
			
			return false;
		}
	};

	/* Initializes the builder's admin settings. */
	$(function(){
		FLBuilderAdminSettings.init();
	});

})(jQuery);