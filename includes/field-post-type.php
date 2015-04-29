<select name="<?php echo $name; ?>"<?php if(isset($field['class'])) echo ' class="'. $field['class'] .'"'; ?>>
	<?php foreach(FLBuilderLoop::post_types() as $slug => $type) : ?>
	<option value="<?php echo $slug; ?>" <?php selected($value, $slug); ?>><?php echo $type->labels->name; ?></option>
	<?php endforeach; ?>
</select>