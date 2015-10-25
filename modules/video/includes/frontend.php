<?php 
	
$vid_data = $module->get_data();
$preload  = FLBuilderModel::is_builder_active() ? ' preload="none"' : '';

?>

<div class="fl-video fl-<?php echo ($settings->video_type == 'media_library') ? 'wp' : 'embed'; ?>-video" itemscope itemtype="http://schema.org/VideoObject">
	<?php
	
		global $wp_embed;
	
		if($vid_data && $settings->video_type == 'media_library') {
			echo '<meta itemprop="url" content="' . $vid_data->url . '" />';
			echo '<meta itemprop="thumbnail" content="' . $vid_data->poster . '" />';
			echo do_shortcode('[video width="100%" height="100%" ' . $vid_data->extension . '="' . $vid_data->url . '" poster="' . $vid_data->poster . '"' . $vid_data->autoplay . $vid_data->loop . $preload . '][/video]');
		} 
		else if($settings->video_type == 'embed') {
			echo $wp_embed->autoembed($settings->embed_code);
		}
	?>
</div>