<div class="fl-link-field">
	<input type="text" name="<?php echo $name; ?>" value="<?php echo esc_url($value); ?>" class="text fl-link-field-input" placeholder="http://www.example.com" />
	<span class="fl-link-field-select fl-builder-button fl-builder-button-small" href="javascript:void(0);" onclick="return false;"><?php _e('Select', 'fl-builder'); ?></span>
	<div class="fl-link-field-search">
		<span class="fl-link-field-search-title"><?php _e('Enter a post title to search.', 'fl-builder'); ?></span>
		<input type="text" name="<?php echo $name; ?>-search" class="text text-full fl-link-field-search-input" placeholder="<?php esc_attr_e( 'Start typing...', 'fl-builder' ); ?>" />
		<span class="fl-link-field-search-cancel fl-builder-button fl-builder-button-small" href="javascript:void(0);" onclick="return false;"><?php _e('Cancel', 'fl-builder'); ?></span>
	</div>
</div>