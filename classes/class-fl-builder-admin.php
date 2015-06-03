<?php

/**
 * Main builder admin class.
 *
 * @since 1.0
 */
final class FLBuilderAdmin {

	/**
	 * Called on plugin activation and checks to see if the correct 
	 * WordPress version is installed and multisite is supported. If 
	 * all checks are passed the install method is called.
	 *
	 * @since 1.0
	 * @return void
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
					$url = FLBuilderModel::get_upgrade_url( array( 'utm_source' => 'external', 'utm_medium' => 'builder', 'utm_campaign' => 'no-multisite-support' ) );
					self::show_activate_error( sprintf( __( 'This version of the <strong>Page Builder</strong> plugin is not compatible with WordPress Multisite. <a%s>Please upgrade</a> to the Multisite version of this plugin.', 'fl-builder' ), ' href="' . $url . '" target="_blank"' ) );
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
	 * Show a message if there is an activation error and 
	 * deactivates the plugin.
	 *
	 * @since 1.0
	 * @param string $message The message to show.
	 * @return void
	 */
	static public function show_activate_error($message)
	{
		deactivate_plugins( FLBuilderModel::plugin_basename(), false, is_network_admin() );

		die( $message );
	}

	/**
	 * Sets the action to show the activation success message.
	 *
	 * @since 1.0
	 * @return void
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
	 * Shows the activation success message.
	 *
	 * @since 1.0
	 * @return void
	 */
	static public function activate_notice()
	{
		if ( class_exists('FLBuilderMultisiteSettings') && is_multisite() && current_user_can( 'manage_network_plugins' ) ) {
			$href = esc_url( network_admin_url( '/settings.php?page=fl-builder-multisite-settings#license' ) );
		}
		else {
			$href = esc_url( admin_url( '/options-general.php?page=fl-builder-settings#license' ) );
		}
		
		echo '<div class="updated" style="background: #d3ebc1;">';
		echo '<p><strong>' . sprintf( __( 'Page Builder activated! <a%s>Click here</a> to enable remote updates.', 'fl-builder' ), ' href="' . esc_url( $href ) . '"' ) . '</strong></p>';
		echo '</div>';
	}

	/**
	 * Installs the builder upon successful activation. 
	 * Currently not used but may be in the future.
	 *
	 * @since 1.0
	 * @return void
	 */
	static public function install() {}

	/**
	 * Uninstalls the builder.
	 *
	 * @since 1.0
	 * @return void
	 */
	static public function uninstall()
	{
		FLBuilderModel::uninstall_database();
	}

	/**
	 * Initializes builder logic for wp-admin.
	 *
	 * @since 1.0
	 * @return void
	 */
	static public function init()
	{
		self::init_classes();
		self::init_settings();
		self::init_multisite();
		self::init_templates();
		self::show_activate_notice();
	}

	/**
	 * Loads builder classes if they exist.
	 *
	 * @since 1.0
	 * @return void
	 */
	static public function init_classes()
	{
		$templates_class	= FL_BUILDER_DIR . 'classes/class-fl-builder-template-settings.php';
		$overrides_class	= FL_BUILDER_DIR . 'classes/class-fl-builder-templates-override.php';
		$ms_class			= FL_BUILDER_DIR . 'classes/class-fl-builder-multisite.php';
		$ms_settings_class	= FL_BUILDER_DIR . 'classes/class-fl-builder-multisite-settings.php';

		if(file_exists($templates_class)) {
			require_once $templates_class;
		}
		if(file_exists($overrides_class)) {
			require_once $overrides_class;
		}
		if(is_multisite()) {

			if(file_exists($ms_class)) {
				require_once $ms_class;
			}
			if(file_exists($ms_settings_class)) {
				require_once $ms_settings_class;
			}
		}

		require_once FL_BUILDER_DIR . 'classes/class-fl-builder-admin-settings.php';
	}

	/**
	 * Initializes the builder admin settings page.
	 *
	 * @since 1.0
	 * @return void
	 */
	static public function init_settings()
	{
		FLBuilderAdminSettings::init();
	}

	/**
	 * Initializes the builder network admin settings page
	 * and additional multisite logic.
	 *
	 * @since 1.0
	 * @return void
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
	 * Initializes the interface for core builder templates.
	 *
	 * @since 1.0
	 * @return void
	 */
	static public function init_templates()
	{
		if(class_exists('FLBuilderTemplates')) {
			FLBuilderTemplates::init();
		}
	}

	/**
	 * Renders the link for the row actions on the plugins page.
	 *
	 * @since 1.0
	 * @param array $actions An array of row action links.
	 * @return array
	 */
	static public function render_plugin_action_links($actions)
	{
		if(FL_BUILDER_LITE === true) {
			$url = FLBuilderModel::get_upgrade_url( array( 'utm_source' => 'external', 'utm_medium' => 'builder', 'utm_campaign' => 'plugins-page' ) );
			$actions[] = '<a href="' . $url . '" style="color:#3db634;" target="_blank">' . _x( 'Upgrade', 'Plugin action link label.', 'fl-builder' ) . '</a>';
		}

		return $actions;
	}

	/**
	 * White labels the builder on the plugins page.
	 *
	 * @since 1.0
	 * @param array $plugins An array data for each plugin.
	 * @return array
	 */
	static public function white_label_plugins_page($plugins)
	{
		$default  = __( 'Page Builder', 'fl-builder' );
		$branding = FLBuilderModel::get_branding();
		$key	  = FLBuilderModel::plugin_basename();

		if ( isset( $plugins[ $key ] ) && $branding != $default ) {
			$plugins[ $key ]['Name']	   = $branding;
			$plugins[ $key ]['Title']	   = $branding;
			$plugins[ $key ]['Author']	   = '';
			$plugins[ $key ]['AuthorName'] = '';
			$plugins[ $key ]['PluginURI']  = '';
		}

		return $plugins;
	}
}