<div class="wrap <?php FLBuilderAdminSettings::render_page_class(); ?>">

	<h2 class="fl-settings-heading">
		<?php FLBuilderAdminSettings::render_page_heading(); ?>
	</h2>
	
	<?php FLBuilderAdminSettings::render_update_message(); ?>

	<div class="fl-settings-nav">
		<ul>
			<?php FLBuilderAdminSettings::render_nav_items(); ?>
		</ul>
	</div>

	<div class="fl-settings-content">
		<?php FLBuilderAdminSettings::render_forms(); ?>
	</div>
</div>