<div class="fl-lightbox-header">
	<h1><?php _e( 'Select Icon', 'fl-builder' ); ?></h1>
	<div class="fl-icons-filter fl-builder-settings-fields">
		<select class="fl-icons-filter-select">
			<option value="all"><?php _ex( 'All Libraries', 'Select option for showing all icon libraries.', 'fl-builder' ); ?></option>
			<?php foreach ( $icon_sets as $set_key => $set_data ) : ?>
			<option value="<?php echo $set_key; ?>"><?php echo $set_data['name']; ?></option>
			<?php endforeach; ?>
		</select>
		<input type="text" class="fl-icons-filter-text" placeholder="Search..." />
	</div>
</div>
<div class="fl-icons-list">
	<?php foreach ( $icon_sets as $set_key => $set_data ) : ?>
	<div class="fl-icons-section fl-<?php echo $set_key; ?>">
		<h2><?php echo $set_data['name']; ?></h2>
		<?php foreach( $set_data['icons'] as $icon ) : ?>
			<?php if ( ! empty( $set_data['prefix'] ) ) : ?>
			<i class="<?php echo $set_data['prefix'] . ' ' . $icon; ?>"></i>
			<?php else : ?>
			<i class="<?php echo $icon; ?>"></i>
			<?php endif; ?>
		<?php endforeach; ?>
	</div>
	<?php endforeach; ?>
</div>
<div class="fl-lightbox-footer fl-icon-selector-footer">
	<a class="fl-icon-selector-cancel fl-builder-button fl-builder-button-large" href="javascript:void(0);" onclick="return false;"><?php _e('Cancel', 'fl-builder'); ?></a>
</div>