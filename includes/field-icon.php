<div class="fl-icon-field fl-builder-custom-field<?php if(empty($value)) echo ' fl-icon-empty'; if(isset($field['class'])) echo ' ' . $field['class']; ?>">
	<a class="fl-icon-select" href="javascript:void(0);" onclick="return false;"><?php _e('Select Icon', 'fl-builder'); ?></a>
	<div class="fl-icon-preview">
		<i class="<?php echo $value; ?>" data-icon="<?php echo $value; ?>"></i>
		<a class="fl-icon-replace" href="javascript:void(0);" onclick="return false;"><?php _e('Replace', 'fl-builder'); ?></a>
		<?php if(isset($field['show_remove']) && $field['show_remove']) : ?>
		<a class="fl-icon-remove" href="javascript:void(0);" onclick="return false;"><?php _e('Remove', 'fl-builder'); ?></a>
		<?php endif; ?>
	</div>
	<input name="<?php echo $name; ?>" type="hidden" value="<?php echo $value; ?>" />
</div>