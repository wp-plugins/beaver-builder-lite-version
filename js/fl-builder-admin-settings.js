(function($){

    /**
     * @class FLBuilderAdminSettings
     */ 
    FLBuilderAdminSettings = {
	    
	    /**
		 * @property _iconUploader
		 * @private
		 */
		_iconUploader: null,
    
	    /**
	     * @method init
	     */ 
        init: function()
        {
            this._bind();
            this._initNav();
            this._initOverrides();
        },
        
        /**
	     * @method _bind
	     * @private
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
        },
        
        /**
	     * @method _initNav
	     * @private
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
	     * @method _navClicked
	     * @private
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
	     * @method _moduleAllCheckboxClicked
	     * @private
	     */
        _moduleAllCheckboxClicked: function()
        {
            if($(this).is(':checked')) {
                $('.fl-module-cb').prop('checked', true);
            }
        },
        
        /**
	     * @method _moduleCheckboxClicked
	     * @private
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
	     * @method _initOverrides
	     * @private
	     */
        _initOverrides: function()
        {
            $('.fl-override-ms-cb').each(FLBuilderAdminSettings._initOverride);
        },
        
        /**
	     * @method _initOverride
	     * @private
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
	     * @method _overrideCheckboxClicked
	     * @private
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
	     * @method _showIconUploader
	     * @private
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
	     * @method _iconFileSelected
	     * @private
	     */
        _iconFileSelected: function()
        {
            var file = FLBuilderAdminSettings._iconUploader.state().get('selection').first().toJSON();
            
            $( 'input[name=fl-new-icon-set]' ).val( file.id );
            $( '#icons-form' ).submit();
        },
        
        /**
	     * @method _deleteCustomIconSet
	     * @private
	     */
        _deleteCustomIconSet: function()
        {
            var set = $( this ).data( 'set' );
            
            $( 'input[name=fl-delete-icon-set]' ).val( set );
            $( '#icons-form' ).submit();
        },
        
        /**
	     * @method _uninstallFormSubmit
	     * @private
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

    $(function(){
        FLBuilderAdminSettings.init();
    });

})(jQuery);