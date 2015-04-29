<div class="fl-bg-video">
	<?php if ( wp_is_mobile() ) : ?>
	<div id="fl-bg-video-fallback-<?php echo $row->node; ?>" class="fl-bg-video-fallback"<?php if(!empty($vid_data->fallback)) echo ' style="background-image: url(' . $vid_data->fallback . ');"'; ?>></div>
	<?php else : ?> 
	<video autoplay loop muted preload data-width="<?php echo @$vid_data->width; ?>" data-height="<?php echo @$vid_data->height; ?>">
		<source src="<?php echo $vid_data->url; ?>" type="video/<?php echo $vid_data->extension; ?>" onerror="var wrap = this.parentNode.parentNode, vid = this.parentNode; wrap.appendChild(document.getElementById('fl-bg-video-fallback-<?php echo $row->node; ?>')); wrap.removeChild(vid);">
		<div id="fl-bg-video-fallback-<?php echo $row->node; ?>" class="fl-bg-video-fallback"<?php if(!empty($vid_data->fallback)) echo ' style="background-image: url(' . $vid_data->fallback . ');"'; ?>></div>
	</video>
	<?php endif; ?>
</div>