<?php

// Normalize the value so we have an array.	
if ( ! empty( $value ) && is_string( $value ) ) {
	
	$value = json_decode( $value );
	
	// Older versions might be double encoded.
	if ( is_string( $value ) ) {
		$value = json_decode( $value );
	}
}
else if ( empty( $value ) ) {
	$value = array();
}
	
?>
<div class="fl-multiple-photos-field fl-builder-custom-field<?php if ( empty( $value ) ) echo ' fl-multiple-photos-empty'; if ( isset( $field['class'] ) ) echo ' ' . $field['class']; ?>">
	<div class="fl-multiple-photos-count">
	<?php printf( _n( '1 Photo Selected', '%d Photos Selected', count( $value ), 'fl-builder' ), count( $value ) ); ?>
	</div>
	<a class="fl-multiple-photos-select" href="javascript:void(0);" onclick="return false;"><?php _e('Create Gallery', 'fl-builder'); ?></a>
	<a class="fl-multiple-photos-edit" href="javascript:void(0);" onclick="return false;"><?php _e('Edit Gallery', 'fl-builder'); ?></a>
	<a class="fl-multiple-photos-add" href="javascript:void(0);" onclick="return false;"><?php _e('Add Photos', 'fl-builder'); ?></a>
	<input name="<?php echo $name; ?>" type="hidden" value='<?php if ( ! empty( $value ) ) echo json_encode( $value ); ?>' />
</div>