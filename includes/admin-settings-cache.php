<div id="fl-cache-form" class="fl-settings-form">

	<h3 class="fl-settings-form-header"><?php _e('Cache', 'fl-builder'); ?></h3>

	<form id="cache-form" action="<?php FLBuilderAdminSettings::render_form_action( 'cache' ); ?>" method="post">

		<div class="fl-settings-form-content">
			
			<p><?php _e( 'A CSS and JavaScript file is dynamically generated and cached each time you create a new layout. Sometimes the cache needs to be refreshed when you migrate your site to another server or update to the latest version. If you are running into any issues, please try clearing the cache by clicking the button below.', 'fl-builder' ); ?></p>
			
			<?php if ( is_network_admin() ) : ?>
			<p><strong><?php _e( 'NOTE:', 'fl-builder' ); ?></strong> <?php _e('This applies to all sites on the network.', 'fl-builder'); ?></p>
			<?php elseif ( ! is_network_admin() && is_multisite() ) : ?>
			<p><strong><?php _e( 'NOTE:', 'fl-builder' ); ?></strong> <?php _e('This only applies to this site. Please visit the Network Admin Settings to clear the cache for all sites on the network.', 'fl-builder'); ?></p>
			<?php endif; ?>
			
		</div>
		<p class="submit">
			<input type="submit" name="update" class="button-primary" value="<?php esc_attr_e( 'Clear Cache', 'fl-builder' ); ?>" />
			<?php wp_nonce_field('cache', 'fl-cache-nonce'); ?>
		</p>
	</form>
</div>