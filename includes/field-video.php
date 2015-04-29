<?php $video = FLBuilderPhoto::get_attachment_data($value); ?>
<div class="fl-video-field fl-builder-custom-field<?php if(empty($value) || !$video) echo ' fl-video-empty'; if(isset($field['class'])) echo ' ' . $field['class']; ?>">
	<a class="fl-video-select" href="javascript:void(0);" onclick="return false;"><?php _e('Select Video', 'fl-builder'); ?></a>
	<div class="fl-video-preview">
		<?php if(!empty($value) && $video) : ?>
		<div class="fl-video-preview-img">
			<img src="<?php echo $video->icon; ?>" />
		</div>
		<span class="fl-video-preview-filename"><?php echo $video->filename; ?></span>
		<?php else : ?>
		<div class="fl-video-preview-img">
			<img src="<?php echo FL_BUILDER_URL; ?>img/spacer.png" />
		</div>
		<span class="fl-video-preview-filename"></span>
		<?php endif; ?>
		<br />
		<a class="fl-video-replace" href="javascript:void(0);" onclick="return false;"><?php _e('Replace Video', 'fl-builder'); ?></a>
		<div class="fl-clear"></div>
	</div>
	<input name="<?php echo $name; ?>" type="hidden" value='<?php echo $value; ?>' />
</div>