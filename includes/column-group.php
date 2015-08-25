<div<?php FLBuilder::render_column_group_attributes( $group ); ?>>
	<?php foreach ( $cols as $col ) : ?>
	<div<?php echo FLBuilder::render_column_attributes( $col ); ?>>
		<div class="fl-col-content fl-node-content">
		<?php FLBuilder::render_modules( $col->node ); ?>
		</div>
	</div>
	<?php endforeach; ?>
</div>