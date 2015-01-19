<?php

/**
 * @class FLWidgetModule
 */
class FLWidgetModule extends FLBuilderModule {

    /** 
     * @method __construct
     */  
    public function __construct()
    {
        parent::__construct(array(
            'name'          => __('Widget', 'fl-builder'),
            'description'   => __('Display a WordPress widget.', 'fl-builder'),
            'category'   	=> __('WordPress Widgets', 'fl-builder'),
            'editor_export' => false
        ));
    }
}

/**
 * Register the module and its form settings.
 */
FLBuilder::register_module('FLWidgetModule', array(
    'general'       => array( // Tab
        'title'         => __('General', 'fl-builder'), // Tab title
        'file'          => FL_BUILDER_DIR . 'modules/widget/includes/settings-general.php'
    )
));