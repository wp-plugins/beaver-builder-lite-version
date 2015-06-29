<select name="<?php echo $name; if(isset($field['multi-select'])) echo '[]'; ?>"<?php if(isset($field['class'])) echo ' class="'. $field['class'] .'"'; if(isset($field['multi-select'])) echo ' multiple '; if(isset($field['toggle'])) echo "data-toggle='". json_encode($field['toggle']) ."'"; if(isset($field['hide'])) echo "data-hide='". json_encode($field['hide']) ."'"; if(isset($field['trigger'])) echo "data-trigger='". json_encode($field['trigger']) ."'"; ?>>
	<?php 
	
	foreach ( $field['options'] as $option_key => $option_val ) : 
	
		if ( is_array( $option_val ) && isset( $option_val['premium' ] ) && $option_val['premium'] && true === FL_BUILDER_LITE ) {
			continue;
		}
		
		$label = is_array( $option_val ) ? $option_val['label'] : $option_val;
		
		if ( is_array( $value ) && in_array( $option_key, $value ) ) {
			$selected = ' selected="selected"';
		}
		else if ( ! is_array( $value ) && selected( $value, $option_key, true ) ) {
			$selected = ' selected="selected"';
		}
		else {
			$selected = '';
		}
	
	?>
	<option value="<?php echo $option_key; ?>" <?php echo $selected; ?>><?php echo $label; ?></option>
	<?php endforeach; ?>
</select>