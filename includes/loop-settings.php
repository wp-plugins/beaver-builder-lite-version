<?php 

FLBuilderModel::default_settings($settings, array(
	'post_type' => 'post',
	'order_by'  => 'date',
	'order'     => 'DESC',
	'offset'    => 0,
	'users'     => ''
));

?>
<div id="fl-builder-settings-section-general" class="fl-loop-builder fl-builder-settings-section">

	<table class="fl-form-table">
	<?php 
	
	// Post type
	FLBuilder::render_settings_field('post_type', array(
		'type'          => 'post-type',
		'label'         => __('Post Type', 'fl-builder'),
	), $settings); 
	
	// Order by
	FLBuilder::render_settings_field('order_by', array(
		'type'          => 'select',
		'label'         => __('Order By', 'fl-builder'),
		'options'       => array(
			'ID'            => __('ID', 'fl-builder'),
			'date'          => __('Date', 'fl-builder'),
			'modified'      => __('Date Last Modified', 'fl-builder'),
			'title'         => __('Title', 'fl-builder'),
			'author'        => __('Author', 'fl-builder'),
			'comment_count' => __('Comment Count', 'fl-builder'),
			'menu_order'    => __('Menu Order', 'fl-builder'),
			'random'        => __('Random', 'fl-builder'),
		)
	), $settings); 
	
	// Order
	FLBuilder::render_settings_field('order', array(
		'type'          => 'select',
		'label'         => __('Order', 'fl-builder'),
		'options'       => array(
			'DESC'          => __('Descending', 'fl-builder'),
			'ASC'           => __('Ascending', 'fl-builder'),
		)
	), $settings); 
	
	// Offset
	FLBuilder::render_settings_field('offset', array(
		'type'          => 'text',
		'label'         => _x('Offset', 'How many posts to skip.', 'fl-builder'),
		'default'       => '0',
		'size'          => '4',
		'help'          => __('Skip this many posts that match the specified criteria.', 'fl-builder')
	), $settings); 
	
	?>
	</table>
</div>
<div id="fl-builder-settings-section-filter" class="fl-builder-settings-section">
	<h3 class="fl-builder-settings-title"><?php _e('Filter', 'fl-builder'); ?></h3>
	<?php foreach(FLBuilderLoop::post_types() as $slug => $type) : ?>
		<table class="fl-form-table fl-loop-builder-filter fl-loop-builder-<?php echo $slug; ?>-filter" <?php if($slug == $settings->post_type) echo 'style="display:table;"'; ?>>
		<?php 
		
		// Posts
		FLBuilder::render_settings_field('posts_' . $slug, array(
			'type'          => 'suggest',
			'action'        => 'fl_as_posts',
			'data'          => $slug,
			'label'         => $type->label,
			'help'          => sprintf(__('Enter a comma separated list of %s. Only these %s will be shown.', 'fl-builder'), $type->label, $type->label)
		), $settings); 
		
		// Taxonomies
		$taxonomies = FLBuilderLoop::taxonomies($slug);
		
		foreach($taxonomies as $tax_slug => $tax) {

			FLBuilder::render_settings_field('tax_' . $slug . '_' . $tax_slug, array(
				'type'          => 'suggest',
				'action'        => 'fl_as_terms',
				'data'          => $tax_slug,
				'label'         => $tax->label,
				'help'          => sprintf(__('Enter a comma separated list of %s. Only posts with these %s will be shown.', 'fl-builder'), $tax->label, $tax->label)
			), $settings); 
		}
		
		?>
		</table>
	<?php endforeach; ?>
	<table class="fl-form-table">
	<?php

	// Author
	FLBuilder::render_settings_field('users', array(
		'type'          => 'suggest',
		'action'        => 'fl_as_users',
		'label'         => __('Authors', 'fl-builder'),
		'help'          => __('Enter a comma separated list of authors usernames. Only posts with these authors will be shown.', 'fl-builder')
	), $settings); 
	
	?>
	</table>
</div>