<div id="fl-editing-form" class="fl-settings-form">

	<h3 class="fl-settings-form-header"><?php _e('Editing Settings', 'fl-builder'); ?></h3>

	<form id="editing-form" action="<?php FLBuilderAdminSettings::render_form_action( 'editing' ); ?>" method="post">

		<?php if ( FLBuilderAdminSettings::multisite_support() && ! is_network_admin() ) : ?>
		<label>
			<input class="fl-override-ms-cb" type="checkbox" name="fl-override-ms" value="1" <?php if(get_option('_fl_builder_editing_capability')) echo 'checked="checked"'; ?> />
			<?php _e('Override network settings?', 'fl-builder'); ?>
		</label>
		<?php endif; ?>

		<div class="fl-settings-form-content">

			<p><?php printf( __( 'Set the <a%s>capability</a> required for users to access advanced builder editing such as adding, deleting or moving modules.', 'fl-builder' ), ' href="http://codex.wordpress.org/Roles_and_Capabilities#Capability_vs._Role_Table" target="_blank"' ); ?></p>

			<input type="text" name="fl-editing-capability" value="<?php echo esc_html(FLBuilderModel::get_editing_capability()); ?>" class="regular-text" />

		</div>
		<p class="submit">
			<input type="submit" name="update" class="button-primary" value="<?php esc_attr_e( 'Save Editing Settings', 'fl-builder' ); ?>" />
			<?php wp_nonce_field('editing', 'fl-editing-nonce'); ?>
		</p>
	</form>
</div>