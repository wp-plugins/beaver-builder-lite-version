<?php

/**
 * Main builder class.
 *
 * @since 1.0
 */
final class FLBuilder {

	/**
	 * Localization
	 *
	 * Load the translation file for current language. Checks the default WordPress
	 * languages folder first and then the languages folder inside the plugin.
	 *
	 * @since 1.4.4
	 * @return string|bool The translation file path or false if none is found.
	 */
	static public function load_plugin_textdomain()
	{
		//Traditional WordPress plugin locale filter
		$locale = apply_filters( 'plugin_locale', get_locale(), 'fl-builder' );

		//Setup paths to current locale file
		$mofile_local  = trailingslashit( FL_BUILDER_DIR ) . 'languages/' . $locale . '.mo';
		$mofile_global = trailingslashit( WP_LANG_DIR ) . 'plugins/beaver-builder/' . $locale . '.mo';

		if ( file_exists( $mofile_local ) ) {
			//Look in local /wp-content/plugins/beaver-builder/languages/ folder
			return load_textdomain( 'fl-builder', $mofile_local );
		} 
		else if ( file_exists( $mofile_global ) ) {
			//Look in global /wp-content/languages/plugins/beaver-builder/ folder
			return load_textdomain( 'fl-builder', $mofile_global );
		}

		//Nothing found
		return false;
	}

	/**
	 * Front-end AJAX handler for the builder interface. We use this 
	 * instead of wp_ajax because that only works in the admin and 
	 * certain things like some shortcodes won't render there. AJAX
	 * requests handled through this method only run for logged in users
	 * for extra security. Developers creating custom modules that need
	 * AJAX should use wp_ajax instead.
	 *
	 * @since 1.0
	 * @return void
	 */
	static public function ajax()
	{
		// Only run for logged in users.
		if(is_user_logged_in()) {

			// Get builder post data.
			$post_data = FLBuilderModel::get_post_data();

			// Get the action.
			if(!empty($_REQUEST['fl_action'])) {
				$action = $_REQUEST['fl_action'];
			}
			else if(!empty($post_data['fl_action'])) {
				$action = $post_data['fl_action'];
			}
			else {
				return;
			}

			// Run the action.
			define('DOING_AJAX', true);
			do_action('fl_ajax_' . $action);
			die('0');
		}
	}

	/**
	 * Initializes the builder interface.
	 *
	 * @since 1.0
	 * @return void
	 */
	static public function init()
	{
		// Enable editing if the builder is active.
		if ( FLBuilderModel::is_builder_active() && ! defined( 'DOING_AJAX' ) ) {
			
			// Tell W3TC not to minify while the builder is active.
			define( 'DONOTMINIFY', true );

			// Remove 3rd party editor buttons.
			remove_all_actions('media_buttons', 999999);
			remove_all_actions('media_buttons_context', 999999);

			// Get the post.
			require_once ABSPATH . 'wp-admin/includes/post.php';
			$post_id = FLBuilderModel::get_post_id();

			// Check to see if the post is locked.
			if(wp_check_post_lock($post_id) !== false) {
				header('Location: ' . admin_url('/post.php?post=' . $post_id . '&action=edit'));
			}
			else {
				FLBuilderModel::enable_editing();
			}
		}
	}

	/**
	 * Alias method for registering a module with the builder.
	 *
	 * @since 1.0
	 * @param string $class The module's PHP class name.
	 * @param array $form The module's settings form data.
	 * @return void
	 */
	static public function register_module($class, $form)
	{
		FLBuilderModel::register_module($class, $form);
	}

	/**
	 * Alias method for registering a settings form with the builder.
	 *
	 * @since 1.0
	 * @param string $id The form's ID.
	 * @param array $form The form data.
	 * @return void
	 */
	static public function register_settings_form($id, $form)
	{
		FLBuilderModel::register_settings_form($id, $form);
	}

	/**
	 * Send no cache headers when the builder interface is active.
	 *
	 * @since 1.0
	 * @return void
	 */
	static public function no_cache_headers()
	{
		if(isset($_GET['fl_builder'])) {
			header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
			header('Cache-Control: no-store, no-cache, must-revalidate');
			header('Cache-Control: post-check=0, pre-check=0', false);
			header('Pragma: no-cache');
			header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');
		}
	}

	/**
	 * Set the default text editor to tinymce when the builder is active.
	 *
	 * @since 1.0
	 * @param string $type The current default editor type.
	 * @return string
	 */
	static public function default_editor($type)
	{
		return FLBuilderModel::is_builder_active() ? 'tinymce' : $type;
	}

	/**
	 * Add custom CSS for the builder to the text editor.
	 *
	 * @since 1.0
	 * @param string $mce_css
	 * @return string
	 */
	static public function add_editor_css($mce_css)
	{
		if(FLBuilderModel::is_builder_active()) {

			if(!empty($mce_css)) {
				$mce_css .= ',';
			}

			$mce_css .= FL_BUILDER_URL . 'css/editor.css';
		}

		return $mce_css;
	}

	/**
	 * Add additional buttons to the text editor.
	 *
	 * @since 1.0
	 * @param array $buttons The current buttons array.
	 * @return array
	 */
	static public function editor_buttons_2($buttons)
	{
		if(FLBuilderModel::is_builder_active()) {

			array_shift($buttons);
			array_unshift($buttons, 'fontsizeselect');
			array_unshift($buttons, 'formatselect');

			if(($key = array_search('wp_help', $buttons)) !== false) {
				unset($buttons[$key]);
			}
		}

		return $buttons;
	}

	/**
	 * Custom font size options for the editor font size select.
	 *
	 * @since 1.6.3
	 * @param array $init The TinyMCE init array.
	 * @return array
	 */
	static public function editor_font_sizes( $init )
	{
		if ( FLBuilderModel::is_builder_active() ) {
			$init['fontsize_formats'] = implode( ' ', array(
				'10px',
				'12px',
				'14px',
				'16px',
				'18px',
				'20px',
				'22px',
				'24px',
				'26px',
				'28px',
				'30px',
				'32px',
				'34px',
				'36px',
				'38px',
				'40px',
				'42px',
				'44px',
				'46px',
				'48px',
			));
		}

		return $init;
	}

	/**
	 * Only allows certain text editor plugins to avoid conflicts
	 * with third party plugins.
	 *
	 * @since 1.0
	 * @param array $plugins The current editor plugins.
	 * @return array
	 */
	static public function editor_external_plugins($plugins)
	{
		if(FLBuilderModel::is_builder_active()) {

			$allowed = array(
				'anchor',
				'code',
				'insertdatetime',
				'nonbreaking',
				'print',
				'searchreplace',
				'table',
				'visualblocks',
				'visualchars',
				'emoticons',
				'advlist',
				'wptadv',
			);

			foreach($plugins as $key => $val) {
				if(!in_array($key, $allowed)) {
					unset($plugins[$key]);
				}
			}
		}

		return $plugins;
	}

	/**
	 * Include a jQuery fallback script when the builder is
	 * enabled for a page.
	 *
	 * @since 1.0
	 * @return void
	 */
	static public function include_jquery()
	{
		if(FLBuilderModel::is_builder_enabled()) {
			include FL_BUILDER_DIR . 'includes/jquery.php';
		}
	}

	/**
	 * Register and enqueue the styles and scripts for all builder 
	 * layouts in the main WordPress query.
	 *
	 * @since 1.0
	 * @return void
	 */
	static public function layout_styles_scripts()
	{
		global $wp_query;
		global $post;

		$original_post  = $post;
		$ver            = FL_BUILDER_VERSION;
		$css_url 		= plugins_url('/css/', FL_BUILDER_FILE);
		$js_url  		= plugins_url('/js/', FL_BUILDER_FILE);

		// Register additional CSS
		wp_register_style('font-awesome',           $css_url . 'font-awesome.min.css', array(), $ver);
		wp_register_style('foundation-icons',       $css_url . 'foundation-icons.css', array(), $ver);
		wp_register_style('fl-slideshow',           $css_url . 'fl-slideshow.css', array(), $ver);
		wp_register_style('jquery-bxslider',        $css_url . 'jquery.bxslider.css', array(), $ver);
		wp_register_style('jquery-magnificpopup',   $css_url . 'jquery.magnificpopup.css', array(), $ver);

		// Register additional JS
		wp_register_script('fl-slideshow',          $js_url . 'fl-slideshow.js', array('yui3'), $ver, true);
		wp_register_script('fl-gallery-grid',       $js_url . 'fl-gallery-grid.js', array('jquery'), $ver, true);
		wp_register_script('jquery-bxslider',       $js_url . 'jquery.bxslider.min.js', array('jquery-easing', 'jquery-fitvids'), $ver, true);
		wp_register_script('jquery-easing',         $js_url . 'jquery.easing.1.3.js', array('jquery'), '1.3', true);
		wp_register_script('jquery-fitvids',        $js_url . 'jquery.fitvids.js', array('jquery'), $ver, true);
		wp_register_script('jquery-infinitescroll', $js_url . 'jquery.infinitescroll.js', array('jquery'), $ver, true);
		wp_register_script('jquery-magnificpopup',  $js_url . 'jquery.magnificpopup.min.js', array('jquery'), $ver, true);
		wp_register_script('jquery-mosaicflow',     $js_url . 'jquery.mosaicflow.min.js', array('jquery'), $ver, true);
		wp_register_script('jquery-waypoints',      $js_url . 'jquery.waypoints.min.js', array('jquery'), $ver, true);
		wp_register_script('jquery-wookmark',       $js_url . 'jquery.wookmark.min.js', array('jquery'), $ver, true);

		// YUI 3 (Needed for the slideshow)
		if(FLBuilderModel::is_ssl()) {
			wp_register_script('yui3', 'https://yui-s.yahooapis.com/3.5.1/build/yui/yui-min.js', array(), '3.5.1', false);
		}
		else {
			wp_register_script('yui3', 'http://yui.yahooapis.com/3.5.1/build/yui/yui-min.js', array(), '3.5.1', false);
		}

		// Enqueue assets for posts in the main query.
		if ( isset( $wp_query->posts ) ) {
			foreach ( $wp_query->posts as $post ) {
				self::enqueue_layout_styles_scripts( $post->ID );
			}
		}

		// Enqueue assets for posts via the fl_builder_global_posts filter.
		$post_ids = FLBuilderModel::get_global_posts();

		if(count($post_ids) > 0) {

			$posts = get_posts(array(
				'post__in' 			=> $post_ids, 
				'post_type' 		=> 'any',
				'posts_per_page'	=> -1
			));

			foreach($posts as $post) {
				self::enqueue_layout_styles_scripts($post->ID);
			}
		}

		// Reset the global post variable.
		$post = $original_post;
	}

	/**
	 * Enqueue the styles and scripts for a single layout.
	 *
	 * @since 1.0
	 * @param int $post_id The post ID for this layout.
	 * @return void
	 */
	static public function enqueue_layout_styles_scripts($post_id)
	{
		if(FLBuilderModel::is_builder_enabled()) {

			$nodes 		= FLBuilderModel::get_categorized_nodes();
			$asset_info = FLBuilderModel::get_asset_info();
			$asset_ver  = FLBuilderModel::get_asset_version();

			// Enqueue required row CSS and JS
			foreach($nodes['rows'] as $row) {
				if($row->settings->bg_type == 'slideshow') {
					wp_enqueue_script('yui3');
					wp_enqueue_script('fl-slideshow');
					wp_enqueue_style('fl-slideshow');
				}
			}

			// Enqueue required module CSS and JS
			foreach($nodes['modules'] as $module) {

				$module->enqueue_icon_styles();
				$module->enqueue_font_styles();
				$module->enqueue_scripts();

				foreach($module->css as $handle => $props) {
					wp_enqueue_style($handle, $props[0], $props[1], $props[2], $props[3]);
				}
				foreach($module->js as $handle => $props) {
					wp_enqueue_script($handle, $props[0], $props[1], $props[2], $props[3]);
				}
				if(!empty($module->settings->animation)) {
					wp_enqueue_script('jquery-waypoints');
				}
			}

			// Enqueue main CSS
			if(!file_exists($asset_info['css'])) {
				FLBuilder::render_css();
			}
			
			$deps 	= apply_filters( 'fl_builder_layout_style_dependencies', array() );
			$media 	= apply_filters( 'fl_builder_layout_style_media', 'all' );

			wp_enqueue_style('fl-builder-layout-' . $post_id, $asset_info['css_url'], $deps, $asset_ver, $media);

			// Enqueue Google Fonts
			FLBuilderFonts::enqueue_styles();

			// Enqueue main JS
			if(!file_exists($asset_info['js'])) {
				FLBuilder::render_js();
			}

			wp_enqueue_script('fl-builder-layout-' . $post_id, $asset_info['js_url'], array('jquery'), $asset_ver, true);
		}
	}

	/**
	 * Enqueue the styles and scripts for the builder interface.
	 *
	 * @since 1.0
	 * @return void
	 */
	static public function styles_scripts()
	{
		if(FLBuilderModel::is_builder_active()) {

			$ver     = FL_BUILDER_VERSION;
			$css_url = plugins_url('/css/', FL_BUILDER_FILE);
			$js_url  = plugins_url('/js/', FL_BUILDER_FILE);

			/* We have a custom version of sortable that fixes a bug. */
			wp_deregister_script('jquery-ui-sortable');

			/* Frontend builder styles */
			wp_enqueue_style('dashicons');
			wp_enqueue_style('font-awesome');
			wp_enqueue_style('foundation-icons');
			wp_enqueue_style('jquery-nanoscroller',     $css_url . 'jquery.nanoscroller.css', array(), $ver);
			wp_enqueue_style('jquery-autosuggest',      $css_url . 'jquery.autoSuggest.min.css', array(), $ver);
			wp_enqueue_style('jquery-tiptip',           $css_url . 'jquery.tiptip.css', array(), $ver);
			wp_enqueue_style('bootstrap-tour',          $css_url . 'bootstrap-tour-standalone.min.css', array(), $ver);
			wp_enqueue_style('fl-color-picker',         $css_url . 'fl-color-picker.css', array(), $ver);
			wp_enqueue_style('fl-lightbox',             $css_url . 'fl-lightbox.css', array(), $ver);
			wp_enqueue_style('fl-icon-selector',        $css_url . 'fl-icon-selector.css', array(), $ver);
			wp_enqueue_style('fl-builder',              $css_url . 'fl-builder.css', array(), $ver);
			
			/* Custom Icons */
			FLBuilderIcons::enqueue_all_custom_icons_styles();

			/* RTL Support */
			if(is_rtl()) {
				wp_enqueue_style('fl-builder-rtl',      $css_url . 'fl-builder-rtl.css', array(), $ver);
			}

			/* Frontend builder scripts */
			wp_enqueue_media();
			wp_enqueue_script('heartbeat');
			wp_enqueue_script('wpdialogs');
			wp_enqueue_script('wpdialogs-popup');
			wp_enqueue_script('wplink');
			wp_enqueue_script('editor');
			wp_enqueue_script('quicktags');
			wp_enqueue_script('json2');
			wp_enqueue_script('jquery-ui-droppable');
			wp_enqueue_script('jquery-ui-draggable');
			wp_enqueue_script('jquery-ui-slider');
			wp_enqueue_script('jquery-ui-widget');
			wp_enqueue_script('jquery-ui-position');
			wp_enqueue_script('jquery-ui-sortable',     $js_url . 'jquery.ui.sortable.js', array('jquery-ui-core', 'jquery-ui-widget', 'jquery-ui-mouse'), $ver, true);
			wp_enqueue_script('jquery-nanoscroller',    $js_url . 'jquery.nanoscroller.min.js', array(), $ver, true);
			wp_enqueue_script('jquery-autosuggest',     $js_url . 'jquery.autoSuggest.min.js', array(), $ver, true);
			wp_enqueue_script('jquery-tiptip',          $js_url . 'jquery.tiptip.min.js', array(), $ver, true);
			wp_enqueue_script('jquery-simulate',        $js_url . 'jquery.simulate.js', array(), $ver, true);
			wp_enqueue_script('jquery-validate',        $js_url . 'jquery.validate.min.js', array(), $ver, true);
			wp_enqueue_script('bootstrap-tour',         $js_url . 'bootstrap-tour-standalone.min.js', array(), $ver, true);
			wp_enqueue_script('ace',                    $js_url . 'ace/ace.js', array(), $ver, true);
			wp_enqueue_script('fl-color-picker',        $js_url . 'fl-color-picker.js', array('jquery', 'jquery-ui-position'), $ver, true);
			wp_enqueue_script('fl-lightbox',            $js_url . 'fl-lightbox.js', array(), $ver, true);
			wp_enqueue_script('fl-icon-selector',       $js_url . 'fl-icon-selector.js', array(), $ver, true);
			wp_enqueue_script('fl-stylesheet',          $js_url . 'fl-stylesheet.js', array(), $ver, true);
			wp_enqueue_script('fl-builder',             $js_url . 'fl-builder.js', array(), $ver, true);
			wp_enqueue_script('fl-builder-preview',     $js_url . 'fl-builder-preview.js', array(), $ver, true);
			wp_enqueue_script('fl-builder-services',    $js_url . 'fl-builder-services.js', array(), $ver, true);
			wp_enqueue_script('fl-builder-tour',        $js_url . 'fl-builder-tour.js', array(), $ver, true);

			/* Core template settings */
			if(file_exists(FL_BUILDER_DIR . 'js/fl-builder-template-settings.js')) {
				wp_enqueue_script('fl-builder-template-settings', FL_BUILDER_URL . 'js/fl-builder-template-settings.js', array(), $ver, true);
			}

			/* Additional module styles and scripts */
			foreach(FLBuilderModel::$modules as $module) {

				$module->enqueue_scripts();

				foreach($module->css as $handle => $props) {
					wp_enqueue_style($handle, $props[0], $props[1], $props[2], $props[3]);
				}
				foreach($module->js as $handle => $props) {
					wp_enqueue_script($handle, $props[0], $props[1], $props[2], $props[3]);
				}
			}
		}
	}

	/**
	 * Adds builder classes to the body class.
	 *
	 * @since 1.0
	 * @param array $classes An array of existing classes.
	 * @return array
	 */
	static public function body_class($classes)
	{
		if(FLBuilderModel::is_builder_enabled()) {
			$classes[] = 'fl-builder';
		}
		if(FLBuilderModel::is_builder_active() && !current_user_can(FLBuilderModel::get_editing_capability())) {
			$classes[] = 'fl-builder-simple';
		}

		return $classes;
	}

	/**
	 * Adds the page builder button to the WordPress admin bar.
	 *
	 * @since 1.0
	 * @param object $wp_admin_bar An instance of the WordPress admin bar.
	 * @return void
	 */
	static public function admin_bar_menu($wp_admin_bar)
	{
		global $wp_the_query;

		if ( FLBuilderModel::is_post_editable() ) {

			$wp_admin_bar->add_node( array(
				'id'    => 'fl-builder-frontend-edit-link',
				'title' => '<style> #wp-admin-bar-fl-builder-frontend-edit-link .ab-icon:before { content: "\f116" !important; top: 2px; margin-right: 3px; } </style><span class="ab-icon"></span>' . FLBuilderModel::get_branding(),
				'href'  => FLBuilderModel::get_edit_url( $wp_the_query->post->ID )
			));
		}
	}

	/**
	 * Renders the markup for the builder interface.
	 *
	 * @since 1.0
	 * @return void
	 */
	static public function render_ui()
	{
		global $wp_the_query;

		if ( FLBuilderModel::is_builder_active() ) {

			$post_id            = $wp_the_query->post->ID;
			$help_button 		= FLBuilderModel::get_help_button_settings();
			$enabled_templates  = FLBuilderModel::get_enabled_templates();
			$color_presets      = FLBuilderModel::get_color_presets();
			$simple_ui			= ! current_user_can( FLBuilderModel::get_editing_capability() );
			$categories         = FLBuilderModel::get_categorized_modules();
			$row_templates		= null;
			$module_templates	= null;
			
			if ( ! FLBuilderModel::is_post_user_template( 'module' ) && ! $simple_ui ) {
				
				if ( class_exists( 'FLBuilderTemplatesOverride' ) ) {
					
					if ( FLBuilderTemplatesOverride::show_modules() ) {
						$module_templates = FLBuilderTemplatesOverride::get_selector_data( 'module' );
					}
					if ( FLBuilderTemplatesOverride::show_rows() && ! FLBuilderModel::is_post_user_template( 'row' ) ) {
						$row_templates = FLBuilderTemplatesOverride::get_selector_data( 'row' );
					}
				}
				
				include FL_BUILDER_DIR . 'includes/ui-panel.php';
			}
			
			include FL_BUILDER_DIR . 'includes/ui-bar.php';
			include FL_BUILDER_DIR . 'includes/ui-fields.php';
			include FL_BUILDER_DIR . 'includes/ui-js-templates.php';
			include FL_BUILDER_DIR . 'includes/ui-js-config.php';
		}
	}

	/**
	 * Renders the markup for the title in the builder's bar.
	 *
	 * @since 1.6.3
	 * @return void
	 */
	static public function render_ui_bar_title()
	{
		global $wp_the_query;
		
		$post_id = $wp_the_query->post->ID;
		
		// Get the bar title.
		if( FLBuilderModel::is_post_user_template() ) {
			$title = sprintf( __( 'Template: %s', 'fl-builder' ), get_the_title( $post_id ) );
		}
		else {
			$title = FLBuilderModel::get_branding();
		}
		
		// Render the bar title.
		if ( '' == FLBuilderModel::get_branding_icon() ) {
			echo '<span class="fl-builder-bar-title fl-builder-bar-title-no-icon">' . $title . '</span>';
		}
		else {
			echo '<span class="fl-builder-bar-title">';
			echo '<img src="' . FLBuilderModel::get_branding_icon() . '" /> ';
			echo '<span>' . $title . '</span></span>';
		}
	}

	/**
	 * Renders the markup for the buttons in the builder's bar.
	 *
	 * @since 1.6.3
	 * @return void
	 */
	static public function render_ui_bar_buttons()
	{
		$help_button 		= FLBuilderModel::get_help_button_settings();
		$enabled_templates  = FLBuilderModel::get_enabled_templates();
		$simple_ui			= ! current_user_can( FLBuilderModel::get_editing_capability() );
		
		$buttons = array(
			'help' => array(
				'label' => '<i class="fa fa-question-circle"></i>',
				'show'	=> $help_button['enabled'] && ! $simple_ui
			),
			'upgrade' => array(
				'label' => __( 'Upgrade!', 'fl-builder' ),
				'show'	=> true === FL_BUILDER_LITE
			),
			'buy' => array(
				'label' => __( 'Buy Now!', 'fl-builder' ),
				'show'	=> stristr( home_url(), 'demo.wpbeaverbuilder.com' )
			),
			'done' => array(
				'label' => __( 'Done', 'fl-builder' ),
				'class' => 'fl-builder-button-primary'
			),
			'tools' => array(
				'label' => __( 'Tools', 'fl-builder' ),
				'show'	=> ! FLBuilderModel::is_post_user_template( 'module' ) && ! $simple_ui
			),
			'templates' => array(
				'label' => __( 'Templates', 'fl-builder' ),
				'show'	=> ! FLBuilderModel::is_post_user_template() && true !== FL_BUILDER_LITE && $enabled_templates != 'disabled' && ! $simple_ui
			),
			'add-content' => array(
				'label' => __( 'Add Content', 'fl-builder' ),
				'show'	=> ! FLBuilderModel::is_post_user_template( 'module' ) && ! $simple_ui
			)	
		);
		
		echo '<div class="fl-builder-bar-actions">';
		
		foreach ( $buttons as $slug => $button ) {
			
			if ( isset( $button['show'] ) && ! $button['show'] ) {
				continue;
			}
			
			echo '<span class="fl-builder-' . $slug . '-button fl-builder-button';
			
			if ( isset( $button['class'] ) ) {
				echo ' ' . $button['class'];
			}
			
			echo '">' . $button['label'] . '</span>';
		}
		
		echo '<div class="fl-clear"></div></div>';
	}

	/**
	 * Renders the UI panel for node templates.
	 *
	 * @since 1.6.3
	 * @return void
	 */
	static public function render_ui_panel_node_templates()
	{
		$file = FL_BUILDER_DIR . 'includes/ui-panel-node-templates.php';
		
		if ( file_exists( $file ) && FLBuilderModel::node_templates_enabled() ) {
			
			$saved_rows    = FLBuilderModel::get_node_templates( 'row' );
			$saved_modules = FLBuilderModel::get_node_templates( 'module' );
			$node_template = FLBuilderModel::is_post_node_template();
			
			// Don't global rows for node templates.
			foreach ( $saved_rows as $key => $val ) {
				if ( $node_template && $val['global'] ) {
					unset( $saved_rows[ $key ] );
				}
			}
			
			// Don't global modules for node templates.
			foreach ( $saved_modules as $key => $val ) {
				if ( $node_template && $val['global'] ) {
					unset( $saved_modules[ $key ] );
				}
			}
			
			include $file;
		}
	}

	/**
	 * Renders the layout to be passed back to
	 * the builder via AJAX for an update.
	 *
	 * @since 1.0
	 * @param bool $return Whether to return the layout or output it.
	 * @return array|void The layout data or nothing.
	 */
	static public function render_layout($return = false)
	{
		global $wp_scripts;
		global $wp_styles;
		
		// Render the assets. 
		self::render_css();
		self::render_js();
		
		// Do the wp_enqueue_scripts action here to register any scripts or 
		// styles that might need to be registered for shortcodes or widgets.
		ob_start();
		do_action( 'wp_enqueue_scripts' );
		ob_end_clean();

		// Dequeue scripts and styles so we can capture only those
		// enqueued by shortcodes or widgets.
		if(isset($wp_scripts)) {
			$wp_scripts->queue = array();
		}
		if(isset($wp_styles)) {
			$wp_styles->queue = array();
		}

		// Render the layout.
		ob_start();
		self::render_nodes();
		$html = ob_get_clean();

		// Process shortcodes.
		ob_start();
		echo do_shortcode($html);
		$html = ob_get_clean();

		// Print scripts and styles enqueued by shortcodes or widgets.
		ob_start();

		if(isset($wp_scripts)) {
			$wp_scripts->done[] = 'jquery';
			wp_print_scripts($wp_scripts->queue);
		}
		if(isset($wp_styles)) {
			wp_print_styles($wp_styles->queue);
		}

		$html = ob_get_clean() . $html;

		// Get the asset info.
		$asset_info = FLBuilderModel::get_asset_info();
		$asset_ver  = FLBuilderModel::get_asset_version();

		// Build the response.
		$response = array(
			'html' => $html,
			'css'  => $asset_info['css_url'] . '?ver=' . $asset_ver,
			'js'  => $asset_info['js_url'] . '?ver=' . $asset_ver
		);

		// Return or echo the response.
		if($return) {
			return $response;
		}
		else {
			echo json_encode($response);
			die();
		}
	}

	/**
	 * Renders the content for a builder layout while in the loop. 
	 * This method should only be called by the_content filter as 
	 * defined in fl-builder.php. To output builder content, use 
	 * the_content function while in a WordPress loop. 
	 *
	 * @since 1.0
	 * @param string $content The existing content.
	 * @return string
	 */
	static public function render_content( $content )
	{
		$post_id        = FLBuilderModel::get_post_id();
		$enabled        = FLBuilderModel::is_builder_enabled();
		$ajax           = defined( 'DOING_AJAX' );
		$in_loop        = in_the_loop();
		$is_global      = in_array( $post_id, FLBuilderModel::get_global_posts() );

		if( $enabled && ! $ajax && ( $in_loop || $is_global ) ) {

			// Remove the builder's render_content filter so it's not called again.
			remove_filter( 'the_content', 'FLBuilder::render_content' );
			
			// Render the content.
			ob_start();
			echo '<div class="' . self::render_content_classes() . '" data-post-id="' . $post_id . '">';
			self::render_nodes();
			echo '</div>';
			$content = ob_get_clean();
			
			// Do shortcodes here since letting the WP filter run can cause an infinite loop.
			$pattern = get_shortcode_regex();
			$content = preg_replace_callback( "/$pattern/s", 'FLBuilder::double_escape_shortcodes', $content );
			$content = do_shortcode( $content );
			
			// Reapply the builder's render_content filter.
			add_filter( 'the_content', 'FLBuilder::render_content' );
		}

		return $content;
	}

	/**
	 * Escaped shortcodes need to be double escaped or they will
	 * be parsed by WP's shortcodes filter.
	 *
	 * @since 1.6.4.1
	 * @param array $matches The existing content.
	 * @return string
	 */
	static public function double_escape_shortcodes( $matches )
	{
		if ( $matches[1] == '[' && $matches[6] == ']' ) {
			return '[' . $matches[0] . ']';
		}
		
		return $matches[0];
	}

	/**
	 * Renders the CSS classes for the main content div tag.
	 *
	 * @since 1.6.4
	 * @return string
	 */
	static public function render_content_classes()
	{
		// Build the content class.
		$classes = 'fl-builder-content fl-builder-content-' . FLBuilderModel::get_post_id();
		
		// Add template classes to the content class.
		if ( FLBuilderModel::is_post_user_template() ) {
			$classes .= ' fl-builder-template';
			$classes .= ' fl-builder-' . FLBuilderModel::get_user_template_type() . '-template';
		}
		
		// Add the global templates locked class.
		if ( ! current_user_can( FLBuilderModel::get_global_templates_editing_capability() ) ) {
			$classes .= ' fl-builder-global-templates-locked';
		}
		
		// Add browser specific classes.
		if ( isset( $_SERVER[ 'HTTP_USER_AGENT' ] ) ) {
			if ( stristr( $_SERVER[ 'HTTP_USER_AGENT' ], 'Trident/7.0' ) && stristr( $_SERVER[ 'HTTP_USER_AGENT' ], 'rv:11.0' ) ) {
				$classes .= ' fl-builder-ie-11';
			}
		}
		
		return $classes;
	}

	/**
	 * Renders the markup for all nodes in a layout.
	 *
	 * @since 1.6.3
	 * @return void
	 */
	static public function render_nodes()
	{
		if ( FLBuilderModel::is_post_user_template( 'module' ) ) {
			self::render_modules();
		}
		else {
			self::render_rows();
		}
	}

	/**
	 * Renders the stripped down content for a layout
	 * that is saved to the WordPress editor.
	 *
	 * @since 1.0
	 * @param string $content The existing content.
	 * @return string
	 */
	static public function render_editor_content()
	{
		$rows = FLBuilderModel::get_nodes('row');

		ob_start();

		// Render the modules.
		foreach($rows as $row) {

			$groups = FLBuilderModel::get_nodes('column-group', $row);

			foreach($groups as $group) {

				$cols = FLBuilderModel::get_nodes('column', $group);

				foreach($cols as $col) {

					$modules = FLBuilderModel::get_modules($col);

					foreach($modules as $module) {

						if($module->editor_export) {

							// Don't crop photos to ensure media library photos are rendered.
							if($module->settings->type == 'photo') {
								$module->settings->crop = false;
							}

							FLBuilder::render_module_html($module->settings->type, $module->settings, $module);
						}
					}
				}
			}
		}

		// Get the content.
		$content = ob_get_clean();

		// Remove unnecessary tags.
		$content = preg_replace('/<\/?div[^>]*\>/i',                '', $content);
		$content = preg_replace('/<\/?span[^>]*\>/i',               '', $content);
		$content = preg_replace('#<script(.*?)>(.*?)</script>#is',  '', $content);
		$content = preg_replace('/<i [^>]*><\\/i[^>]*>/',           '', $content);
		$content = preg_replace('/ class=".*?"/',                   '', $content);

		// Remove empty lines.
		$content = preg_replace('/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/', "\n", $content);

		return $content;
	}

	/**
	 * Renders a settings form. The form is either returned
	 * or output for an AJAX request.
	 *
	 * @since 1.0
	 * @param array $form The form data.
	 * @param object $settings The settings data.
	 * @param bool $return Whether to return the form or output it.
	 * @return string|void The form markup or nothing.
	 */
	static public function render_settings($form = array(), $settings, $return = false)
	{
		$defaults = array(
			'class'     => '',
			'attrs'     => '',
			'title'     => '',
			'badges'	=> array(),
			'tabs'      => array(),
			'buttons'	=> array()
		);

		$form = array_merge($defaults, $form);

		ob_start();
		include FL_BUILDER_DIR . 'includes/settings.php';
		$html = ob_get_clean();

		if(defined('DOING_AJAX') && !$return) {
			echo $html;
			die();
		}
		else {
			return $html;
		}
	}

	/**
	 * Renders a settings form field.
	 *
	 * @since 1.0
	 * @param string $name The field name.
	 * @param array $field An array of setup data for the field.
	 * @param object $settings Form settings data object.
	 * @return void
	 */
	static public function render_settings_field($name, $field, $settings = null)
	{
		$i                  = null;
		$is_multiple        = isset($field['multiple']) && $field['multiple'] === true;
		$supports_multiple  = $field['type'] != 'editor' && $field['type'] != 'photo' && $field['type'] != 'service';
		$settings           = ! $settings ? new stdClass() : $settings;
		$value              = isset($settings->$name) ? $settings->$name : '';
		$preview            = isset($field['preview']) ? json_encode($field['preview']) : json_encode(array('type' => 'refresh'));
		$row_class          = isset($field['row_class']) ? ' ' . $field['row_class'] : '';

		if($is_multiple && $supports_multiple) {

			$values     = $value;
			$arr_name   = $name;
			$name      .= '[]';

			echo '<tbody class="fl-field fl-builder-field-multiples" data-type="form" data-preview=\'' . $preview . '\'>';

			for($i = 0; $i < count($values); $i++) {
				$value = $values[$i];
				echo '<tr class="fl-builder-field-multiple" data-field="'. $arr_name .'">';
				include FL_BUILDER_DIR . 'includes/field.php';
				echo '<td class="fl-builder-field-actions">';
				echo '<i class="fl-builder-field-move fa fa-arrows"></i>';
				echo '<i class="fl-builder-field-copy fa fa-copy"></i>';
				echo '<i class="fl-builder-field-delete fa fa-times"></i>';
				echo '</td>';
				echo '</tr>';
			}

			echo '<tr>';

			if(empty($field['label'])) {
				echo '<td colspan="2">';
			}
			else {
				echo '<td>&nbsp;</td><td>';
			}

			echo '<a href="javascript:void(0);" onclick="return false;" class="fl-builder-field-add fl-builder-button" data-field="'. $arr_name .'">'. sprintf( _x( 'Add %s', 'Field name to add.', 'fl-builder' ), $field['label'] ) .'</a>';
			echo '</td>';
			echo '</tr>';
			echo '</tbody>';
		}
		else {
			echo '<tr id="fl-field-'. $name .'" class="fl-field' . $row_class . '" data-type="' . $field['type'] . '" data-preview=\'' . $preview . '\'>';
			include FL_BUILDER_DIR . 'includes/field.php';
			echo '</tr>';
		}
	}

	/**
	 * Renders a settings form for an AJAX request. The form
	 * markup will be output to the page.
	 *
	 * @since 1.0
	 * @param string $type The type of form to render.
	 * @param object $settings The settings data.
	 * @return void
	 */
	static public function render_settings_form($type = null, $settings = null)
	{
		$post_data  = FLBuilderModel::get_post_data();
		$type       = isset($post_data['type']) ? $post_data['type'] : $type;
		$settings   = isset($post_data['settings']) ? $post_data['settings'] : $settings;
		$form       = FLBuilderModel::get_settings_form( $type );

		if(isset($settings) && !empty($settings)) {
			$defaults = FLBuilderModel::get_settings_form_defaults( $type );
			$settings = (object)array_merge((array)$defaults, (array)$settings);
		}
		else {
			$settings = FLBuilderModel::get_settings_form_defaults( $type );
		}

		self::render_settings(array(
			'title' => $form['title'],
			'tabs'  => $form['tabs']
		), $settings);
	}

	/**
	 * Renders the markup for the global settings form.
	 *
	 * @since 1.0
	 * @return void
	 */
	static public function render_global_settings()
	{
		$settings = FLBuilderModel::get_global_settings();
		$form = FLBuilderModel::$settings_forms['global'];

		self::render_settings(array(
			'class'   => 'fl-builder-global-settings',
			'title'   => $form['title'],
			'tabs'    => $form['tabs']
		), $settings);
	}

	/**
	 * Registers the custom post type for builder templates.
	 *
	 * @since 1.1.3
	 * @since 1.5.7 Added template category taxonomy.
	 * @return void
	 */
	static public function register_templates_post_type()
	{
		// Template classes aren't included in the lite version. 
		if(FL_BUILDER_LITE === true) {
			return;
		}
		
		// Vars for checking if the templates admin should be public.
		$admin_enabled 	= FLBuilderModel::user_templates_admin_enabled();
		$can_edit 		= current_user_can( FLBuilderModel::get_editing_capability() );
		
		// Get the array of supported features for the templates post type.
		$supports = array(
			'title',
			'revisions',
			'page-attributes'
		);
		
		// Include thumbnail support if core templates can be overridden.
		if ( class_exists( 'FLBuilderTemplatesOverride' ) ) {
			$supports[] = 'thumbnail';	
		}
		
		// Register the template post type.
		register_post_type('fl-builder-template', array(
			'public'            => $admin_enabled && $can_edit ? true : false,
			'labels'            => array(
				'name'               => _x( 'Templates', 'Custom post type label.', 'fl-builder' ),
				'singular_name'      => _x( 'Template', 'Custom post type label.', 'fl-builder' ),
				'menu_name'          => _x( 'Templates', 'Custom post type label.', 'fl-builder' ),
				'name_admin_bar'     => _x( 'Template', 'Custom post type label.', 'fl-builder' ),
				'add_new'            => _x( 'Add New', 'Custom post type label.', 'fl-builder' ),
				'add_new_item'       => _x( 'Add New Template', 'Custom post type label.', 'fl-builder' ),
				'new_item'           => _x( 'New Template', 'Custom post type label.', 'fl-builder' ),
				'edit_item'          => _x( 'Edit Template', 'Custom post type label.', 'fl-builder' ),
				'view_item'          => _x( 'View Template', 'Custom post type label.', 'fl-builder' ),
				'all_items'          => _x( 'All Templates', 'Custom post type label.', 'fl-builder' ),
				'search_items'       => _x( 'Search Templates', 'Custom post type label.', 'fl-builder' ),
				'parent_item_colon'  => _x( 'Parent Templates:', 'Custom post type label.', 'fl-builder' ),
				'not_found'          => _x( 'No templates found.', 'Custom post type label.', 'fl-builder' ),
				'not_found_in_trash' => _x( 'No templates found in Trash.', 'Custom post type label.', 'fl-builder' )
			),
			'menu_icon'			=> 'dashicons-welcome-widgets-menus',
			'supports'          => $supports,
			'taxonomies'		=> array(
				'fl-builder-template-category'
			),
			'publicly_queryable' 	=> $can_edit,
			'exclude_from_search'	=> true
		) );
		
		// Register the template category tax.
		register_taxonomy( 'fl-builder-template-category', array( 'fl-builder-template' ), array(
			'labels'            => array(
				'name'              => _x( 'Categories', 'Custom taxonomy label.', 'fl-builder' ),
				'singular_name'     => _x( 'Category', 'Custom taxonomy label.', 'fl-builder' ),
				'search_items'      => _x( 'Search Categories', 'Custom taxonomy label.', 'fl-builder' ),
				'all_items'         => _x( 'All Categories', 'Custom taxonomy label.', 'fl-builder' ),
				'parent_item'       => _x( 'Parent Category', 'Custom taxonomy label.', 'fl-builder' ),
				'parent_item_colon' => _x( 'Parent Category:', 'Custom taxonomy label.', 'fl-builder' ),
				'edit_item'         => _x( 'Edit Category', 'Custom taxonomy label.', 'fl-builder' ),
				'update_item'       => _x( 'Update Category', 'Custom taxonomy label.', 'fl-builder' ),
				'add_new_item'      => _x( 'Add New Category', 'Custom taxonomy label.', 'fl-builder' ),
				'new_item_name'     => _x( 'New Category Name', 'Custom taxonomy label.', 'fl-builder' ),
				'menu_name'         => _x( 'Categories', 'Custom taxonomy label.', 'fl-builder' ),
			),
			'hierarchical'      => true,
			'public'            => true,
			'show_admin_column' => true
		) );
		
		// Register the template type tax.
		register_taxonomy( 'fl-builder-template-type', array( 'fl-builder-template' ), array(
			'label'             => _x( 'Type', 'Custom taxonomy label.', 'fl-builder' ),
			'hierarchical'      => false,
			'public'            => false,
			'show_admin_column' => true
		) );
	}

	/**
	 * Renders the markup for the template selector.
	 *
	 * @since 1.0
	 * @return void
	 */
	static public function render_template_selector()
	{
		if(file_exists(FL_BUILDER_DIR . 'includes/template-selector.php')) {

			$enabled_templates  = FLBuilderModel::get_enabled_templates();
			$user_templates     = FLBuilderModel::get_user_templates();
			$templates          = FLBuilderModel::get_template_selector_data();

			include FL_BUILDER_DIR . 'includes/template-selector.php';

			if(defined('DOING_AJAX')) {
				die();
			}
		}
	}

	/**
	 * Renders the settings form for saving a user defined template.
	 *
	 * @since 1.0
	 * @return void
	 */
	static public function render_user_template_settings()
	{
		$defaults = FLBuilderModel::get_settings_form_defaults( 'user_template' );
		$form     = FLBuilderModel::get_settings_form( 'user_template' );

		FLBuilder::render_settings(array(
			'class'   => 'fl-builder-user-template-settings',
			'title'   => $form['title'],
			'tabs'    => $form['tabs']
		), $defaults);
	}

	/**
	 * Renders the settings form for saving a node template.
	 *
	 * @since 1.6.3
	 * @return void
	 */
	static public function render_node_template_settings()
	{
		$post_data  = FLBuilderModel::get_post_data();
		$defaults 	= FLBuilderModel::get_settings_form_defaults( 'node_template' );
		$form     	= FLBuilderModel::get_settings_form( 'node_template' );
		$node 		= FLBuilderModel::get_node( $post_data['node_id'] );

		FLBuilder::render_settings(array(
			'class'   => 'fl-builder-node-template-settings',
			'attrs'   => 'data-node="'. $node->node .'"',
			'title'   => str_replace( '%s', ucwords( $node->type ), $form['title'] ),
			'tabs'    => $form['tabs']
		), $defaults);
	}

	/**
	 * Trys to load page.php for editing a builder template.
	 *
	 * @since 1.0
	 * @param string $template The current template to be loaded.
	 * @return string
	 */
	static public function render_template($template)
	{
		global $post;

		if($post && $post->post_type == 'fl-builder-template') {

			$page = locate_template(array('page.php'));

			if(!empty($page)) {
				return $page;
			}
		}

		return $template;
	}

	/**
	 * Renders the markup for the icon selector.
	 *
	 * @since 1.0
	 * @return void
	 */
	static public function render_icon_selector()
	{
		$icon_sets = FLBuilderIcons::get_sets();

		include FL_BUILDER_DIR . 'includes/icon-selector.php';

		if(defined('DOING_AJAX')) {
			die();
		}
	}

	/**
	 * Renders the markup for all of the rows in a layout.
	 *
	 * @since 1.0
	 * @return void
	 */
	static public function render_rows()
	{
		$rows = FLBuilderModel::get_nodes('row');

		foreach($rows as $row) {
			self::render_row($row);
		}
	}

	/**
	 * Renders the markup for a single row.
	 *
	 * @since 1.0
	 * @param object $row The row to render.
	 * @return void
	 */
	static public function render_row($row)
	{
		$groups = FLBuilderModel::get_nodes('column-group', $row);

		include FL_BUILDER_DIR . 'includes/row.php';
	}

	/**
	 * Adds a new row to the current layout and renders it.
	 *
	 * @since 1.0
	 * @param string $cols The type of column layout to use.
	 * @param int $position The position of the new row in the layout.
	 * @return void
	 */
	static public function render_new_row($cols = '1-col', $position = false)
	{
		$post_data  = FLBuilderModel::get_post_data();
		$cols       = isset($post_data['cols']) ? $post_data['cols'] : $cols;
		$position   = isset($post_data['position']) ? (int)$post_data['position'] : $position;
		$row        = FLBuilderModel::add_row($cols, $position);

		self::render_row($row);

		if(defined('DOING_AJAX')) {
			die();
		}
	}

	/**
	 * Renders the HTML attributes for a single row.
	 *
	 * @since 1.0
	 * @param object $row A row node object.
	 * @return void
	 */
	static public function render_row_attributes( $row )
	{
		$custom_class = apply_filters( 'fl_builder_row_custom_class', $row->settings->class, $row );
		$overlay_bgs  = array( 'photo', 'parallax', 'slideshow', 'video' );
		$active		  = FLBuilderModel::is_builder_active();
		$global       = FLBuilderModel::is_node_global( $row );
		
		// ID
		if ( ! empty( $row->settings->id ) ) {
			echo ' id="' . esc_attr( $row->settings->id ) . '"';
		}
		
		// Class
		echo ' class="fl-row';
		echo ' fl-row-' . $row->settings->width . '-width';
		echo ' fl-row-bg-' . $row->settings->bg_type;

		if ( ! empty( $row->settings->full_height ) && $row->settings->full_height == 'full' ) {
			echo ' fl-row-full-height';
		}

		if ( in_array( $row->settings->bg_type, $overlay_bgs ) && ! empty( $row->settings->bg_overlay_color ) ) {
			echo ' fl-row-bg-overlay';
		}
		if ( ! empty( $row->settings->responsive_display ) ) {
			echo ' fl-visible-' . $row->settings->responsive_display;
		}
		if ( ! empty( $custom_class ) ) {
			echo ' ' . trim( esc_attr( $custom_class ) );
		}
		if ( $global && $active ) {
			echo ' fl-node-global';
		}
		
		echo ' fl-node-' . $row->node;
		echo '"';
		
		// Data
		echo ' data-node="' . $row->node . '"';

		if ( $row->settings->bg_type == 'parallax' && ! empty( $row->settings->bg_parallax_image_src ) ) {
			echo ' data-parallax-speed="' . $row->settings->bg_parallax_speed . '"';
			echo ' data-parallax-image="' . $row->settings->bg_parallax_image_src . '"';
		}
		if ( $global && $active ) {
			echo ' data-template="' . $row->template_id . '"';
			echo ' data-template-node="' . $row->template_node_id . '"';
			echo ' data-template-url="' . FLBuilderModel::get_node_template_edit_url( $row->template_id ) . '"';
		}
	}

	/**
	 * Renders the markup for a row's background.
	 *
	 * @since 1.0
	 * @param object $row A row node object.
	 * @return void
	 */
	static public function render_row_bg($row)
	{
		if($row->settings->bg_type == 'video') {

			$vid_data = FLBuilderModel::get_row_bg_data($row);

			if($vid_data) {
				include FL_BUILDER_DIR . 'includes/row-video.php';
			}
		}
		else if($row->settings->bg_type == 'slideshow') {
			echo '<div class="fl-bg-slideshow"></div>';
		}
	}

	/**
	 * Renders the HTML class for a row's content wrapper.
	 *
	 * @since 1.0
	 * @param object $row A row node object.
	 * @return void
	 */
	static public function render_row_content_class($row)
	{
		echo 'fl-row-content';
		echo ' fl-row-' . $row->settings->content_width . '-width';
		echo ' fl-node-content';
	}

	/**
	 * Renders the settings lightbox for a row.
	 *
	 * @since 1.0
	 * @param string $node_id A row node ID.
	 * @return void
	 */
	static public function render_row_settings($node_id = null)
	{
		$post_data  = FLBuilderModel::get_post_data();
		$node_id    = isset($post_data['node_id']) ? $post_data['node_id'] : $node_id;
		$node       = FLBuilderModel::get_node($node_id);
		$settings   = $node->settings;
		$form       = FLBuilderModel::$settings_forms['row'];
		$global     = FLBuilderModel::is_node_global( $node );
		$buttons    = array();
		
		// Add the Save As button?
		if ( ! $global && ! FLBuilderModel::is_post_node_template() && FLBuilderModel::node_templates_enabled() ) {
			$buttons[] = 'save-as';
		}

		// Render the form.
		self::render_settings(array(
			'class'     => 'fl-builder-row-settings',
			'attrs'     => 'data-node="'. $node->node .'"',
			'title'     => $form['title'],
			'badges'	=> $global ? array( 'global' => _x( 'Global', 'Indicator for global node templates.', 'fl-builder' ) ) : array(),
			'tabs'      => $form['tabs'],
			'buttons'	=> $buttons
		), $settings);
	}

	/**
	 * Renders the markup for a column group.
	 *
	 * @since 1.0
	 * @param object $group A column group node object.
	 * @return void
	 */
	static public function render_column_group($group)
	{
		$cols = FLBuilderModel::get_nodes('column', $group);

		include FL_BUILDER_DIR . 'includes/column-group.php';
	}

	/**
	 * Adds a new column group and renders it.
	 *
	 * @since 1.0
	 * @param string $node_id The node ID of a row to add the new group to.
	 * @param string $cols The type of column layout to use.
	 * @param int $position The position of the new column group in the row.
	 * @return void
	 */
	static public function render_new_column_group($node_id = null, $cols = '1-col', $position = false)
	{
		$post_data  = FLBuilderModel::get_post_data();
		$node_id    = isset($post_data['node_id']) ? $post_data['node_id'] : $node_id;
		$cols       = isset($post_data['cols']) ? $post_data['cols'] : $cols;
		$position   = isset($post_data['position']) ? (int)$post_data['position'] : $position;
		$group      = FLBuilderModel::add_col_group($node_id, $cols, $position);

		self::render_column_group($group);

		if(defined('DOING_AJAX')) {
			die();
		}
	}

	static public function is_column_equal_height( $group ){
		$cols = FLBuilderModel::get_nodes( 'column', $group );

		foreach( $cols as $col ){
			if( isset( $col->settings->equal_height ) && $col->settings->equal_height == 'yes' )
				return true;
		}
		return false;
	}

	static public function column_has_custom_width( $group ){
		$cols = FLBuilderModel::get_nodes( 'column', $group );

		foreach( $cols as $col ){
			if( isset( $col->settings->responsive_size ) && $col->settings->responsive_size == 'custom' )
				return true;
		}
		return false;
	}

	/**
	 * Adds a new column group and renders it.
	 *
	 * @since 1.0
	 * @param string $node_id The node ID of a row to add the new group to.
	 * @param string $cols The type of column layout to use.
	 * @param int $position The position of the new column group in the row.
	 * @return void
	 */
	static public function render_column_group_attributes( $group )
	{
		$equal_height = self::is_column_equal_height( $group ) ? ' fl-col-group-equal-height' : '';
		$custom_width = self::column_has_custom_width( $group ) ? ' fl-col-group-custom-width' : '';
		echo ' class="fl-col-group fl-node-' . $group->node . $equal_height . $custom_width . '"';
		echo ' data-node="' . $group->node . '"';
	}

	/**
	 * Renders the markup for the column settings lightbox.
	 *
	 * @since 1.0
	 * @param string $node_id A column node ID.
	 * @return void
	 */
	static public function render_column_settings($node_id = null)
	{
		$post_data  = FLBuilderModel::get_post_data();
		$node_id    = isset($post_data['node_id']) ? $post_data['node_id'] : $node_id;
		$node       = FLBuilderModel::get_node($node_id);
		$settings   = $node->settings;
		$form       = FLBuilderModel::$settings_forms['col'];
		$global 	= FLBuilderModel::is_node_global( $node );

		self::render_settings(array(
			'class'     => 'fl-builder-col-settings',
			'attrs'     => 'data-node="'. $node->node .'"',
			'title'     => $form['title'],
			'badges'	=> $global ? array( 'global' => _x( 'Global', 'Indicator for global node templates.', 'fl-builder' ) ) : array(),
			'tabs'      => $form['tabs']
		), $settings);
	}

	/**
	 * Renders the HTML attributes for a single column.
	 *
	 * @since 1.0
	 * @param object $col A column node object.
	 * @return void
	 */
	static public function render_column_attributes( $col )
	{
		$custom_class = apply_filters( 'fl_builder_column_custom_class', $col->settings->class, $col );
		$overlay_bgs  = array( 'photo' );
		$active		  = FLBuilderModel::is_builder_active();
		$global       = FLBuilderModel::is_node_global( $col );
		
		// ID
		if ( ! empty( $col->settings->id ) ) {
			echo ' id="' . esc_attr( $col->settings->id ) . '"';
		}
		
		// Class
		echo ' class="fl-col';

		if ( $col->settings->size <= 50 ) {
			echo ' fl-col-small';
		}
		if ( in_array( $col->settings->bg_type, $overlay_bgs ) && ! empty( $col->settings->bg_overlay_color ) ) {
			echo ' fl-col-bg-overlay';
		}
		if ( ! empty( $col->settings->responsive_display ) ) {
			echo ' fl-visible-' . $col->settings->responsive_display;
		}
		if ( ! empty( $custom_class ) ) {
			echo ' ' . trim( esc_attr( $custom_class ) );
		}
		if ( $global && $active ) {
			echo ' fl-node-global';
		}
		
		echo ' fl-node-' . $col->node;
		echo '"';
		
		// Width
		echo ' style="width: ' . $col->settings->size . '%;"';
		
		// Data
		echo ' data-node="' . $col->node . '"';
		
		if ( $global && $active ) {
			echo ' data-template="' . $col->template_id . '"';
			echo ' data-template-node="' . $col->template_node_id . '"';
		}
	}

	/**
	 * Renders the markup for all modules in a column.
	 *
	 * @since 1.0
	 * @param string|object $col_id A column ID or object.
	 * @return void
	 */
	static public function render_modules( $col_id = null )
	{
		$modules = FLBuilderModel::get_modules( $col_id );

		foreach ( $modules as $module ) {
			
			$settings 	= $module->settings;
			$id 		= $module->node;
			
			include FL_BUILDER_DIR . 'includes/module.php';
		}
	}

	/**
	 * Renders the settings lightbox for when a new module
	 * is added to a layout.
	 *
	 * @since 1.0
	 * @param string $parent_id A column node ID.
	 * @param string $type The type of module.
	 * @param int $position The new module position.
	 * @return array|void An array of layout data or nothing if called via AJAX.
	 */
	static public function render_new_module_settings($parent_id = null, $type = null, $position = false)
	{
		$post_data      = FLBuilderModel::get_post_data();
		$parent_id      = isset($post_data['parent_id']) ? $post_data['parent_id'] : $parent_id;
		$type           = isset($post_data['type']) ? $post_data['type'] : $type;
		$position       = isset($post_data['position']) ? (int)$post_data['position'] : $position;
		$module         = FLBuilderModel::add_default_module($parent_id, $type, $position);

		// Force the global parent id.
		FLBuilderModel::update_post_data('parent_id', $module->parent);

		// Get the settings html.
		ob_start();
		self::render_module_settings($module->node, $module->type, $module->parent, true);
		$settings = ob_get_clean();

		// Build the response.
		$response = array(
			'layout'    => self::render_layout(true),
			'settings'  => $settings
		);

		// Echo or return the response.
		if(defined('DOING_AJAX')) {
			echo json_encode($response);
			die();
		}
		else {
			return $response;
		}
	}

	/**
	 * Renders the settings lightbox for when a module template
	 * is added to a layout.
	 *
	 * @since 1.6.3
	 * @param string $parent_id A column node ID.
	 * @param string $type The type of module.
	 * @param int $position The new module position.
	 * @return array|void An array of layout data or nothing if called via AJAX.
	 */
	static public function render_module_template_settings( $parent_id = null, $type = null, $position = false )
	{
		$post_data      = FLBuilderModel::get_post_data();
		$template_id    = isset( $post_data['template_id'] ) ? $post_data['template_id'] : $template_id;
		$parent_id      = isset( $post_data['parent_id'] ) ? $post_data['parent_id'] : $parent_id;
		$position       = isset( $post_data['position'] ) ? ( int )$post_data['position'] : $position;
		$module         = FLBuilderModel::apply_node_template( $template_id, $parent_id, $position );

		// Force the global parent id.
		FLBuilderModel::update_post_data( 'parent_id', $module->parent) ;

		// Get the settings html.
		ob_start();
		self::render_module_settings( $module->node, $module->type, $module->parent, true );
		$settings = ob_get_clean();

		// Build the response.
		$response = array(
			'layout'    => self::render_layout( true ),
			'settings'  => $settings
		);

		// Echo or return the response.
		if ( defined( 'DOING_AJAX' ) ) {
			echo json_encode( $response );
			die();
		}
		else {
			return $response;
		}
	}

	/**
	 * Renders the settings lightbox for a module.
	 *
	 * @since 1.0
	 * @param string $node_id The module node ID.
	 * @param string $type The type of module.
	 * @param string $parent_id The parent column node ID.
	 * @param bool $return Whether to return the layout data or echo it.
	 * @return void
	 */
	static public function render_module_settings($node_id = null, $type = null, $parent_id = null, $return = false)
	{
		$post_data  = FLBuilderModel::get_post_data();
		$node_id    = isset($post_data['node_id']) ? $post_data['node_id'] : $node_id;
		$type       = isset($post_data['type']) ? $post_data['type'] : $type;
		$parent_id  = isset($post_data['parent_id']) ? $post_data['parent_id'] : $parent_id;
		$buttons    = array();
		
		// Get the module and settings.
		if($node_id) {
			$module     = FLBuilderModel::get_module($node_id);
			$settings   = $module->settings;
		}
		else {
			$module     = FLBuilderModel::$modules[$type];
			$settings   = FLBuilderModel::get_module_defaults($type);
		}
		
		// Is this module global?
		$global = FLBuilderModel::is_node_global( $module );
		
		// Add the Save As button?
		if ( ! $global && ! FLBuilderModel::is_post_node_template() && FLBuilderModel::node_templates_enabled() ) {
			$buttons[] = 'save-as';
		}

		// Render the settings CSS/JS.
		if(file_exists($module->dir .'css/settings.css')) {
			echo '<link class="fl-builder-settings-css" rel="stylesheet" href="'. $module->url .'css/settings.css" />';
		}
		if(file_exists($module->dir .'js/settings.js')) {
			echo '<script class="fl-builder-settings-js" src="'. $module->url .'js/settings.js"></script>';
		}

		// Render the form.
		echo self::render_settings(array(
			'class' 	=> 'fl-builder-module-settings fl-builder-'. $type .'-settings',
			'attrs' 	=> 'data-node="'. $node_id .'" data-parent="'. $parent_id .'" data-type="'. $type .'"',
			'title' 	=> sprintf( _x( '%s Settings', '%s stands for module name.', 'fl-builder' ), $module->name ),
			'badges'	=> $global ? array( 'global' => _x( 'Global', 'Indicator for global node templates.', 'fl-builder' ) ) : array(),
			'tabs'  	=> $module->form,
			'buttons'	=> $buttons
		), $settings, $return);
	}

	/**
	 * Renders the markup for a single module. This can be used to render
	 * the markup of a module within another module by passing the type 
	 * and settings params and leaving the module param null.
	 *
	 * @since 1.0
	 * @param string $type The type of module.
	 * @param object $settings A module settings object.
	 * @param object $module Optional. An existing module object to use.
	 * @return void
	 */
	static public function render_module_html($type, $settings, $module = null)
	{
		// Settings
		$defaults = FLBuilderModel::get_module_defaults($type);
		$settings = (object)array_merge((array)$defaults, (array)$settings);

		// Module
		$class = get_class(FLBuilderModel::$modules[$type]);
		$module = new $class();
		$module->settings = $settings;

		// Shorthand reference to the module's id.
		$id = $module->node;

		include $module->dir .'includes/frontend.php';
	}

	/**
	 * Renders the HTML attributes for a single module.
	 *
	 * @since 1.0
	 * @param object $module A module node object.
	 * @return void
	 */
	static public function render_module_attributes( $module )
	{
		$custom_class = apply_filters( 'fl_builder_module_custom_class', $module->settings->class, $module );
		$active		  = FLBuilderModel::is_builder_active();
		$global       = FLBuilderModel::is_node_global( $module );
		
		// ID
		if ( ! empty( $module->settings->id ) ) {
			echo ' id="' . esc_attr( $module->settings->id ) . '"';
		}
		
		// Class
		echo ' class="fl-module';
		echo ' fl-module-' . $module->settings->type;

		if ( ! empty( $module->settings->responsive_display ) ) {
			echo ' fl-visible-' . $module->settings->responsive_display;
		}
		if ( ! empty( $module->settings->animation ) ) {
			echo ' fl-animation fl-' . $module->settings->animation;
		}
		if ( ! empty( $custom_class ) ) {
			echo ' ' . trim( esc_attr( $custom_class ) );
		}
		if ( $global && $active ) {
			echo ' fl-node-global';
		}
		
		echo ' fl-node-' . $module->node;
		echo '"';
		
		// Data
		echo ' data-node="' . $module->node . '" ';
		echo ' data-animation-delay="' . $module->settings->animation_delay . '" ';

		if ( FLBuilderModel::is_builder_active() ) {
			echo ' data-parent="' . $module->parent . '" ';
			echo ' data-type="' . $module->settings->type . '" ';
			echo ' data-name="' . $module->name . '" ';
		}
		if ( $global && $active ) {
			echo ' data-template="' . $module->template_id . '"';
			echo ' data-template-node="' . $module->template_node_id . '"';
		}
	}

	/**
	 * Renders the CSS for a single module.
	 *
	 * @since 1.0
	 * @param string $type The type of module.
	 * @param object $id A module node ID.
	 * @param object $settings A module settings object.
	 * @return void
	 */
	static public function render_module_css($type, $id, $settings)
	{
		// Settings
		$global_settings = FLBuilderModel::get_global_settings();
		$defaults = FLBuilderModel::get_module_defaults($type);
		$settings = (object)array_merge((array)$defaults, (array)$settings);

		// Module
		$class = get_class(FLBuilderModel::$modules[$type]);
		$module = new $class();
		$module->settings = $settings;

		include $module->dir .'includes/frontend.css.php';
	}

	/**
	 * Renders and caches the CSS for a builder layout.
	 *
	 * @since 1.0
	 * @return void
	 */
	static public function render_css()
	{
		// Delete the old file.
		FLBuilderModel::delete_asset_cache('css');

		// Get info on the new file.
		$nodes 				= FLBuilderModel::get_categorized_nodes();
		$global_settings    = FLBuilderModel::get_global_settings();
		$asset_info         = FLBuilderModel::get_asset_info();
		$post_id            = FLBuilderModel::get_post_id();
		$post               = get_post($post_id);
		$compiled           = array();

		// Global css
		$css = file_get_contents(FL_BUILDER_DIR . '/css/fl-builder-layout.css');
		
		// Global RTL css
		if(is_rtl()) {
			$css .= file_get_contents(FL_BUILDER_DIR . '/css/fl-builder-layout-rtl.css');
		}

		// Responsive css
		if($global_settings->responsive_enabled) {
			
			$css .= '@media (max-width: '. $global_settings->medium_breakpoint .'px) { ';
			$css .= file_get_contents(FL_BUILDER_DIR . '/css/fl-builder-layout-medium.css');
			$css .= ' }';
			$css .= '@media (max-width: '. $global_settings->responsive_breakpoint .'px) { ';
			$css .= file_get_contents(FL_BUILDER_DIR . '/css/fl-builder-layout-responsive.css');
			
			if ( ! isset( $global_settings->auto_spacing ) || $global_settings->auto_spacing ) {
				$css .= file_get_contents(FL_BUILDER_DIR . '/css/fl-builder-layout-auto-spacing.css');
			}
			
			$css .= ' }';
		}

		// Global row margins
		$css .= '.fl-row-content-wrap { margin: '. $global_settings->row_margins .'px; }';

		// Global row padding
		$css .= '.fl-row-content-wrap { padding: '. $global_settings->row_padding .'px; }';

		// Global row width
		$css .= '.fl-row-fixed-width { max-width: '. $global_settings->row_width .'px; }';

		// Global module margins
		$css .= '.fl-module-content { margin: '. $global_settings->module_margins .'px; }';

		// Loop through rows
		foreach($nodes['rows'] as $row) {

			// Instance row css
			ob_start();
			include FL_BUILDER_DIR . 'includes/row-css.php';
			$css .= ob_get_clean();

			// Instance row margins
			$css .= self::render_row_margins($row);

			// Instance row padding
			$css .= self::render_row_padding($row);
		}
		
		// Loop through the columns.
		foreach($nodes['columns'] as $col) {
			
			// Instance column css
			ob_start();
			include FL_BUILDER_DIR . 'includes/column-css.php';
			$css .= ob_get_clean();

			// Instance column margins
			$css .= self::render_column_margins($col);

			// Instance column padding
			$css .= self::render_column_padding($col);

			// Get the modules in this column.
			$modules = FLBuilderModel::get_modules($col);
		}
		
		// Loop through the modules.
		foreach($nodes['modules'] as $module) {

			// Global module css
			$file = $module->dir . 'css/frontend.css';
			$file_responsive = $module->dir . 'css/frontend.responsive.css';

			// Only include global module css that hasn't been included yet.
			if(!in_array($module->settings->type, $compiled)) {

				// Add to the compiled array so we don't include it again.
				$compiled[] = $module->settings->type;

				// Get the standard module css.
				if(file_exists($file)) {
					$css .= file_get_contents($file);
				}

				// Get the responsive module css.
				if($global_settings->responsive_enabled && file_exists($file_responsive)) {
					$css .= '@media (max-width: '. $global_settings->responsive_breakpoint .'px) { ';
					$css .= file_get_contents($file_responsive);
					$css .= ' }';
				}
			}

			// Instance module css
			$file       = $module->dir . 'includes/frontend.css.php';
			$settings   = $module->settings;
			$id         = $module->node;

			if(file_exists($file)) {
				ob_start();
				include $file;
				$css .= ob_get_clean();
			}

			// Instance module margins
			$css .= self::render_module_margins($module);
			
			if ( ! isset( $global_settings->auto_spacing ) || $global_settings->auto_spacing ) {
				$css .= self::render_responsive_module_margins($module);
			}
		}

		// Default page heading
		if($post && !$global_settings->show_default_heading && ($post->post_type == 'page' || $post->post_type == 'fl-builder-template')) {
			$css .= $global_settings->default_heading_selector . ' { display:none; }';
		}

		// Save the css
		if(!empty($css)) {
			$css = apply_filters( 'fl_builder_render_css', $css, $nodes, $global_settings );
			$css = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css);
			$css = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $css);
			file_put_contents($asset_info['css'], $css);
		}
	}

	/**
	 * Renders the CSS margins for a row.
	 *
	 * @since 1.0
	 * @param object $row A row node object.
	 * @return string The row CSS margins string.
	 */
	static public function render_row_margins($row)
	{
		$settings   = $row->settings;
		$margins    = '';
		$css        = '';

		if($settings->margin_top != '') {
			$margins    .= 'margin-top:'    . $settings->margin_top . 'px;';
		}
		if($settings->margin_bottom != '') {
			$margins    .= 'margin-bottom:' . $settings->margin_bottom . 'px;';
		}
		if($settings->margin_left != '') {
			$margins    .= 'margin-left:'   . $settings->margin_left . 'px;';
		}
		if($settings->margin_right != '') {
			$margins    .= 'margin-right:'  . $settings->margin_right . 'px;';
		}
		if($margins != '') {
			$css .= '.fl-node-' . $row->node . ' .fl-row-content-wrap {' . $margins . '}';
		}

		return $css;
	}

	/**
	 * Renders the CSS padding for a row.
	 *
	 * @since 1.0
	 * @param object $row A row node object.
	 * @return string The row CSS padding string.
	 */
	static public function render_row_padding($row)
	{
		$settings = $row->settings;
		$padding  = '';
		$css      = '';

		if($settings->padding_top != '') {
			$padding .= 'padding-top:' . $settings->padding_top . 'px;';
		}
		if($settings->padding_bottom != '') {
			$padding .= 'padding-bottom:' . $settings->padding_bottom . 'px;';
		}
		if($settings->padding_left != '') {
			$padding .= 'padding-left:' . $settings->padding_left . 'px;';
		}
		if($settings->padding_right != '') {
			$padding .= 'padding-right:' . $settings->padding_right . 'px;';
		}
		if($padding != '') {
			$css = '.fl-node-' . $row->node . ' .fl-row-content-wrap {' . $padding . '}';
		}

		return $css;
	}

	/**
	 * Renders the CSS margins for a column.
	 *
	 * @since 1.0
	 * @param object $col A column node object.
	 * @return string The column CSS margins string.
	 */
	static public function render_column_margins($col)
	{
		$settings   = $col->settings;
		$margins    = '';
		$css        = '';

		if($settings->margin_top != '') {
			$margins    .= 'margin-top:'    . $settings->margin_top . 'px;';
		}
		if($settings->margin_bottom != '') {
			$margins    .= 'margin-bottom:' . $settings->margin_bottom . 'px;';
		}
		if($settings->margin_left != '') {
			$margins    .= 'margin-left:'   . $settings->margin_left . 'px;';
		}
		if($settings->margin_right != '') {
			$margins    .= 'margin-right:'  . $settings->margin_right . 'px;';
		}
		if($margins != '') {
			$css .= '.fl-node-' . $col->node . ' .fl-col-content {' . $margins . '}';
		}

		return $css;
	}

	/**
	 * Renders the CSS padding for a column.
	 *
	 * @since 1.0
	 * @param object $col A column node object.
	 * @return string The column CSS padding string.
	 */
	static public function render_column_padding($col)
	{
		$settings = $col->settings;
		$padding  = '';
		$css      = '';

		if($settings->padding_top != '') {
			$padding .= 'padding-top:' . $settings->padding_top . 'px;';
		}
		if($settings->padding_bottom != '') {
			$padding .= 'padding-bottom:' . $settings->padding_bottom . 'px;';
		}
		if($settings->padding_left != '') {
			$padding .= 'padding-left:' . $settings->padding_left . 'px;';
		}
		if($settings->padding_right != '') {
			$padding .= 'padding-right:' . $settings->padding_right . 'px;';
		}
		if($padding != '') {
			$css = '.fl-node-' . $col->node . ' .fl-col-content {' . $padding . '}';
		}

		return $css;
	}

	/**
	 * Renders the CSS margins for a module.
	 *
	 * @since 1.0
	 * @param object $module A module node object.
	 * @return string The module CSS margins string.
	 */
	static public function render_module_margins($module)
	{
		$settings  = $module->settings;
		$margins   = '';
		$css       = '';

		if($settings->margin_top != '') {
			$margins .= 'margin-top:' . $settings->margin_top . 'px;';
		}
		if($settings->margin_bottom != '') {
			$margins .= 'margin-bottom:' . $settings->margin_bottom . 'px;';
		}
		if($settings->margin_left != '') {
			$margins .= 'margin-left:' . $settings->margin_left . 'px;';
		}
		if($settings->margin_right != '') {
			$margins .= 'margin-right:' . $settings->margin_right . 'px;';
		}
		if($margins != '') {
			$css = '.fl-node-' . $module->node . ' .fl-module-content {' . $margins . '}';
		}

		return $css;
	}

	/**
	 * Renders the responsive CSS margins for a module.
	 *
	 * @since 1.0
	 * @param object $module A module node object.
	 * @return string The module CSS margins string.
	 */
	static public function render_responsive_module_margins($module)
	{
		$global_settings    = FLBuilderModel::get_global_settings();
		$default            = $global_settings->module_margins;
		$settings           = $module->settings;
		$margins            = '';
		$css                = '';

		if($settings->margin_top != '' && ($settings->margin_top > $default || $settings->margin_top < 0)) {
			$margins .= 'margin-top:' . $default . 'px;';
		}
		if($settings->margin_bottom != '' && ($settings->margin_bottom > $default || $settings->margin_bottom < 0)) {
			$margins .= 'margin-bottom:' . $default . 'px;';
		}
		if($settings->margin_left != '' && ($settings->margin_left > $default || $settings->margin_left < 0)) {
			$margins .= 'margin-left:' . $default . 'px;';
		}
		if($settings->margin_right != '' && ($settings->margin_right > $default || $settings->margin_right < 0)) {
			$margins .= 'margin-right:' . $default . 'px;';
		}
		if($margins != '') {
			$css .= '@media (max-width: '. $global_settings->responsive_breakpoint .'px) { ';
			$css .= '.fl-node-' . $module->node . ' .fl-module-content {' . $margins . '}';
			$css .= ' }';
		}

		return $css;
	}

	/**
	 * Renders and caches the JavaScript for a builder layout.
	 *
	 * @since 1.0
	 * @return void
	 */
	static public function render_js()
	{
		// Delete the old file.
		FLBuilderModel::delete_asset_cache('js');

		// Get info on the new file.
		$nodes 				= FLBuilderModel::get_categorized_nodes();
		$global_settings    = FLBuilderModel::get_global_settings();
		$rows               = FLBuilderModel::get_nodes('row');
		$asset_info         = FLBuilderModel::get_asset_info();
		$compiled           = array();
		$js                 = '';
		
		// Layout config object.
		ob_start();
		include FL_BUILDER_DIR . 'includes/layout-js-config.php';
		$js .= ob_get_clean();

		// Main JS
		$js .= file_get_contents(FL_BUILDER_DIR . 'js/fl-builder-layout.js');

		// Loop through the rows.
		foreach($nodes['rows'] as $row) {

			// Setup row vars
			$settings   = $row->settings;
			$id         = $row->node;

			// Include the row instance JS
			ob_start();
			include FL_BUILDER_DIR . 'includes/row-js.php';
			$js .= ob_get_clean();
		}
		
		// Loop through the modules.
		foreach($nodes['modules'] as $module) {

			// Global module JS
			$file = $module->dir . 'js/frontend.js';

			if(file_exists($file) && !in_array($module->settings->type, $compiled)) {
				$js .= "\n" . file_get_contents($file);
				$compiled[] = $module->settings->type;
			}

			// Instance module JS
			$file       = $module->dir . 'includes/frontend.js.php';
			$settings   = $module->settings;
			$id         = $module->node;

			if(file_exists($file)) {
				ob_start();
				include $file;
				$js .= ob_get_clean();
			}
		}

		// Add the path legacy vars (FLBuilderLayoutConfig.paths should be used instead).
		$js .= "var wpAjaxUrl = '" . admin_url('admin-ajax.php') . "';";
		$js .= "var flBuilderUrl = '" . FL_BUILDER_URL . "';";

		// Call the FLBuilder._renderLayoutComplete method if we're currently editing.
		if(stristr($asset_info['js'], '-draft.js') || stristr($asset_info['js'], '-preview.js')) {
			$js .= "; if(typeof FLBuilder !== 'undefined' && typeof FLBuilder._renderLayoutComplete !== 'undefined') FLBuilder._renderLayoutComplete();";
		}

		// Include FLJSMin
		if(!class_exists('FLJSMin')) {
			include FL_BUILDER_DIR . 'classes/class-fl-jsmin.php';
		}

		// Save the js
		if(!empty($js)) {
			file_put_contents($asset_info['js'], FLJSMin::minify($js));
		}
	}

	/**
	 * Custom error logging function that handles objects and arrays.
	 *
	 * @since 1.0
	 * @return void
	 */
	static public function log($data)
	{
		ob_start();
		print_r($data);
		error_log(ob_get_clean());
	}
}