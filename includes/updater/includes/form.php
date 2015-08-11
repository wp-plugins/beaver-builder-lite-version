<div class="wrap">

	<?php if(!$status) : ?>
	<p style="padding:10px 20px; background: #d54e21; color: #fff;">
		<?php _e('UPDATES UNAVAILABLE! Please subscribe or enter your license key below to enable automatic updates.', 'fl-builder'); ?>
		&nbsp;<a style="color: #fff;" href="<?php echo FLBuilderModel::get_upgrade_url( array( 'utm_source' => 'external', 'utm_medium' => 'builder', 'utm_campaign' => 'settings-page' ) ); ?>" target="_blank"><?php _e('Subscribe Now', 'fl-builder'); ?> &raquo;</a>
	</p>
	<?php endif; ?>

	<h3 class="fl-settings-form-header">
		<?php _e('Updates &amp; Support Subscription', 'fl-builder'); ?>
		<span> &mdash; </span>
		<?php if($status) : ?>
		<i style="color:#3cb341;"><?php _e('Active!', 'fl-builder'); ?></i>
		<?php else : ?>
		<i style="color:#ae5842;"><?php _e('Not Active!', 'fl-builder'); ?></i>
		<?php endif; ?>
	</h3>

	<?php if(isset($_POST['fl-updater-nonce'])) : ?>
	<div class="updated">
		<p><?php _e('Email address saved!', 'fl-builder'); ?></p>
	</div>
	<?php endif; ?>

	<p>
		<?php echo sprintf( __( 'Enter your <a%s>license key</a> to enable remote updates and support.', 'fl-builder' ), ' href="https://www.wpbeaverbuilder.com/my-account/?utm_source=external&utm_medium=builder&utm_campaign=settings-page" target="_blank"' ) ?>
	</p>
	<?php if(is_multisite()) : ?>
	<p>
		<strong><?php _e( 'NOTE:', 'fl-builder' ); ?></strong> <?php _e('This applies to all sites on the network.', 'fl-builder'); ?>
	</p>
	<?php endif; ?>
	<form action="" method="post">

		<input type="password" name="email" value="<?php echo self::get_subscription_email(); ?>" class="regular-text" />

		<p class="submit">
			<input type="submit" name="submit" class="button button-primary" value="<?php esc_attr_e( 'Save Subscription Settings', 'fl-builder' ); ?>">
			<?php wp_nonce_field('updater-nonce', 'fl-updater-nonce'); ?>
		</p>
	</form>

</div>