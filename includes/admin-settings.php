<div class="wrap<?php if(!class_exists('FLBuilderMultisiteSettings')) echo ' fl-settings-single-install'; ?>">

    <h2 class="fl-settings-heading">
        <?php if(FLBuilderModel::get_branding_icon() != '') : ?>
        <img src="<?php echo FLBuilderModel::get_branding_icon(); ?>" />
        <?php endif; ?>
        <span><?php printf( _x( '%s Settings', '%s stands for custom branded "Page Builder" name.', 'fl-builder' ), FLBuilderModel::get_branding() ); ?></span>
    </h2>

    <?php if(!empty($_POST) && !isset($_POST['email'])) : ?>
    <div class="updated">
        <p><?php _e('Settings updated!', 'fl-builder'); ?></p>
    </div>
    <?php endif; ?>

	<div class="fl-settings-nav">
        <ul>
            <?php if(!class_exists('FLBuilderMultisiteSettings') && FL_BUILDER_LITE !== true) : ?>
            <li><a href="#license"><?php _e('License', 'fl-builder'); ?></a></li>
            <?php endif; ?>

            <?php if(FL_BUILDER_LITE === true) : ?>
            <li><a href="#upgrade"><?php _e('Upgrade', 'fl-builder'); ?></a></li>
            <?php endif; ?>

            <li><a href="#modules"><?php _e('Modules', 'fl-builder'); ?></a></li>

            <?php if(FL_BUILDER_LITE !== true) : ?>
            <li><a href="#templates"><?php _e('Templates', 'fl-builder'); ?></a></li>
            <?php endif; ?>

            <li><a href="#post-types"><?php _e('Post Types', 'fl-builder'); ?></a></li>
            <li><a href="#editing"><?php _e('Editing', 'fl-builder'); ?></a></li>

            <?php if(!class_exists('FLBuilderMultisiteSettings') && file_exists(FL_BUILDER_DIR . 'includes/admin-branding.php')) : ?>
            <li><a href="#branding"><?php _e('Branding', 'fl-builder'); ?></a></li>
            <?php endif; ?>

            <?php if(!class_exists('FLBuilderMultisiteSettings')) : ?>
            <li><a href="#uninstall"><?php _e('Uninstall', 'fl-builder'); ?></a></li>
            <?php endif; ?>
        </ul>
	</div>

	<div class="fl-settings-content">

        <?php if(!class_exists('FLBuilderMultisiteSettings') && FL_BUILDER_LITE !== true) : ?>
	    <!-- LICENSE -->
        <div id="fl-license-form" class="fl-settings-form">
            <?php do_action('fl_themes_license_form'); ?>
        </div>
        <!-- LICENSE -->
        <?php endif; ?>

        <?php if(FL_BUILDER_LITE === true) : ?>
	    <!-- UPGRADE -->
        <div id="fl-upgrade-form" class="fl-settings-form">

            <h3 class="fl-settings-form-header"><?php _e('Upgrade', 'fl-builder'); ?></h3>

            <p><?php _e('You are currently running the lite version of the Beaver Builder plugin. Upgrade today for access to premium features such as advanced modules, templates, support and more!', 'fl-builder'); ?></p>

            <input type="button" class="button button-primary" value="<?php _e('Upgrade Now', 'fl-builder'); ?>" onclick="window.location.href='<?php echo FL_BUILDER_UPGRADE_URL; ?>';" style="margin-right: 10px;">

            <input type="button" class="button button-primary" value="<?php _e('Learn More', 'fl-builder'); ?>" onclick="window.location.href='http://www.wpbeaverbuilder.com/';">

        </div>
        <!-- UPGRADE -->
        <?php endif; ?>

	    <!-- MODULES -->
        <div id="fl-modules-form" class="fl-settings-form">

            <h3 class="fl-settings-form-header"><?php _e('Enabled Modules', 'fl-builder'); ?></h3>

            <form id="modules-form" action="<?php echo admin_url('/options-general.php?page=fl-builder-settings#modules'); ?>" method="post">

                <?php if(class_exists('FLBuilderMultisiteSettings')) : ?>
                <label>
                    <input class="fl-override-ms-cb" type="checkbox" name="fl-override-ms" value="1" <?php if(get_option('_fl_builder_enabled_modules')) echo 'checked="checked"'; ?> />
                    <?php _e('Override network settings?', 'fl-builder'); ?>
                </label>
                <?php endif; ?>

                <div class="fl-settings-form-content">

                    <p><?php _e('Check or uncheck modules below to enable or disable them.', 'fl-builder'); ?></p>
                    <?php

                    $enabled_modules = FLBuilderModel::get_enabled_modules();
                    $checked = in_array('all', $enabled_modules) ? 'checked' : '';

                    ?>
                    <label>
                        <input class="fl-module-all-cb" type="checkbox" name="fl-modules[]" value="all" <?php echo $checked; ?> />
                        <?php _ex( 'All', 'Plugin setup page: Modules.', 'fl-builder' ); ?>
                    </label>
                    <?php

                    foreach(FLBuilderModel::$modules as $module) :

                        $checked = in_array($module->slug, $enabled_modules) ? 'checked' : '';

                    ?>
                    <p>
                        <label>
                            <input class="fl-module-cb" type="checkbox" name="fl-modules[]" value="<?php echo $module->slug; ?>" <?php echo $checked; ?> />
                            <?php echo $module->name; ?>
                        </label>
                    </p>
                    <?php endforeach; ?>
                </div>
                <p class="submit">
                    <input type="submit" name="update" class="button-primary" value="<?php esc_attr_e( 'Save Module Settings', 'fl-builder' ); ?>" />
                    <?php wp_nonce_field('modules', 'fl-modules-nonce'); ?>
                </p>
            </form>
        </div>
        <!-- MODULES -->

        <?php if(FL_BUILDER_LITE !== true) : ?>
	    <!-- TEMPLATES -->
        <div id="fl-templates-form" class="fl-settings-form">

            <h3 class="fl-settings-form-header"><?php _e('Template Settings', 'fl-builder'); ?></h3>

            <form id="templates-form" action="<?php echo admin_url('/options-general.php?page=fl-builder-settings#templates'); ?>" method="post">

                <?php if(class_exists('FLBuilderMultisiteSettings')) : ?>
                <label>
                    <input class="fl-override-ms-cb" type="checkbox" name="fl-override-ms" value="1" <?php if(get_option('_fl_builder_enabled_templates')) echo 'checked="checked"'; ?> />
                    <?php _e('Override network settings?', 'fl-builder'); ?>
                </label>
                <?php endif; ?>

                <div class="fl-settings-form-content">

                    <p><?php _e('Enable or disable templates using the options below.', 'fl-builder'); ?></p>
                    <?php

                    $enabled_templates = FLBuilderModel::get_enabled_templates();

                    ?>
					<select name="fl-template-settings">
						<option value="enabled" <?php selected( $enabled_templates, 'enabled' ); ?>><?php _e( 'Enable All Templates', 'fl-builder' ); ?></option>
						<option value="core" <?php selected( $enabled_templates, 'core' ); ?>><?php _e( 'Enable Core Templates Only', 'fl-builder' ); ?></option>
						<option value="user" <?php selected( $enabled_templates, 'user' ); ?>><?php _e( 'Enable User Templates Only', 'fl-builder' ); ?></option>
						<option value="disabled" <?php selected( $enabled_templates, 'disabled' ); ?>><?php _e( 'Disable All Templates', 'fl-builder' ); ?></option>
					</select>
                </div>
                <p class="submit">
                    <input type="submit" name="update" class="button-primary" value="<?php esc_attr_e( 'Save Template Settings', 'fl-builder' ); ?>" />
                    <?php wp_nonce_field('templates', 'fl-templates-nonce'); ?>
                </p>
            </form>
        </div>
        <!-- TEMPLATES -->
        <?php endif; ?>

	    <!-- POST TYPES -->
        <div id="fl-post-types-form" class="fl-settings-form">

            <h3 class="fl-settings-form-header"><?php _e('Post Types', 'fl-builder'); ?></h3>

            <form id="post-types-form" action="<?php echo admin_url('/options-general.php?page=fl-builder-settings#post-types'); ?>" method="post">

                <?php if(class_exists('FLBuilderMultisiteSettings')) : ?>
                <label>
                    <input class="fl-override-ms-cb" type="checkbox" name="fl-override-ms" value="1" <?php if(get_option('_fl_builder_post_types')) echo 'checked="checked"'; ?> />
                    <?php _e('Override network settings?', 'fl-builder'); ?>
                </label>
                <?php endif; ?>

                <div class="fl-settings-form-content">

                    <p><?php _e('Select the post types you would like the builder to work with.', 'fl-builder'); ?></p>
                    <p><?php _e('NOTE: Not all custom post types may be supported.', 'fl-builder'); ?></p>

                    <?php

                    $saved_post_types   = FLBuilderModel::get_post_types();
                    $post_types         = get_post_types(array('public' => true), 'objects');

                    foreach($post_types as $post_type) :

                        $checked = in_array($post_type->name, $saved_post_types) ? 'checked' : '';

                        if($post_type->name == 'attachment') {
                            continue;
                        }
                        if($post_type->name == 'fl-builder-template') {
                            continue;
                        }

                    ?>
                    <p>
                        <label>
                            <input type="checkbox" name="fl-post-types[]" value="<?php echo $post_type->name; ?>" <?php echo $checked; ?> />
                            <?php echo $post_type->labels->name; ?>
                        </label>
                    </p>
                    <?php endforeach; ?>
                </div>
                <p class="submit">
                    <input type="submit" name="update" class="button-primary" value="<?php esc_attr_e( 'Save Post Types', 'fl-builder' ); ?>" />
                    <?php wp_nonce_field('post-types', 'fl-post-types-nonce'); ?>
                </p>
            </form>
        </div>
        <!-- POST TYPES -->

	    <!-- EDITING -->
        <div id="fl-editing-form" class="fl-settings-form">

            <h3 class="fl-settings-form-header"><?php _e('Editing Settings', 'fl-builder'); ?></h3>

            <form id="editing-form" action="<?php echo admin_url('/options-general.php?page=fl-builder-settings#editing'); ?>" method="post">

                <?php if(class_exists('FLBuilderMultisiteSettings')) : ?>
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
        <!-- EDITING -->

        <?php if(file_exists(FL_BUILDER_DIR . 'includes/admin-branding.php')) : ?>
        <!-- BRANDING -->
        <?php include FL_BUILDER_DIR . 'includes/admin-branding.php'; ?>
        <!-- BRANDING -->
        <?php endif; ?>

        <!-- UNINSTALL -->
        <div id="fl-uninstall-form" class="fl-settings-form">

            <h3 class="fl-settings-form-header"><?php _e('Uninstall', 'fl-builder'); ?></h3>

            <p><?php _e('Clicking the button below will uninstall the page builder plugin and delete all of the data associated with it. You can uninstall or deactivate the page builder from the plugins page instead if you do not wish to delete the data.', 'fl-builder'); ?></p>

			<p><strong><?php _e( 'NOTE:', 'fl-builder' ); ?></strong> <?php _e( 'The builder does not delete the post meta <code>_fl_builder_data</code>, <code>_fl_builder_draft</code> and <code>_fl_builder_enabled</code> in case you want to reinstall it later. If you do, the builder will rebuild all of its data using those meta values.', 'fl-builder' ); ?></p>

            <?php if(is_multisite()) : ?>
            <p><strong style="color:#ff0000;"><?php _e( 'NOTE:', 'fl-builder' ); ?></strong> <?php _e('This applies to all sites on the network.', 'fl-builder'); ?></p>
            <?php endif; ?>

            <form id="uninstall-form" action="<?php echo admin_url('/options-general.php?page=fl-builder-settings'); ?>" method="post">
                <p>
                    <input type="submit" name="uninstall-submit" class="button button-primary" value="<?php _e('Uninstall', 'fl-builder'); ?>">
                    <?php wp_nonce_field('uninstall', 'fl-uninstall'); ?>
                </p>
            </form>
        </div>
        <!-- UNINSTALL -->

	</div>
</div>
<script type="text/javascript">

jQuery(function(){

    FLBuilderAdminSettings.strings = {
        uninstall: '<?php _e('Please type "uninstall" in the box below to confirm that you really want to uninstall the page builder and all of its data.', 'fl-builder'); ?>'
    };
});

</script>