(function($){

	/**
	 * Helper class for dealing with the post edit screen.
	 *
	 * @class FLBuilderAdminPosts
	 * @since 1.0
	 * @static
	 */
	FLBuilderAdminPosts = {
		
		/**
		 * Initializes the builder for the post edit screen. 
		 *
		 * @since 1.0
		 * @method init
		 */
		init: function()
		{
			$('.fl-enable-editor').on('click', this._enableEditorClicked);
			$('.fl-enable-builder').on('click', this._enableBuilderClicked);
			$('.fl-launch-builder').on('click', this._launchBuilderClicked);
			
			/* WPML Support */
			$('#icl_cfo').on('click', this._wpmlCopyClicked);
		},
		
		/**
		 * Fires when the text editor button is clicked 
		 * and switches the current post to use that 
		 * instead of the builder. 
		 *
		 * @since 1.0
		 * @access private
		 * @method _enableEditorClicked
		 */        
		_enableEditorClicked: function()
		{
			if ( ! $( 'body' ).hasClass( 'fl-builder-enabled' ) ) {
				return;
			}
			if ( confirm( FLBuilderAdminPostsStrings.switchToEditor ) ) {
			
				$('.fl-builder-admin-tabs a').removeClass('fl-active');
				$(this).addClass('fl-active');
				
				FLBuilderAdminPosts.ajax({
					action: 'fl_builder_save',
					method: 'disable',
				}, FLBuilderAdminPosts._enableEditorComplete);
			}
		},
 
		/**
		 * Callback for enabling the editor.
		 *
		 * @since 1.0
		 * @access private
		 * @method _enableEditorComplete
		 */          
		_enableEditorComplete: function()
		{
			$('body').removeClass('fl-builder-enabled');
			$(window).resize();
		},

		/**
		 * Callback for enabling the editor.
		 *
		 * @since 1.0
		 * @access private
		 * @method _enableBuilderClicked
		 */         
		_enableBuilderClicked: function()
		{
			if($('body').hasClass('fl-builder-enabled')) {
				return;
			}
			else {
				$('.fl-builder-admin-tabs a').removeClass('fl-active');
				$(this).addClass('fl-active');
				FLBuilderAdminPosts._launchBuilder();
			}
		},

		/**
		 * Fires when the page builder button is clicked 
		 * and switches the current post to use that 
		 * instead of the text editor. 
		 *
		 * @since 1.0
		 * @access private
		 * @method _launchBuilderClicked
		 * @param {Object} e An event object.
		 */   
		_launchBuilderClicked: function(e)
		{
			e.preventDefault();
			
			FLBuilderAdminPosts._launchBuilder();
		},

		/**
		 * Callback for enabling the builder.
		 *
		 * @since 1.0
		 * @access private
		 * @method _launchBuilder
		 */   
		_launchBuilder: function()
		{
			var redirect = $('.fl-launch-builder').attr('href'),
				title    = $('#title');
			
			if(typeof title !== 'undefined' && title.val() == '') {
				title.val('Post #' + $('#post_ID').val());
			}
			
			$(window).off('beforeunload');
			$('body').addClass('fl-builder-enabled');
			$('.fl-builder-loading').show();
			$('form#post').append('<input type="hidden" name="fl-builder-redirect" value="' + redirect + '" />');
			$('form#post').submit();
		},
		
		/**
		 * Fires when the WPML copy button is clicked.
		 *
		 * @since 1.1.7
		 * @access private
		 * @method _wpmlCopyClicked
		 * @param {Object} e An event object.
		 */   
		_wpmlCopyClicked: function(e)
		{
			var originalPostId = $('#icl_translation_of').val();
			
			if(typeof originalPostId !== 'undefined') {
			
				$('.fl-builder-loading').show();
				
				FLBuilderAdminPosts.ajax({
					action: 'fl_builder_save',
					method: 'duplicate_wpml_layout',
					original_post_id: originalPostId
				}, FLBuilderAdminPosts._wpmlCopyComplete);
			}
		},
		
		/**
		 * Callback for when the WPML copy button is clicked.
		 *
		 * @since 1.1.7
		 * @access private
		 * @method _wpmlCopyComplete
		 * @param {String} response The JSON encoded response.
		 */   
		_wpmlCopyComplete: function(response)
		{
			var response = JSON.parse(response);
			
			$('.fl-builder-loading').hide();
			
			if(response.has_layout && response.enabled) {
				$('body').addClass('fl-builder-enabled');
			}
		},

		/**
		 * Makes an AJAX request.
		 *
		 * @since 1.0
		 * @method ajax
		 * @param {Object} data An object with data to send in the request.
		 * @param {Function} callback A function to call when the request is complete.
		 */   
		ajax: function(data, callback)
		{
			data.post_id = $('#post_ID').val();
			
			$('.fl-builder-loading').show();
			
			$.post(ajaxurl, data, function(response) {

				FLBuilderAdminPosts._ajaxComplete();
			
				if(typeof callback !== 'undefined') {
					callback.call(this, response);
				}
			});
		},

		/**
		 * Generic callback for when an AJAX request is complete.
		 *
		 * @since 1.0
		 * @access private
		 * @method _ajaxComplete
		 */   
		_ajaxComplete: function()
		{
			$('.fl-builder-loading').hide();
		}
	};

	/* Initializes the post edit screen. */
	$(function(){
		FLBuilderAdminPosts.init();
	});

})(jQuery);