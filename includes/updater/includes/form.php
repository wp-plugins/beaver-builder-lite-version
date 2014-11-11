<div class="wrap">
    
    <h3 class="fl-settings-form-header">
        <?php _e('Support &amp; Updates Subscription', 'fl-builder'); ?>
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
		<?php _e('Enter the email address you used to signup to receive support and enable remote updates.', 'fl-builder'); ?>
	</p>
	<?php if(is_multisite()) : ?>
    <p>
        <strong><?php _e('NOTE:'); ?></strong> <?php _e('This applies to all sites on the network.', 'fl-builder'); ?>
    </p>
    <?php endif; ?>
    <form action="" method="post">
         	
		<input type="text" name="email" value="<?php echo self::get_subscription_email(); ?>" class="regular-text" placeholder="<?php _e('email@yourwebsite.com', 'fl-builder'); ?>" />
		
        <p class="submit">
			<input type="submit" name="submit" class="button button-primary" value="<?php _e('Save Subscription Settings', 'fl-builder'); ?>">
			<?php wp_nonce_field('updater-nonce', 'fl-updater-nonce'); ?>
        </p>
    </form>

</div>