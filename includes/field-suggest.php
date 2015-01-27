<?php

$action = isset($field['action']) ? $field['action'] : '';
$data   = isset($field['data']) ? $field['data'] : '';

?>
<input type="text" name="<?php echo $name; ?>" data-value='<?php echo FLBuilderAutoSuggest::get_value($action, $value, $data); ?>' data-action="<?php echo $action; ?>" data-action-data="<?php echo $data; ?>" class="text text-full fl-suggest-field<?php if(isset($field['class'])) echo ' '. $field['class']; ?>" placeholder="<?php if ( isset( $field['placeholder'] ) ) echo esc_attr( $field['placeholder'] ); else esc_attr_e( 'Start typing...', 'fl-builder' ); ?>" />