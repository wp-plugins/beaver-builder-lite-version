<?php 

// Get builder post data.
$post_data = FLBuilderModel::get_post_data();

// Widget class
if(isset($settings->widget)) {
    $widget_class = $settings->widget;
}
else if(isset($post_data['widget'])) {
    $widget_class = $post_data['widget'];
}

if(isset($widget_class) && class_exists($widget_class)) {

    // Widget instance
    $widget_instance = new $widget_class();
    
    // Widget settings
    $settings_key = 'widget-' . $widget_instance->id_base;
    $widget_settings = array();
    
    if(isset($settings->$settings_key)) {
        $widget_settings = (array)$settings->$settings_key;
    }

    // Widget title
    echo '<h3 class="fl-builder-settings-title">' . $widget_instance->name . '</h3>';
    
    // Widget form
    echo '<div class="fl-field" data-preview=\'{"type":"widget"}\'>';
    $widget_instance->form($widget_settings);
    echo '<input type="hidden" name="widget" value="' . $widget_class . '" />';   
    echo '</div>';
}
else if(isset($widget_class)) {   

    // Widget doesn't exist!
    echo '<div class="fl-builder-widget-missing">';
    echo $widget_class . __(' no longer exists.');
    echo '</div>';
}