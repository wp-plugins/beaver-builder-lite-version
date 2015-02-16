var FLBuilderAdminPosts;

(function($){

    /**
     * @class FLBuilderAdminPosts
     * @static
     */
    FLBuilderAdminPosts = {
        
        /**
         * @method _init
         * @private
         */
        _init: function()
        {
            $('.fl-enable-editor').on('click', this._enableEditorClicked);
            $('.fl-enable-builder').on('click', this._enableBuilderClicked);
            $('.fl-launch-builder').on('click', this._launchBuilderClicked);
            
            /* WPML Support */
            $('#icl_cfo').on('click', this._wpmlCopyClicked);
        },
        
        /**
         * @method _enableEditorClicked
         * @private
         */        
        _enableEditorClicked: function()
        {
            if(!$('body').hasClass('fl-builder-enabled')) {
                return;
            }
            
            $('.fl-builder-admin-tabs a').removeClass('fl-active');
            $(this).addClass('fl-active');
            
            FLBuilderAdminPosts.ajax({
                action: 'fl_builder_save',
                method: 'disable',
            }, FLBuilderAdminPosts._enableEditorComplete);
        },
 
        /**
         * @method _enableEditorComplete
         * @private
         */          
        _enableEditorComplete: function()
        {
            $('body').removeClass('fl-builder-enabled');
            $(window).resize();
        },

        /**
         * @method _enableBuilderClicked
         * @private
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
         * @method _launchBuilderClicked
         * @private
         */   
        _launchBuilderClicked: function(e)
        {
            e.preventDefault();
            
            FLBuilderAdminPosts._launchBuilder();
        },

        /**
         * @method _launchBuilder
         * @private
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
         * @method _wpmlCopyClicked
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
         * @method _wpmlCopyComplete
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
         * @method ajax
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
         * @method _ajaxComplete
         * @private
         */   
        _ajaxComplete: function(data)
        {
            $('.fl-builder-loading').hide();
        }
    };

    $(function(){
        FLBuilderAdminPosts._init();
    });

})(jQuery);