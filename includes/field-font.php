<div class="fl-font-field">
	<select name="<?php echo $name . '[][family]'; ?>" class="fl-font-field-font">
		<?php FLBuilderFonts::display_select_font( $value['family'] ) ?>
	</select>
	<select name="<?php echo $name . '[][weight]'; ?>" class="fl-font-field-weight">
		<?php FLBuilderFonts::display_select_weight( $value['family'], $value['weight'] ) ?>
	</select>
</div>