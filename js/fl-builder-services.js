(function( $ ) {
	
	/**
	 * JavaScript class for working with third party services.
	 *
	 * @since 1.5.4
	 */
	var FLBuilderServices = {
		
		/**
		 * Initializes the services logic.
		 *
		 * @return void
		 * @since 1.5.4
		 */
		init: function()
		{
			var body = $('body');
			
			// Standard Events
			body.delegate( '.fl-builder-service-select', 'change', this._serviceChange );
			body.delegate( '.fl-builder-service-connect-button', 'click', this._connectClicked );
			body.delegate( '.fl-builder-service-account-select', 'change', this._accountChange );
			body.delegate( '.fl-builder-service-account-delete', 'click', this._accountDeleteClicked );
			
			// Campaign Monitor Events
			body.delegate( '.fl-builder-campaign-monitor-client-select', 'change', this._campaignMonitorClientChange );
			
			// MailChimp Events
			body.delegate( '.fl-builder-mailchimp-list-select', 'change', this._mailChimpListChange );
		},
		
		/**
		 * Show the lightbox loading graphic and remove errors.
		 *
		 * @param {Object} ele An element within the lightbox.
		 * @return void
		 * @since 1.5.4
		 */
		_startSettingsLoading: function( ele )
		{
			var lightbox    = $( '.fl-builder-settings' ),
				wrap        = ele.closest( '.fl-builder-service-settings' ),
				error       = $( '.fl-builder-service-error' );
			
			lightbox.append( '<div class="fl-builder-loading"></div>' );
			wrap.addClass( 'fl-builder-service-settings-loading' );
			error.remove();
		},
		
		/**
		 * Remove the lightbox loading graphic.
		 *
		 * @return void
		 * @since 1.5.4
		 */
		_finishSettingsLoading: function()
		{
			var lightbox    = $( '.fl-builder-settings' ),
				wrap        = $( '.fl-builder-service-settings-loading' );
			
			lightbox.find( '.fl-builder-loading' ).remove();
			wrap.removeClass( 'fl-builder-service-settings-loading' );
		},
		
		/**
		 * Fires when the service select changes.
		 *
		 * @return void
		 * @since 1.5.4
		 */
		_serviceChange: function()
		{
			var nodeId      = $( '.fl-builder-settings' ).data( 'node' ),
				select      = $( this ),
				selectRow   = select.closest( 'tr' ),
				service     = select.val();
			
			selectRow.siblings( 'tr.fl-builder-service-account-row' ).remove();
			selectRow.siblings( 'tr.fl-builder-service-connect-row' ).remove();
			selectRow.siblings( 'tr.fl-builder-service-field-row' ).remove();
			$( '.fl-builder-service-error' ).remove();
				
			if ( '' == service ) {
				return;
			}
			
			FLBuilderServices._startSettingsLoading( select );
			
			FLBuilder.ajax( {
				action  : 'fl_builder_render_service_settings',
				node_id : nodeId,
				service : service
			}, FLBuilderServices._serviceChangeComplete );
		},
		
		/**
		 * AJAX callback for when the service select changes.
		 *
		 * @param {String} response The JSON response.
		 * @return void
		 * @since 1.5.4
		 */
		_serviceChangeComplete: function( response )
		{
			var data        = JSON.parse( response ),
				wrap        = $( '.fl-builder-service-settings-loading' ),
				selectRow   = wrap.find( '.fl-builder-service-select-row' );
				
			selectRow.after( data.html );
			FLBuilderServices._addAccountDelete( wrap );
			FLBuilderServices._finishSettingsLoading();
		},
		
		/**
		 * Fires when the service connect button is clicked.
		 *
		 * @return void
		 * @since 1.5.4
		 */
		_connectClicked: function()
		{
			var nodeId          = $( '.fl-builder-settings' ).data( 'node' ),
				wrap            = $( this ).closest( '.fl-builder-service-settings' ),
				select          = wrap.find( '.fl-builder-service-select' ),
				connectRows     = wrap.find( '.fl-builder-service-connect-row' ),
				connectInputs   = wrap.find( '.fl-builder-service-connect-input' ),
				input           = null,
				name            = null,
				i               = 0,
				data            = {
					action          : 'fl_builder_connect_service',
					node_id         : nodeId,
					service         : select.val(),
					fields          : {}
				};
			
			for ( ; i < connectInputs.length; i++ ) {
				input                   = connectInputs.eq( i );
				name                    = input.attr( 'name' );
				data['fields'][ name ]  = input.val();
			}
			
			connectRows.hide();
			FLBuilderServices._startSettingsLoading( select );
			FLBuilder.ajax( data, FLBuilderServices._connectComplete );
		},
		
		/**
		 * AJAX callback for when the service connect button is clicked.
		 *
		 * @param {String} response The JSON response.
		 * @return void
		 * @since 1.5.4
		 */
		_connectComplete: function( response )
		{
			var data        = JSON.parse( response ),
				wrap        = $( '.fl-builder-service-settings-loading' ),
				selectRow   = wrap.find( '.fl-builder-service-select-row' ),
				select      = wrap.find( '.fl-builder-service-select' ),
				accountRow  = wrap.find( '.fl-builder-service-account-row' ),
				account     = wrap.find( '.fl-builder-service-account-select' ),
				connectRows = wrap.find( '.fl-builder-service-connect-row' );
			
			if ( data.error ) {
				
				connectRows.show();
				
				if ( 0 === account.length ) {
					select.after( '<div class="fl-builder-service-error">' + data.error + '</div>' );
				}
				else {
					account.after( '<div class="fl-builder-service-error">' + data.error + '</div>' );
				}
			}
			else {
				connectRows.remove();
				accountRow.remove();
				selectRow.after( data.html );
			}
			
			FLBuilderServices._addAccountDelete( wrap );
			FLBuilderServices._finishSettingsLoading();
		},
		
		/**
		 * Fires when the service account select changes.
		 *
		 * @return void
		 * @since 1.5.4
		 */
		_accountChange: function()
		{
			var nodeId      = $( '.fl-builder-settings' ).data( 'node' ),
				wrap        = $( this ).closest( '.fl-builder-service-settings' ),
				select      = wrap.find( '.fl-builder-service-select' ),
				account     = wrap.find( '.fl-builder-service-account-select' ),
				connectRows = wrap.find( '.fl-builder-service-connect-row' ),
				fieldRows   = wrap.find( 'tr.fl-builder-service-field-row' ),
				error       = $( '.fl-builder-service-error' ),
				value       = account.val(),
				data        = null;
			
			connectRows.remove();
			fieldRows.remove();
			error.remove();
			
			if ( 'add_new_account' == value ) {
				data = {
					action  : 'fl_builder_render_service_settings',
					node_id : nodeId,
					service : select.val(),
					add_new : true
				};
			}
			else if ( '' != value ) {
				data = {
					action  : 'fl_builder_render_service_fields',
					node_id : nodeId,
					service : select.val(),
					account : value
				};
			}
			
			if ( data ) {
				FLBuilderServices._startSettingsLoading( select );
				FLBuilder.ajax( data, FLBuilderServices._accountChangeComplete );
			}
			
			FLBuilderServices._addAccountDelete( wrap );
		},
		
		/**
		 * AJAX callback for when the service account select changes.
		 *
		 * @param {String} response The JSON response.
		 * @return void
		 * @since 1.5.4
		 */
		_accountChangeComplete: function( response )
		{
			var data        = JSON.parse( response ),
				wrap        = $( '.fl-builder-service-settings-loading' ),
				accountRow  = wrap.find( '.fl-builder-service-account-row' );
			
			accountRow.after( data.html );
			FLBuilderServices._finishSettingsLoading();
		},
		
		/**
		 * Adds an account delete link.
		 *
		 * @param {Object} wrap An element within the lightbox.
		 * @return void
		 * @since 1.5.4
		 */
		_addAccountDelete: function( wrap )
		{
			var account = wrap.find( '.fl-builder-service-account-select' );
			
			if ( account.length > 0 ) {
				
				wrap.find( '.fl-builder-service-account-delete' ).remove();
				
				if ( '' != account.val() && 'add_new_account' != account.val() ) {
					account.after( '<a href="javascript:void(0);" class="fl-builder-service-account-delete">' + FLBuilderStrings.deleteAccount + '</a>' );
				}
			}
		},
		
		/**
		 * Fires when the account delete link is clicked.
		 *
		 * @return void
		 * @since 1.5.4
		 */
		_accountDeleteClicked: function()
		{
			var wrap        = $( this ).closest( '.fl-builder-service-settings' ),
				select      = wrap.find( '.fl-builder-service-select' ),
				account     = wrap.find( '.fl-builder-service-account-select' );
			
			if ( confirm( FLBuilderStrings.deleteAccountWarning ) ) {
			
				FLBuilder.ajax( {
					action  : 'fl_builder_delete_service_account',
					service : select.val(),
					account : account.val()
				}, FLBuilderServices._accountDeleteComplete );
				
				FLBuilderServices._startSettingsLoading( account );
			}
		},
		
		/**
		 * AJAX callback for when the account delete link is clicked.
		 *
		 * @return void
		 * @since 1.5.4
		 */
		_accountDeleteComplete: function()
		{
			var wrap   = $( '.fl-builder-service-settings-loading' ),
				select = wrap.find( '.fl-builder-service-select' );
				
			FLBuilderServices._finishSettingsLoading();
				
			select.trigger( 'change' );
		},
		
		/* Campaign Monitor
		----------------------------------------------------------*/
		
		/**
		 * Fires when the Campaign Monitor client select is changed.
		 *
		 * @return void
		 * @since 1.5.4
		 */
		_campaignMonitorClientChange: function()
		{
			var nodeId      = $( '.fl-builder-settings' ).data( 'node' ),
				wrap        = $( this ).closest( '.fl-builder-service-settings' ),
				select      = wrap.find( '.fl-builder-service-select' ),
				account     = wrap.find( '.fl-builder-service-account-select' ),
				client      = $( this ),
				list        = wrap.find( '.fl-builder-service-list-select' ),
				value       = client.val();
			
			if ( 0 != list.length ) {
				list.closest( 'tr' ).remove();
			}
			if ( '' == value ) {
				return;
			}
			
			FLBuilderServices._startSettingsLoading( select );
			
			FLBuilder.ajax( {
				action  : 'fl_builder_render_service_fields',
				node_id : nodeId,
				service : select.val(),
				account : account.val(),
				client  : value
			}, FLBuilderServices._campaignMonitorClientChangeComplete );
		},
		
		/**
		 * AJAX callback for when the Campaign Monitor client select is changed.
		 *
		 * @param {String} response The JSON response.
		 * @return void
		 * @since 1.5.4
		 */
		_campaignMonitorClientChangeComplete: function( response )
		{
			var data    = JSON.parse( response ),
				wrap    = $( '.fl-builder-service-settings-loading' ),
				client  = wrap.find( '.fl-builder-campaign-monitor-client-select' );
			
			client.closest( 'tr' ).after( data.html );
			FLBuilderServices._finishSettingsLoading();
		},
		
		/* MailChimp
		----------------------------------------------------------*/
		
		/**
		 * Fires when the MailChimp list select is changed.
		 *
		 * @return void
		 * @since 1.6.0
		 */
		_mailChimpListChange: function()
		{
			var nodeId      = $( '.fl-builder-settings' ).data( 'node' ),
				wrap        = $( this ).closest( '.fl-builder-service-settings' ),
				select      = wrap.find( '.fl-builder-service-select' ),
				account     = wrap.find( '.fl-builder-service-account-select' ),
				list        = wrap.find( '.fl-builder-service-list-select' );
			
			$( '.fl-builder-mailchimp-group-select' ).closest( 'tr' ).remove();
			
			if ( '' == list.val() ) {
				return;
			}
			
			FLBuilderServices._startSettingsLoading( select );
			
			FLBuilder.ajax( {
				action  : 'fl_builder_render_service_fields',
				node_id : nodeId,
				service : select.val(),
				account : account.val(),
				list_id : list.val()
			}, FLBuilderServices._mailChimpListChangeComplete );
		},
		
		/**
		 * AJAX callback for when the MailChimp list select is changed.
		 *
		 * @param {String} response The JSON response.
		 * @return void
		 * @since 1.6.0
		 */
		_mailChimpListChangeComplete: function( response )
		{
			var data    = JSON.parse( response ),
				wrap    = $( '.fl-builder-service-settings-loading' ),
				list    = wrap.find( '.fl-builder-service-list-select' );
			
			list.closest( 'tr' ).after( data.html );
			FLBuilderServices._finishSettingsLoading();
		}
	};

	$ ( function() {
		FLBuilderServices.init();
	});

})( jQuery );