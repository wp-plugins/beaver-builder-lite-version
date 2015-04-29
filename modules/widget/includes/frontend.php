<div class="fl-widget">
<?php

global $wp_widget_factory;

// Get builder post data.
$post_data = FLBuilderModel::get_post_data();

// Widget class
if(isset($settings->widget)) {
	$widget_slug = $settings->widget;
}
else if(isset($post_data['widget']) && FLBuilderModel::is_builder_active()) {
	$widget_slug = $post_data['widget'];
}

if(isset($widget_slug) && isset($wp_widget_factory->widgets[$widget_slug])) {

	// Widget instance
	$factory_instance   = $wp_widget_factory->widgets[$widget_slug];
	$widget_class       = get_class($factory_instance);
	$widget_instance    = new $widget_class($factory_instance->id_base, $factory_instance->name, $factory_instance->widget_options);

	// Widget settings
	$settings_key       = 'widget-' . $widget_instance->id_base;
	$widget_settings    = isset($settings->$settings_key) ? (array)$settings->$settings_key : array();

	// Render the widget
	wp_cache_flush($widget_slug, 'widget');
	the_widget($widget_slug, $widget_settings, array('widget_id' => $module->node));
}
else if(isset($widget_slug) && FLBuilderModel::is_builder_active()) {

	// Widget doesn't exist!
	printf( _x( '%s no longer exists.', '%s stands for widget slug.', 'fl-builder' ), $widget_slug );

}

?>
</div>