<?php

global $wp_widget_factory;

// Get builder post data.
$post_data = FLBuilderModel::get_post_data();

// Widget slug
if(isset($settings->widget)) {
	$widget_slug = $settings->widget;
}
else if(isset($post_data['widget'])) {
	$widget_slug = $post_data['widget'];
}

if(isset($widget_slug) && isset($wp_widget_factory->widgets[$widget_slug])) {

	// Widget instance
	$factory_instance   = $wp_widget_factory->widgets[$widget_slug];
	$widget_class       = get_class($factory_instance);
	$widget_instance    = new $widget_class($factory_instance->id_base, $factory_instance->name, $factory_instance->widget_options);

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
	// Uncommenting this will display custom fields from plugins like ACF, but we don't have a way to save them, yet..
	//do_action_ref_array( 'in_widget_form', array( &$widget_instance, true, $widget_settings ) );
	echo '<input type="hidden" name="widget" value="' . $widget_slug . '" />';
	echo '</div>';
}
else if(isset($widget_slug)) {

	// Widget doesn't exist!
	echo '<div class="fl-builder-widget-missing">';
	printf( _x( '%s no longer exists.', '%s stands for widget slug.', 'fl-builder' ), $widget_slug );
	echo '</div>';
}