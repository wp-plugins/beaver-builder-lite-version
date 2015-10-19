<div class="fl-layout-field">
	<?php foreach($field['options'] as $key => $img) : ?>
	<div class="fl-layout-field-option <?php if($key == $value) echo 'fl-layout-field-option-selected'; ?>" data-value="<?php echo $key; ?>">
		<img src="<?php echo $img; ?>" />
	</div>
	<?php endforeach; ?>
	<div class="fl-clear"></div>
	<input name="<?php echo $name; ?>" type="hidden" value='<?php echo $value; ?>' />
</div>