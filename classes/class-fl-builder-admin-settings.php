<?php

/**
 * Handles logic for the admin settings page. 
 *
 * @since 1.0
 */
final class FLBuilderAdminSettings {
	
	/**
	 * Holds any errors that may arise from
	 * saving admin settings.
	 *
	 * @since 1.0
	 * @var array $errors
	 */
	static public $errors = array();
	
	/** 
	 * Adds the admin menu and enqueues CSS/JS if we are on
	 * the builder admin settings page.
	 *
	 * @since 1.0
	 * @return void
	 */
	static public function init()
	{
		add_action( 'admin_menu', 'FLBuilderAdminSettings::menu' );
			
		if ( isset( $_REQUEST['page'] ) && 'fl-builder-settings' == $_REQUEST['page'] ) {
			add_action( 'admin_enqueue_scripts', 'FLBuilderAdminSettings::styles_scripts' );
			self::save();
		}
	}
	
	/** 
	 * Enqueues the needed CSS/JS for the builder's admin settings page.
	 *
	 * @since 1.0
	 * @return void
	 */
	static public function styles_scripts()
	{
		// Styles
		wp_enqueue_style( 'fl-builder-admin-settings', FL_BUILDER_URL . 'css/fl-builder-admin-settings.css', array(), FL_BUILDER_VERSION );

		// Scripts
		wp_enqueue_script( 'fl-builder-admin-settings', FL_BUILDER_URL . 'js/fl-builder-admin-settings.js', array(), FL_BUILDER_VERSION );
		
		// Media Uploader
		wp_enqueue_media();
	}
	
	/** 
	 * Renders the admin settings menu.
	 *
	 * @since 1.0
	 * @return void
	 */
	static public function menu() 
	{
		if ( current_user_can( 'delete_users' ) ) {
			
			$title = FLBuilderModel::get_branding();
			$cap   = 'delete_users';
			$slug  = 'fl-builder-settings';
			$func  = 'FLBuilderAdminSettings::render';
			
			add_submenu_page( 'options-general.php', $title, $title, $cap, $slug, $func );
		}
	}
	
	/** 
	 * Renders the admin settings.
	 *
	 * @since 1.0
	 * @return void
	 */
	static public function render()
	{
		include FL_BUILDER_DIR . 'includes/admin-settings-js-config.php';
		include FL_BUILDER_DIR . 'includes/admin-settings.php';
	}
	
	/** 
	 * Renders the page class for network installs and single site installs.
	 *
	 * @since 1.0
	 * @return void
	 */
	static public function render_page_class()
	{
		if ( self::multisite_support() ) {
			echo 'fl-settings-network-admin';
		}
		else {
			echo 'fl-settings-single-install';
		}
	}
	
	/** 
	 * Renders the admin settings page heading.
	 *
	 * @since 1.0
	 * @return void
	 */
	static public function render_page_heading()
	{
		$icon = FLBuilderModel::get_branding_icon();
		$name = FLBuilderModel::get_branding();
		
		if ( ! empty( $icon ) ) {
			echo '<img src="' . $icon . '" />';
		}
		
		echo '<span>' . sprintf( _x( '%s Settings', '%s stands for custom branded "Page Builder" name.', 'fl-builder' ), FLBuilderModel::get_branding() ) . '</span>';
	}
	
	/** 
	 * Renders the update message.
	 *
	 * @since 1.0
	 * @return void
	 */	 
	static public function render_update_message()
	{
		if ( ! empty( self::$errors ) ) {
			foreach ( self::$errors as $message ) {
				echo '<div class="error"><p>' . $message . '</p></div>';
			}
		}
		else if( ! empty( $_POST ) && ! isset( $_POST['email'] ) ) {
			echo '<div class="updated"><p>' . __( 'Settings updated!', 'fl-builder' ) . '</p></div>';
		}
	}
	
	/** 
	 * Renders the nav items for the admin settings menu.
	 *
	 * @since 1.0
	 * @return void
	 */	  
	static public function render_nav_items()
	{
		$item_data = array(
			'license' => array(
				'title' => __( 'License', 'fl-builder' ),
				'show'	=> FL_BUILDER_LITE !== true && ( is_network_admin() || ! self::multisite_support() )
			),
			'upgrade' => array(
				'title' => __( 'Upgrade', 'fl-builder' ),
				'show'	=> FL_BUILDER_LITE === true
			),
			'modules' => array(
				'title' => __( 'Modules', 'fl-builder' ),
				'show'	=> true
			),
			'templates' => array(
				'title' => __( 'Templates', 'fl-builder' ),
				'show'	=> FL_BUILDER_LITE !== true
			),
			'post-types' => array(
				'title' => __( 'Post Types', 'fl-builder' ),
				'show'	=> true
			),
			'icons' => array(
				'title' => __( 'Icons', 'fl-builder' ),
				'show'	=> FL_BUILDER_LITE !== true
			),
			'editing' => array(
				'title' => __( 'Editing', 'fl-builder' ),
				'show'	=> true
			),
			'branding' => array(
				'title' => __( 'Branding', 'fl-builder' ),
				'show'	=> self::has_support( 'branding' ) && ( is_network_admin() || ! self::multisite_support() )
			),
			'help-button' => array(
				'title' => __( 'Help Button', 'fl-builder' ),
				'show'	=> self::has_support( 'help-button' ) && ( is_network_admin() || ! self::multisite_support() )
			),
			'cache' => array(
				'title' => __( 'Cache', 'fl-builder' ),
				'show'	=> true
			),
			'uninstall' => array(
				'title' => __( 'Uninstall', 'fl-builder' ),
				'show'	=> is_network_admin() || ! self::multisite_support()
			),
		);
		
		foreach ( $item_data as $key => $data ) {
			if ( $data['show'] ) {
				echo '<li><a href="#' . $key . '">' . $data['title'] . '</a></li>';
			}
		}
	}
	
	/** 
	 * Renders the admin settings forms.
	 *
	 * @since 1.0
	 * @return void
	 */	   
	static public function render_forms()
	{
		// License
		if ( is_network_admin() || ! self::multisite_support() )  {
			self::render_form( 'license' );
		}
		
		// Upgrade
		if ( FL_BUILDER_LITE === true )	 {
			self::render_form( 'upgrade' );
		}
		
		// Modules
		self::render_form( 'modules' );
		
		// Templates
		self::render_form( 'templates' );
		
		// Post Types
		self::render_form( 'post-types' );
		
		// Icons
		self::render_form( 'icons' );
		
		// Editing
		self::render_form( 'editing' );
		
		// Branding
		self::render_form( 'branding' );
		
		// Help Button
		self::render_form( 'help-button' );
		
		// Cache
		self::render_form( 'cache' );
		
		// Uninstall
		self::render_form( 'uninstall' );
	}
	
	/** 
	 * Renders an admin settings form based on the type specified.
	 *
	 * @since 1.0
	 * @param string $type The type of form to render.
	 * @return void
	 */	   
	static public function render_form( $type )
	{
		if ( self::has_support( $type ) ) {
			include FL_BUILDER_DIR . 'includes/admin-settings-' . $type . '.php';
		}
	}
	
	/** 
	 * Renders the action for a form.
	 *
	 * @since 1.0
	 * @param string $type The type of form being rendered.
	 * @return void
	 */	  
	static public function render_form_action( $type = '' )
	{
		if ( is_network_admin() ) {
			echo network_admin_url( '/settings.php?page=fl-builder-multisite-settings#' . $type );
		}
		else {
			echo admin_url( '/options-general.php?page=fl-builder-settings#' . $type );
		}
	}
	
	/** 
	 * Returns the action for a form.
	 *
	 * @since 1.0
	 * @param string $type The type of form being rendered.
	 * @return string The URL for the form action.
	 */	 
	static public function get_form_action( $type = '' )
	{
		if ( is_network_admin() ) {
			return network_admin_url( '/settings.php?page=fl-builder-multisite-settings#' . $type );
		}
		else {
			return admin_url( '/options-general.php?page=fl-builder-settings#' . $type );
		}
	}
	
	/** 
	 * Checks to see if a settings form is supported.
	 *
	 * @since 1.0
	 * @param string $type The type of form to check.
	 * @return bool
	 */ 
	static public function has_support( $type )
	{
		return file_exists( FL_BUILDER_DIR . 'includes/admin-settings-' . $type . '.php' );
	}
	
	/** 
	 * Checks to see if multisite is supported.
	 *
	 * @since 1.0
	 * @return bool
	 */ 
	static public function multisite_support()
	{
		return is_multisite() && class_exists( 'FLBuilderMultisiteSettings' );
	}
	
	/** 
	 * Adds an error message to be rendered.
	 *
	 * @since 1.0
	 * @param string $message The error message to add.
	 * @return void
	 */	 
	static public function add_error( $message )
	{
		self::$errors[] = $message;
	}
	
	/** 
	 * Saves the admin settings.
	 *
	 * @since 1.0
	 * @return void
	 */	 
	static public function save()
	{
		// Only admins can save settings.
		if(!current_user_can('delete_users')) {
			return;
		}
		
		self::save_enabled_modules();
		self::save_enabled_templates();
		self::save_enabled_post_types();
		self::save_enabled_icons();
		self::save_editing_capability();
		self::save_branding();
		self::save_help_button();
		self::clear_cache();
		self::uninstall();
	}
	
	/** 
	 * Saves the enabled modules.
	 *
	 * @since 1.0
	 * @access private
	 * @return void
	 */ 
	static private function save_enabled_modules()
	{
		if ( isset( $_POST['fl-modules-nonce'] ) && wp_verify_nonce( $_POST['fl-modules-nonce'], 'modules' ) ) {
			
			$modules = array();
			
			if ( is_array( $_POST['fl-modules'] ) ) {
				$modules = array_map( 'sanitize_text_field', $_POST['fl-modules'] );
			}
			
			FLBuilderModel::update_admin_settings_option( '_fl_builder_enabled_modules', $modules, true );
		}
	}
	
	/** 
	 * Saves the enabled templates.
	 *
	 * @since 1.0
	 * @since 1.5.7 Added the ability to enable the templates admin UI.
	 * @access private
	 * @return void
	 */ 
	static private function save_enabled_templates()
	{
		if ( isset( $_POST['fl-templates-nonce'] ) && wp_verify_nonce( $_POST['fl-templates-nonce'], 'templates' ) ) {
		
			$enabled_templates   = sanitize_text_field( $_POST['fl-template-settings'] );
			$admin_ui_enabled    = isset( $_POST['fl-template-admin-ui'] ) ? 1 : 0;
			
			FLBuilderModel::update_admin_settings_option( '_fl_builder_enabled_templates', $enabled_templates, true );
			FLBuilderModel::update_admin_settings_option( '_fl_builder_user_templates_admin', $admin_ui_enabled, true );
			
			if ( class_exists( 'FLBuilderTemplatesOverride' ) ) {
				FLBuilderTemplatesOverride::save_admin_settings();	
			}
		}
	}
	
	/** 
	 * Saves the enabled post types.
	 *
	 * @since 1.0
	 * @access private
	 * @return void
	 */ 
	static private function save_enabled_post_types()
	{
		if ( isset( $_POST['fl-post-types-nonce'] ) && wp_verify_nonce( $_POST['fl-post-types-nonce'], 'post-types' ) ) {
		
			if ( is_network_admin() ) {
				$post_types = sanitize_text_field( $_POST['fl-post-types'] );
				$post_types = str_replace( ' ', '', $post_types );
				$post_types = explode( ',', $post_types );
			}
			else {
				
				$post_types = array();
				
				if ( isset( $_POST['fl-post-types'] ) && is_array( $_POST['fl-post-types'] ) ) {
					$post_types = array_map( 'sanitize_text_field', $_POST['fl-post-types'] );
				}
			}
			
			FLBuilderModel::update_admin_settings_option( '_fl_builder_post_types', $post_types, true );
		}
	}
	
	/** 
	 * Saves the enabled icons.
	 *
	 * @since 1.0
	 * @access private
	 * @return void
	 */		 
	static private function save_enabled_icons()
	{
		if ( isset( $_POST['fl-icons-nonce'] ) && wp_verify_nonce( $_POST['fl-icons-nonce'], 'icons' ) ) {
			
			// Make sure we have at least one enabled icon set. 
			if ( ! isset( $_POST['fl-enabled-icons'] ) && empty( $_POST['fl-new-icon-set'] ) ) {
				self::add_error( __( "Error! You must have at least one icon set enabled.", 'fl-builder' ) );
				return;
			}
			
			$filesystem	   = FLBuilderUtils::get_filesystem();
			$enabled_icons = array();
			
			// Sanitize the enabled icons.
			if ( isset( $_POST['fl-enabled-icons'] ) && is_array( $_POST['fl-enabled-icons'] ) ) {
				$enabled_icons = array_map( 'sanitize_text_field', $_POST['fl-enabled-icons'] );
			}
			
			// Update the enabled sets.
			self::update_enabled_icons( $enabled_icons );
			
			// Delete a set? 
			if ( ! empty( $_POST['fl-delete-icon-set'] ) ) {
				
				$sets  = FLBuilderIcons::get_sets();
				$key   = sanitize_text_field( $_POST['fl-delete-icon-set'] );
				$index = array_search( $key, $enabled_icons );
				
				if ( false !== $index ) {
					unset( $enabled_icons[ $index ] );
				}
				if ( isset( $sets[ $key ] ) ) {
					$filesystem->rmdir( $sets[ $key ]['path'], true );
					FLBuilderIcons::remove_set( $key );
				}
			}
			
			// Upload a new set?
			if ( ! empty( $_POST['fl-new-icon-set'] ) ) {

				$dir		 = FLBuilderModel::get_cache_dir( 'icons' );
				$id			 = (int) $_POST['fl-new-icon-set'];
				$path		 = get_attached_file( $id );
				$new_path	 = $dir['path'] . 'icon-' . time() . '/';
				$unzipped	 = unzip_file( $path, $new_path );
				
				// Unzip failed.
				if ( ! $unzipped ) {
					self::add_error( __( "Error! Could not unzip file.", 'fl-builder' ) );
					return;
				}
				
				// Move files if unzipped into a subfolder.
				$files = $filesystem->dirlist( $new_path );
				
				if ( 1 == count( $files ) ) {
					
					$values			= array_values( $files );
					$subfolder_info = array_shift( $values );
					$subfolder		= $new_path . $subfolder_info['name'] . '/';
					
					if ( file_exists( $subfolder ) && is_dir( $subfolder ) ) {
						
						$files = $filesystem->dirlist( $subfolder );
						
						if ( $files ) {
							foreach ( $files as $file ) {
								$filesystem->move( $subfolder . $file['name'], $new_path . $file['name'] );
							}
						}
						
						$filesystem->rmdir( $subfolder );
					}
				}
				
				// Check for supported sets.
				$is_icomoon	 = file_exists( $new_path . 'selection.json' );
				$is_fontello = file_exists( $new_path . 'config.json' );
				
				// Show an error if we don't have a supported icon set.
				if ( ! $is_icomoon && ! $is_fontello ) {
					$filesystem->rmdir( $new_path, true );
					self::add_error( __( "Error! Please upload an icon set from either Icomoon or Fontello.", 'fl-builder' ) );
					return;
				}
				
				// Enable the new set. 
				if ( is_array( $enabled_icons ) ) {
					$key = FLBuilderIcons::get_key_from_path( $new_path );
					$enabled_icons[] = $key;
				}
			}
			
			// Update the enabled sets again in case they have changed.
			self::update_enabled_icons( $enabled_icons );
		}
	}
	
	/** 
	 * Updates the enabled icons in the database.
	 *
	 * @since 1.0
	 * @access private
	 * @return void
	 */ 
	static private function update_enabled_icons( $enabled_icons = array() )
	{
		FLBuilderModel::update_admin_settings_option( '_fl_builder_enabled_icons', $enabled_icons, true );
	}
	
	/** 
	 * Saves the editing capability.
	 *
	 * @since 1.0
	 * @access private
	 * @return void
	 */ 
	static private function save_editing_capability()
	{
		if ( isset( $_POST['fl-editing-nonce'] ) && wp_verify_nonce( $_POST['fl-editing-nonce'], 'editing' ) ) {
			
			$capability = sanitize_text_field( $_POST['fl-editing-capability'] );
			
			FLBuilderModel::update_admin_settings_option( '_fl_builder_editing_capability', $capability, true );
		}
	}
	
	/** 
	 * Saves the branding settings.
	 *
	 * @since 1.0
	 * @access private
	 * @return void
	 */ 
	static private function save_branding()
	{
		if ( isset( $_POST['fl-branding-nonce'] ) && wp_verify_nonce( $_POST['fl-branding-nonce'], 'branding' ) ) {
			
			$branding		= wp_kses_post( $_POST['fl-branding'] );
			$branding_icon	= sanitize_text_field( $_POST['fl-branding-icon'] );
			
			FLBuilderModel::update_admin_settings_option( '_fl_builder_branding', $branding, false );
			FLBuilderModel::update_admin_settings_option( '_fl_builder_branding_icon', $branding_icon, false );
		}
	}
	
	/** 
	 * Saves the help button settings.
	 *
	 * @since 1.0
	 * @access private
	 * @return void
	 */ 
	static private function save_help_button()
	{
		if ( isset( $_POST['fl-help-button-nonce'] ) && wp_verify_nonce( $_POST['fl-help-button-nonce'], 'help-button' ) ) {
			
			$settings					= FLBuilderModel::get_help_button_defaults();
			$settings['enabled']		= isset( $_POST['fl-help-button-enabled'] )		? true : false;
			$settings['tour']			= isset( $_POST['fl-help-tour-enabled'] )		? true : false;
			$settings['video']			= isset( $_POST['fl-help-video-enabled'] )		? true : false;
			$settings['knowledge_base'] = isset( $_POST['fl-knowledge-base-enabled'] )	? true : false;
			$settings['forums']			= isset( $_POST['fl-forums-enabled'] )			? true : false;
			
			// Disable everything if the main button is disabled.
			if ( ! $settings['enabled'] ) {
				$settings['tour']			= false;
				$settings['video']			= false;
				$settings['knowledge_base'] = false;
				$settings['forums']			= false;
			}
			
			// Clean the video embed.
			$video_embed = wp_kses( $_POST['fl-help-video-embed'], array(
				'iframe' => array(
					'src'					=> array(),
					'frameborder'			=> array(),
					'webkitallowfullscreen' => array(),
					'mozallowfullscreen'	=> array(),
					'allowfullscreen'		=> array()
				)
			));
			
			// Save the video embed.
			if ( ! empty( $video_embed ) && ! stristr( $video_embed, 'iframe' ) ) {
				self::add_error( __( "Error! Please enter an iframe for the video embed code.", 'fl-builder' ) );
			}
			else if ( ! empty( $video_embed ) ) {
				$settings['video_embed'] = $video_embed;
			}
			
			// Save the knowledge base URL.
			if ( ! empty( $_POST['fl-knowledge-base-url'] ) ) {
				$settings['knowledge_base_url'] = sanitize_text_field( $_POST['fl-knowledge-base-url'] );
			}
			
			// Save the forums URL.
			if ( ! empty( $_POST['fl-forums-url'] ) ) {
				$settings['forums_url'] = sanitize_text_field( $_POST['fl-forums-url'] );
			}
			
			// Make sure we have at least one help feature enabled.
			if ( $settings['enabled'] && ! $settings['tour'] && ! $settings['video'] && ! $settings['knowledge_base'] && ! $settings['forums'] ) {
				self::add_error( __( "Error! You must have at least one feature of the help button enabled.", 'fl-builder' ) );
				return;
			}
			
			FLBuilderModel::update_admin_settings_option( '_fl_builder_help_button', $settings, false );
		}
	}

	/** 
	 * Clears the builder cache.
	 *
	 * @since 1.5.3
	 * @access private
	 * @return void
	 */ 
	static private function clear_cache()
	{
		if ( ! current_user_can( 'delete_users' ) ) {
			return; 
		}
		else if ( isset( $_POST['fl-cache-nonce'] ) && wp_verify_nonce( $_POST['fl-cache-nonce'], 'cache' ) ) {
			if ( is_network_admin() ) {
				self::clear_cache_for_all_sites();
			}
			else {
				FLBuilderModel::delete_all_asset_cache();
			}
		}
	}

	/** 
	 * Clears the builder cache for all sites on a network.
	 *
	 * @since 1.5.3
	 * @access private
	 * @return void
	 */ 
	static private function clear_cache_for_all_sites()
	{
		global $blog_id;
		global $wpdb;
		
		// Save the original blog id.
		$original_blog_id = $blog_id;
		
		// Get all blog ids.
		$blog_ids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );
		
		// Loop through the blog ids and clear the cache.
		foreach ( $blog_ids as $id ) {
			switch_to_blog( $id );
			FLBuilderModel::delete_all_asset_cache();
		}
		
		// Revert to the original blog.
		switch_to_blog( $original_blog_id );
	}

	/** 
	 * Uninstalls the builder and all of its data.
	 *
	 * @since 1.0
	 * @access private
	 * @return void
	 */ 
	static private function uninstall()
	{
		if ( ! current_user_can( 'delete_plugins' ) ) {
			return; 
		}
		else if ( isset( $_POST['fl-uninstall'] ) && wp_verify_nonce( $_POST['fl-uninstall'], 'uninstall' ) ) {
			if ( is_multisite() && class_exists( 'FLBuilderMultisite' ) ) {
				FLBuilderMultisite::uninstall();
			}
			else {
				FLBuilderAdmin::uninstall();
			}
		}
	}
}