(function($){

    FLBuilder.registerModuleHelper('widget', {

        init: function()
        {
            var form    = $('.fl-builder-settings'),
                missing = form.find('.fl-builder-widget-missing');
                
            if(missing.length > 0) {
                form.find('.fl-builder-settings-save').hide();
            }
        }
    });

})(jQuery);