<div class="fl-bg-video" 
data-width="<?php if ( isset( $vid_data['mp4'] ) ) echo @$vid_data['mp4']->width; else echo @$vid_data['webm']->width; ?>" 
data-height="<?php if ( isset( $vid_data['mp4'] ) ) echo @$vid_data['mp4']->height; else echo @$vid_data['webm']->height; ?>" 
data-fallback="<?php if ( isset( $vid_data['mp4'] ) ) echo $vid_data['mp4']->fallback; else echo $vid_data['webm']->fallback; ?>" 
<?php if ( isset( $vid_data['mp4'] ) ) : ?>
data-mp4="<?php echo $vid_data['mp4']->url; ?>" 
data-mp4-type="video/<?php echo $vid_data['mp4']->extension; ?>" 
<?php endif; ?>
<?php if ( isset( $vid_data['webm'] ) ) : ?>
data-webm="<?php echo $vid_data['webm']->url; ?>" 
data-webm-type="video/<?php echo $vid_data['webm']->extension; ?>" 
<?php endif; ?>></div>