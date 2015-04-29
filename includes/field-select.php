<select name="<?php echo $name; ?>"<?php if(isset($field['class'])) echo ' class="'. $field['class'] .'"'; if(isset($field['toggle'])) echo "data-toggle='". json_encode($field['toggle']) ."'"; if(isset($field['hide'])) echo "data-hide='". json_encode($field['hide']) ."'"; if(isset($field['trigger'])) echo "data-trigger='". json_encode($field['trigger']) ."'"; ?>>
	<?php 
	
	foreach($field['options'] as $key => $val) : 
	
		$label = is_array($val) ? $val['label'] : $val;
	
		if(is_array($val) && isset($val['premium']) && $val['premium'] && FL_BUILDER_LITE === true) {
			continue;
		}
	
	?>
	<option value="<?php echo $key; ?>" <?php selected($value, $key); ?>><?php echo $label; ?></option>
	<?php endforeach; ?>
</select>