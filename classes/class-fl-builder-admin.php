<?php

/**
 * Main builder admin class.
 *
 * @class FLBuilderAdmin
 */
final class FLBuilderAdmin {

	/**
     * @method activate
     */
    static public function activate()
    {
        global $wp_version;

        // Check for WordPress 3.5 and above.
        if(version_compare($wp_version, '3.5', '>=')) {

            // Check for multisite.
            if(is_multisite()) {

                // Init multisite support.
                self::init_classes();
                self::init_multisite();

                // This version has multisite support.
                if(class_exists('FLBuilderMultisite')) {

                    if(is_network_admin()) {
                        FLBuilderMultisite::install();
                    }
                    else {
                        self::install();
                    }
                }
                // This version doesn't have multisite support.
                else {
					self::show_activate_error( sprintf( __( 'This version of the <strong>Page Builder</strong> plugin is not compatible with WordPress Multisite. <a%s>Please upgrade</a> to the Multisite version of this plugin.', 'fl-builder' ), ' href="' . esc_url( FL_BUILDER_UPGRADE_URL ) . '" target="_blank"' ) );
                }
            }
            // No multisite, standard install.
            else {
                self::install();
            }
        }
        // Wrong WordPress version.
        else {
            self::show_activate_error(__('The <strong>Page Builder</strong> plugin requires WordPress version 3.5 or greater. Please update WordPress before activating the plugin.', 'fl-builder'));
        }

        // Success! Trigger the activation notice.
        if(FL_BUILDER_LITE !== true) {
            update_site_option('_fl_builder_activation_admin_notice', true);
        }
    }

	/**
     * @method show_activate_error
     */
    static public function show_activate_error($message)
    {
        deactivate_plugins(plugin_basename(FL_BUILDER_DIR . 'fl-builder.php'), false, is_network_admin());

        die($message);
    }

	/**
     * @method show_activate_notice
     */
    static public function show_activate_notice()
    {
    	$notice = get_site_option('_fl_builder_activation_admin_notice');

        if($notice) {
            add_action('admin_notices', 'FLBuilderAdmin::activate_notice');
            add_action('network_admin_notices', 'FLBuilderAdmin::activate_notice');
            delete_site_option('_fl_builder_activation_admin_notice');
        }
    }

	/**
     * @method activate_notice
     */
    static public function activate_notice()
    {
    	$href = esc_url(admin_url('/options-general.php?page=fl-builder-settings#license'));

    	echo '<div class="updated" style="background: #d3ebc1;">';
		echo '<p><strong>' . sprintf( __( 'Page Builder activated! <a%s>Click here</a> to enable remote updates.', 'fl-builder' ), ' href="' . esc_url( $href ) . '"' ) . '</strong></p>';
	    echo '</div>';
    }

	/**
     * @method install
     */
    static public function install() {}

	/**
     * @method uninstall
     */
    static public function uninstall()
    {
        FLBuilderModel::uninstall_database();
    }

    /**
     * @method init
     */
    static public function init()
    {
        self::show_activate_notice();
        self::init_classes();
        self::init_settings();
        self::init_multisite();
        self::init_templates();
    }

    /**
     * @method init_classes
     */
    static public function init_classes()
    {
        $templates_class    = FL_BUILDER_DIR . 'classes/class-fl-builder-templates.php';
        $ms_class           = FL_BUILDER_DIR . 'classes/class-fl-builder-multisite.php';
        $ms_settings_class  = FL_BUILDER_DIR . 'classes/class-fl-builder-multisite-settings.php';

        if(file_exists($templates_class)) {
            require_once $templates_class;
        }
        if(is_multisite()) {

            if(file_exists($ms_class)) {
                require_once $ms_class;
            }
            if(file_exists($ms_settings_class) && FL_BUILDER_VERSION != '{FL_BUILDER_VERSION}') {
                require_once $ms_settings_class;
            }
        }

        require_once FL_BUILDER_DIR . 'classes/class-fl-builder-admin-settings.php';
    }

    /**
     * @method init_settings
     */
    static public function init_settings()
    {
        FLBuilderAdminSettings::init();
    }

    /**
     * @method init_multisite
     */
    static public function init_multisite()
    {
        if(is_multisite()) {

            if(class_exists('FLBuilderMultisite')) {
                FLBuilderMultisite::init();
            }
            if(class_exists('FLBuilderMultisiteSettings')) {
                FLBuilderMultisiteSettings::init();
            }
        }
    }

    /**
     * @method init_templates
     */
    static public function init_templates()
    {
        if(class_exists('FLBuilderTemplates')) {
            FLBuilderTemplates::init();
        }
    }

	/**
     * @method render_plugin_action_links
     */
	static public function render_plugin_action_links($actions)
    {
    	if(FL_BUILDER_LITE === true) {
			$actions[] = '<a href="' . FL_BUILDER_UPGRADE_URL . '" style="color:#3db634;" target="_blank">' . _x( 'Upgrade', 'Plugin action link label.', 'fl-builder' ) . '</a>';
    	}

    	return $actions;
    }

	/**
     * @method white_label_plugins_page
     */
	static public function white_label_plugins_page($plugins)
    {
        $branding = FLBuilderModel::get_branding();

        if($branding != __('Page Builder', 'fl-builder')) {

            if(isset($plugins['fl-builder/fl-builder.php'])) {
                $plugins['fl-builder/fl-builder.php']['Name']       = $branding;
                $plugins['fl-builder/fl-builder.php']['Title']      = $branding;
                $plugins['fl-builder/fl-builder.php']['Author']     = '';
                $plugins['fl-builder/fl-builder.php']['AuthorName'] = '';
                $plugins['fl-builder/fl-builder.php']['PluginURI']  = '';
            }
            else if(isset($plugins['bb-plugin/fl-builder.php'])) {
                $plugins['bb-plugin/fl-builder.php']['Name']        = $branding;
                $plugins['bb-plugin/fl-builder.php']['Title']       = $branding;
                $plugins['bb-plugin/fl-builder.php']['Author']      = '';
                $plugins['bb-plugin/fl-builder.php']['AuthorName']  = '';
                $plugins['bb-plugin/fl-builder.php']['PluginURI']   = '';
            }
        }

        return $plugins;
    }
}