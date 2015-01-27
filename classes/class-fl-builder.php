<?php

/**
 * Main builder class.
 *
 * @class FLBuilder
 */
final class FLBuilder {

	/**
	 * Localization
	 *
	 * Load the translation file for current language. Checks the default WordPress
	 * languages folder first and then the languages folder inside the plugin.
	 *
	 * @method load_plugin_textdomain
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
	 * Custom ajax handeling since wp_ajax only works in the
	 * admin and we need everything to work on the frontend.
	 *
     * @method ajax
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
     * @method init
     */
    static public function init()
    {
        // Enable editing if the builder is active.
        if(FLBuilderModel::is_builder_active()) {

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
     * @method register_module
     */
    static public function register_module($class, $form)
    {
        FLBuilderModel::register_module($class, $form);
    }

	/**
     * @method register_settings_form
     */
    static public function register_settings_form($id, $form)
    {
        FLBuilderModel::register_settings_form($id, $form);
    }

	/**
     * @method no_cache_headers
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
     * @method default_editor
     */
	static public function default_editor($type)
	{
        return FLBuilderModel::is_builder_active() ? 'tinymce' : $type;
	}

	/**
     * @method add_editor_css
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
     * @method editor_buttons_2
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
     * @method editor_external_plugins
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
     * @method include_jquery
     */
	static public function include_jquery()
	{
	    if(FLBuilderModel::is_builder_enabled()) {
	        include FL_BUILDER_DIR . 'includes/jquery.php';
        }
	}

	/**
     * @method layout_styles_scripts
     */
	static public function layout_styles_scripts()
	{
        global $wp_query;
        global $post;

        $ver     = FL_BUILDER_VERSION;
        $css_url = FL_BUILDER_URL . 'css/';
        $js_url  = FL_BUILDER_URL . 'js/';

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

            $posts = get_posts(array('post__in' => $post_ids, 'post_type' => 'any'));

            foreach($posts as $post) {
                self::enqueue_layout_styles_scripts($post->ID);
            }
        }

        // Reset the main query.
        wp_reset_query();
	}

	/**
     * @method enqueue_layout_styles_scripts
     */
	static public function enqueue_layout_styles_scripts($post_id)
	{
	    if(FLBuilderModel::is_builder_enabled()) {

            $rows       = FLBuilderModel::get_nodes('row');
            $modules    = FLBuilderModel::get_all_modules();
            $asset_info = FLBuilderModel::get_asset_info();
            $asset_ver  = FLBuilderModel::get_asset_version();

            // Enqueue required row CSS and JS
            foreach($rows as $row) {
                if($row->settings->bg_type == 'slideshow') {
                    wp_enqueue_script('yui3');
                    wp_enqueue_script('fl-slideshow');
                    wp_enqueue_style('fl-slideshow');
                }
            }

            // Enqueue required module CSS and JS
            foreach($modules as $module) {

                $module->enqueue_icon_styles();
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

            wp_enqueue_style('fl-builder-layout-' . $post_id, $asset_info['css_url'], array(), $asset_ver);

            // Enqueue main JS
            if(!file_exists($asset_info['js'])) {
                FLBuilder::render_js();
            }

            wp_enqueue_script('fl-builder-layout-' . $post_id, $asset_info['js_url'], array(), $asset_ver, true);
        }
	}

	/**
     * @method styles_scripts
     */
	static public function styles_scripts()
	{
        if(FLBuilderModel::is_builder_active()) {

            $ver     = FL_BUILDER_VERSION;
            $css_url = FL_BUILDER_URL . 'css/';
            $js_url  = FL_BUILDER_URL . 'js/';

            /* We have a custom version of sortable that fixes a bug. */
            wp_deregister_script('jquery-ui-sortable');

            /* Frontend builder styles */
    		wp_enqueue_style('dashicons');
    		wp_enqueue_style('font-awesome');
    		wp_enqueue_style('foundation-icons');
    		wp_enqueue_style('jquery-nanoscroller',     $css_url . 'jquery.nanoscroller.css', array(), $ver);
    		wp_enqueue_style('jquery-autosuggest',      $css_url . 'jquery.autoSuggest.min.css', array(), $ver);
    		wp_enqueue_style('jquery-tiptip',           $css_url . 'jquery.tiptip.css', array(), $ver);
    		wp_enqueue_style('fl-color-picker',         $css_url . 'colorpicker.css', array(), $ver);
    		wp_enqueue_style('fl-lightbox',             $css_url . 'fl-lightbox.css', array(), $ver);
    		wp_enqueue_style('fl-icon-selector',        $css_url . 'fl-icon-selector.css', array(), $ver);
            wp_enqueue_style('fl-builder',              $css_url . 'fl-builder.css', array(), $ver);

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
            wp_enqueue_script('jquery-ui-sortable',     $js_url . 'jquery.ui.sortable.js', array('jquery-ui-core', 'jquery-ui-widget', 'jquery-ui-mouse'), $ver, true);
    		wp_enqueue_script('jquery-nanoscroller',    $js_url . 'jquery.nanoscroller.min.js', array(), $ver, true);
    		wp_enqueue_script('jquery-autosuggest',     $js_url . 'jquery.autoSuggest.min.js', array(), $ver, true);
    		wp_enqueue_script('jquery-tiptip',          $js_url . 'jquery.tiptip.min.js', array(), $ver, true);
    		wp_enqueue_script('jquery-simulate',        $js_url . 'jquery.simulate.js', array(), $ver, true);
    		wp_enqueue_script('jquery-validate',        $js_url . 'jquery.validate.min.js', array(), $ver, true);
			wp_enqueue_script('ace',                    $js_url . 'ace/ace.js', array(), $ver, true);
    		wp_enqueue_script('fl-color-picker',        $js_url . 'colorpicker.js', array(), $ver, true);
    		wp_enqueue_script('fl-lightbox',            $js_url . 'fl-lightbox.js', array(), $ver, true);
    		wp_enqueue_script('fl-icon-selector',       $js_url . 'fl-icon-selector.js', array(), $ver, true);
            wp_enqueue_script('fl-stylesheet',          $js_url . 'fl-stylesheet.js', array(), $ver, true);
            wp_enqueue_script('fl-builder',             $js_url . 'fl-builder.js', array(), $ver, true);
            wp_enqueue_script('fl-builder-preview',     $js_url . 'fl-builder-preview.js', array(), $ver, true);

            /* Core template settings */
            if(file_exists(FL_BUILDER_DIR . 'js/fl-template-settings.js')) {
                wp_enqueue_script('fl-template-settings', FL_BUILDER_URL . 'js/fl-template-settings.js', array(), $ver, true);
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
     * @method body_class
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
     * @method admin_bar_menu
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
     * @method render_ui
     */
    static public function render_ui()
    {
        global $wp_the_query;

        if ( FLBuilderModel::is_builder_active() ) {

            $post_id            = $wp_the_query->post->ID;
        	$categories         = FLBuilderModel::get_categorized_modules();
        	$enabled_templates  = FLBuilderModel::get_enabled_templates();

            include FL_BUILDER_DIR . 'includes/ui.php';
            include FL_BUILDER_DIR . 'includes/js-config.php';
        }
    }

    /**
     * Renders a preview of the layout to be
     * passed back to the builder via AJAX.
     *
     * @method render_preview
     */
    static public function render_preview($return = false)
    {
        self::render_css();
        self::render_js();

        if($return) {
            return self::render_layout(true);
        }
        else {
            self::render_layout();
        }
    }

    /**
     * Renders the layout to be passed back to
     * the builder via AJAX for an update.
     *
     * @method render_layout
     */
    static public function render_layout($return = false)
    {
        global $wp_scripts;
        global $wp_styles;

        // Deregister scripts and styles so we can capture those
        // registered by content functions such as shortcodes.
        if(isset($wp_scripts)) {
            $wp_scripts->queue = array();
        }
        if(isset($wp_styles)) {
            $wp_styles->queue = array();
        }

        // Enqueue jQuery again so it's not added by any
        // third party shortcodes or plugins.
        wp_enqueue_script('jquery');

        // Render the layout.
        ob_start();
        self::render_rows();
        $html = ob_get_clean();

        // Process shortcodes.
        ob_start();
        echo do_shortcode($html);
        $html = ob_get_clean();

        // Print scripts and styles registered by content
        // functions such as shortcodes.
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
     * @method render_content
     */
    static public function render_content($content)
    {
        global $post;

        $post_id        = FLBuilderModel::get_post_id();
        $enabled        = FLBuilderModel::is_builder_enabled();
        $ajax           = defined('DOING_AJAX');
        $global_posts   = FLBuilderModel::get_global_posts();
        $is_global      = in_array($post->ID, $global_posts);
        $in_loop        = in_the_loop();

        if($enabled && !$ajax && ($is_global || $in_loop)) {

            // Remove the builder's render_content filter in case apply_filters
            // is called again by a widget, module or shortcode.
            remove_filter('the_content', 'FLBuilder::render_content');

            // Render the content.
            ob_start();
            echo '<div class="fl-builder-content fl-builder-content-' . $post_id . '">';
            self::render_rows();
            echo '</div>';
            $content = do_shortcode(ob_get_clean());

            // Reapply the builder's render_content filter.
            add_filter('the_content', 'FLBuilder::render_content');
        }

        return $content;
    }

    /**
     * @method render_editor_content
     */
    static public function render_editor_content()
    {
        $rows = FLBuilderModel::get_nodes('row');

        ob_start();

        // Render the modules.
        foreach($rows as $row) {

            $groups = FLBuilderModel::get_nodes('column-group', $row->node);

            foreach($groups as $group) {

                $cols = FLBuilderModel::get_nodes('column', $group->node);

                foreach($cols as $col) {

                    $modules = FLBuilderModel::get_modules($col->node);

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
     * @method render_settings
     */
	static public function render_settings($form = array(), $settings, $return = false)
	{
        $defaults = array(
            'class'     => '',
            'attrs'     => '',
            'title'     => '',
            'tabs'      => array()
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
     * @method render_settings_field
     */
	static public function render_settings_field($name, $field, $settings)
	{
        $i                  = null;
        $is_multiple        = isset($field['multiple']) && $field['multiple'] === true;
        $supports_multiple  = $field['type'] != 'editor' && $field['type'] != 'photo';
        $value              = isset($settings->$name) ? $settings->$name : '';
        $preview            = isset($field['preview']) ? json_encode($field['preview']) : json_encode(array('type' => 'refresh'));

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
            echo '<tr id="fl-field-'. $name .'" class="fl-field" data-type="' . $field['type'] . '" data-preview=\'' . $preview . '\'>';
            include FL_BUILDER_DIR . 'includes/field.php';
            echo '</tr>';
        }
	}

	/**
     * @method render_settings_form
     */
	static public function render_settings_form($type = null, $settings = null)
	{
	    $post_data  = FLBuilderModel::get_post_data();
        $type       = isset($post_data['type']) ? $post_data['type'] : $type;
        $settings   = isset($post_data['settings']) ? $post_data['settings'] : $settings;
        $form       = FLBuilderModel::$settings_forms[$type];

        if(isset($settings) && !empty($settings)) {
            $defaults = FLBuilderModel::get_settings_form_defaults($form['tabs']);
            $settings = (object)array_merge((array)$defaults, (array)$settings);
        }
        else {
            $settings = FLBuilderModel::get_settings_form_defaults($form['tabs']);
        }

        self::render_settings(array(
            'title' => $form['title'],
            'tabs'  => $form['tabs']
        ), $settings);
	}

	/**
     * @method render_global_settings
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
     * @method register_templates_post_type
     */
    static public function register_templates_post_type()
    {
        if(FL_BUILDER_LITE === true) {
            return;
        }

        register_post_type('fl-builder-template', array(
            'public'            => false,
            'labels'            => array(
                'name'               => _x( 'Layout Templates', 'Custom post type label.', 'fl-builder' ),
                'singular_name'      => _x( 'Layout Template', 'Custom post type label.', 'fl-builder' ),
                'menu_name'          => _x( 'Layout Templates', 'Custom post type label.', 'fl-builder' ),
                'name_admin_bar'     => _x( 'Layout Template', 'Custom post type label.', 'fl-builder' ),
                'add_new'            => _x( 'Add New', 'Custom post type label.', 'fl-builder' ),
                'add_new_item'       => _x( 'Add New Layout Template', 'Custom post type label.', 'fl-builder' ),
                'new_item'           => _x( 'New Layout Template', 'Custom post type label.', 'fl-builder' ),
                'edit_item'          => _x( 'Edit Layout Template', 'Custom post type label.', 'fl-builder' ),
                'view_item'          => _x( 'View Layout Template', 'Custom post type label.', 'fl-builder' ),
                'all_items'          => _x( 'All Layout Templates', 'Custom post type label.', 'fl-builder' ),
                'search_items'       => _x( 'Search Layout Templates', 'Custom post type label.', 'fl-builder' ),
                'parent_item_colon'  => _x( 'Parent Layout Templates:', 'Custom post type label.', 'fl-builder' ),
                'not_found'          => _x( 'No layout templates found.', 'Custom post type label.', 'fl-builder' ),
                'not_found_in_trash' => _x( 'No layout templates found in Trash.', 'Custom post type label.', 'fl-builder' )
        	),
            'supports'          => array(
                'title'
            ),
            'publicly_queryable'    => true
        ));
    }

    /**
     * @method render_template_selector
     */
    static public function render_template_selector()
    {
        if(file_exists(FL_BUILDER_DIR . 'includes/template-selector.php')) {

            $enabled_templates  = FLBuilderModel::get_enabled_templates();
            $user_templates     = FLBuilderModel::get_user_templates();
            $templates          = FLBuilderModel::get_templates();
            $num_rows           = FLBuilderModel::count_nodes('row');

            include FL_BUILDER_DIR . 'includes/template-selector.php';

            if(defined('DOING_AJAX')) {
                die();
    		}
		}
    }

    /**
     * @method render_user_template_settings
     */
    static public function render_user_template_settings()
    {
        $defaults = FLBuilderModel::get_settings_form_defaults(FLBuilderModel::$settings_forms['user_template']['tabs']);
        $form     = FLBuilderModel::$settings_forms['user_template'];

        FLBuilder::render_settings(array(
            'class'   => 'fl-builder-user-template-settings',
            'title'   => $form['title'],
            'tabs'    => $form['tabs']
        ), $defaults);
    }

	/**
     * @method render_template
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
     * @method render_icon_selector
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
     * @method render_rows
     */
    static public function render_rows()
    {
        $rows = FLBuilderModel::get_nodes('row');

        foreach($rows as $row) {
            self::render_row($row);
        }
    }

	/**
     * @method render_row
     */
	static public function render_row($row)
	{
        $groups = FLBuilderModel::get_nodes('column-group', $row->node);

		include FL_BUILDER_DIR . 'includes/row.php';
	}

	/**
     * @method render_new_row
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
     * @method render_row_class
     */
	static public function render_row_class($row)
	{
	    echo 'fl-row';
	    echo ' fl-row-' . $row->settings->width . '-width';
	    echo ' fl-row-bg-' . $row->settings->bg_type;
	    echo ' fl-node-' . $row->node;

	    if(!empty($row->settings->class)) {
		    echo apply_filters( 'fl_builder_row_custom_class', ' ' . $row->settings->class, $row );
	    }
	    if(!empty($row->settings->responsive_display)) {
	        echo ' fl-visible-' . $row->settings->responsive_display;
	    }
	}

	/**
     * @method render_row_content_class
     */
	static public function render_row_content_class($row)
	{
	    echo 'fl-row-content';
	    echo ' fl-row-' . $row->settings->content_width . '-width';
	    echo ' fl-node-content';
	}

	/**
     * @method render_row_data_attrs
     */
	static public function render_row_data_attrs($row)
	{
	    echo ' data-node="' . $row->node . '"';

	    if($row->settings->bg_type == 'parallax' && !empty($row->settings->bg_parallax_image_src)) {
	        echo ' data-parallax-speed="' . $row->settings->bg_parallax_speed . '"';
	        echo ' data-parallax-image="' . $row->settings->bg_parallax_image_src . '"';
	    }
	}

	/**
     * @method render_row_bg
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
     * @method render_row_settings
     */
	static public function render_row_settings($node_id = null)
	{
	    $post_data  = FLBuilderModel::get_post_data();
        $node_id    = isset($post_data['node_id']) ? $post_data['node_id'] : $node_id;
        $node       = FLBuilderModel::get_node($node_id);
        $settings   = $node->settings;
        $form       = FLBuilderModel::$settings_forms['row'];

        self::render_settings(array(
            'class'     => 'fl-builder-row-settings',
            'attrs'     => 'data-node="'. $node->node .'"',
            'title'     => $form['title'],
            'tabs'      => $form['tabs']
        ), $settings);
	}

	/**
     * @method render_column_group
     */
	static public function render_column_group($group)
	{
        $cols = FLBuilderModel::get_nodes('column', $group->node);

        include FL_BUILDER_DIR . 'includes/column-group.php';
	}

	/**
     * @method render_new_column_group
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

	/**
     * @method render_column_settings
     */
	static public function render_column_settings($node_id = null)
	{
	    $post_data  = FLBuilderModel::get_post_data();
        $node_id    = isset($post_data['node_id']) ? $post_data['node_id'] : $node_id;
        $node       = FLBuilderModel::get_node($node_id);
        $settings   = $node->settings;
        $form       = FLBuilderModel::$settings_forms['col'];

        self::render_settings(array(
            'class'     => 'fl-builder-col-settings',
            'attrs'     => 'data-node="'. $node->node .'"',
            'title'     => $form['title'],
            'tabs'      => $form['tabs']
        ), $settings);
	}

	/**
     * @method render_column_class
     */
	static public function render_column_class($col)
	{
	    echo 'fl-col';

	    if($col->settings->size <= 50) {
	        echo ' fl-col-small';
        }
        if(!empty($col->settings->class)) {
		    echo apply_filters( 'fl_builder_column_custom_class', ' ' . $col->settings->class, $col );
	    }
	    if(!empty($col->settings->responsive_display)) {
	        echo ' fl-visible-' . $col->settings->responsive_display;
	    }

	    echo ' fl-node-' . $col->node;
	}

    /**
     * @method render_modules
     */
    static public function render_modules($parent_id)
    {
        $modules = FLBuilderModel::get_modules($parent_id);

        foreach($modules as $module) {
            $settings = $module->settings;
            include FL_BUILDER_DIR . 'includes/module.php';
        }
    }

	/**
     * @method render_new_module_settings
     */
	static public function render_new_module_settings($parent_id = null, $type = null, $position = false)
	{
	    $post_data      = FLBuilderModel::get_post_data();
        $parent_id 	    = isset($post_data['parent_id']) ? $post_data['parent_id'] : $parent_id;
        $type 		    = isset($post_data['type']) ? $post_data['type'] : $type;
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
	        'layout'    => self::render_preview(true),
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
     * @method render_module_settings
     */
	static public function render_module_settings($node_id = null, $type = null, $parent_id = null, $return = false)
	{
	    $post_data  = FLBuilderModel::get_post_data();
        $node_id    = isset($post_data['node_id']) ? $post_data['node_id'] : $node_id;
        $type       = isset($post_data['type']) ? $post_data['type'] : $type;
        $parent_id  = isset($post_data['parent_id']) ? $post_data['parent_id'] : $parent_id;

        if($node_id) {
            $module     = FLBuilderModel::get_module($node_id);
            $settings   = $module->settings;
        }
        else {
            $module     = FLBuilderModel::$modules[$type];
            $settings   = FLBuilderModel::get_module_defaults($type);
        }

        if(file_exists($module->dir .'css/settings.css')) {
            echo '<link class="fl-builder-settings-css" rel="stylesheet" href="'. $module->url .'css/settings.css" />';
        }
        if(file_exists($module->dir .'js/settings.js')) {
            echo '<script class="fl-builder-settings-js" src="'. $module->url .'js/settings.js"></script>';
        }

        echo self::render_settings(array(
            'class' => 'fl-builder-module-settings fl-builder-'. $type .'-settings',
            'attrs' => 'data-node="'. $node_id .'" data-parent="'. $parent_id .'" data-type="'. $type .'"',
            'title' => sprintf( _x( '%s Settings', '%s stands for module name.', 'fl-builder' ), $module->name ),
            'tabs'  => $module->form
        ), $settings, $return);
	}

    /**
     * @method render_module_html
     */
    static public function render_module_html($type, $settings, $module = null)
    {
        // Settings
        $defaults = FLBuilderModel::get_module_defaults($type);
        $settings = FLBuilderUtils::array_to_object($settings);
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
     * @method render_module_class
     */
	static public function render_module_class($module)
	{
	    echo 'fl-module';
	    echo ' fl-module-' . $module->settings->type;
	    echo ' fl-node-' . $module->node;

	    if(!empty($module->settings->class)) {
	        echo apply_filters( 'fl_builder_module_custom_class', ' ' . $module->settings->class, $module );
	    }
	    if(!empty($module->settings->responsive_display)) {
	        echo ' fl-visible-' . $module->settings->responsive_display;
	    }
	    if(!empty($module->settings->animation)) {
	        echo ' fl-animation fl-' . $module->settings->animation;
	    }
	}

	/**
     * @method render_module_data_attrs
     */
	static public function render_module_data_attrs($module)
	{
        echo ' data-node="' . $module->node . '" ';
        echo ' data-animation-delay="' . $module->settings->animation_delay . '" ';

	    if(FLBuilderModel::is_builder_active()) {
	        echo ' data-parent="' . $module->parent . '" ';
	        echo ' data-type="' . $module->settings->type . '" ';
	        echo ' data-name="' . $module->name . '" ';
	    }
	}

    /**
     * @method render_module_css
     */
    static public function render_module_css($type, $id, $settings)
    {
        // Settings
        $global_settings = FLBuilderModel::get_global_settings();
        $defaults = FLBuilderModel::get_module_defaults($type);
        $settings = FLBuilderUtils::array_to_object($settings);
        $settings = (object)array_merge((array)$defaults, (array)$settings);

        // Module
        $class = get_class(FLBuilderModel::$modules[$type]);
        $module = new $class();
        $module->settings = $settings;

        include $module->dir .'includes/frontend.css.php';
    }

	/**
     * @method render_css
     */
	static public function render_css()
	{
        // Delete the old file.
        FLBuilderModel::delete_asset_cache('css');

        // Get info on the new file.
        $rows               = FLBuilderModel::get_nodes('row');
        $cols               = FLBuilderModel::get_nodes('column');
        $modules            = FLBuilderModel::get_all_modules();
        $global_settings    = FLBuilderModel::get_global_settings();
        $asset_info         = FLBuilderModel::get_asset_info();
        $post_id            = FLBuilderModel::get_post_id();
        $post               = get_post($post_id);
        $compiled           = array();

        // Global css
        $css = file_get_contents(FL_BUILDER_DIR . '/css/fl-builder-layout.css');

        // Responsive css
        if($global_settings->responsive_enabled) {
            $css .= '@media (max-width: '. $global_settings->medium_breakpoint .'px) { ';
            $css .= file_get_contents(FL_BUILDER_DIR . '/css/fl-builder-layout-medium.css');
            $css .= ' }';
            $css .= '@media (max-width: '. $global_settings->responsive_breakpoint .'px) { ';
            $css .= file_get_contents(FL_BUILDER_DIR . '/css/fl-builder-layout-responsive.css');
            $css .= ' }';
        }

        // Global row margins
        $css .= '.fl-row-content-wrap { margin: '. $global_settings->row_margins .'px; }';

        // Global row padding
        $css .= '.fl-row-content-wrap { padding: '. $global_settings->row_padding .'px; }';

        // Global row width
        $css .= '.fl-row-fixed-width { max-width: '. $global_settings->row_width .'px; }';

        // Global row content width
        $css .= '.fl-row-content-wrap.fl-row-fixed-width .fl-row-content { max-width: '. $global_settings->row_width .'px; }';

        // Row instances
        foreach($rows as $row) {

            // Instance row css
            ob_start();
            include FL_BUILDER_DIR . 'includes/row-css.php';
            $css .= ob_get_clean();

            // Instance row margins
            $css .= self::render_row_margins($row);

            // Instance row padding
            $css .= self::render_row_padding($row);

            // Instance row bg positions
            $css .= self::render_row_bg_positions($row);
            $css .= self::render_responsive_row_bg_positions($row);
        }

        // Column instances
        foreach($cols as $col) {

            // Instance column css
            ob_start();
            include FL_BUILDER_DIR . 'includes/column-css.php';
            $css .= ob_get_clean();

            // Instance column margins
            $css .= self::render_column_margins($col);

            // Instance column padding
            $css .= self::render_column_padding($col);
        }

        // Global module margins
        $css .= '.fl-module-content { margin: '. $global_settings->module_margins .'px; }';

        // Modules
        foreach($modules as $module) {

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
            $css .= self::render_responsive_module_margins($module);
        }

        // Default page heading
        if($post && !$global_settings->show_default_heading && ($post->post_type == 'page' || $post->post_type == 'fl-builder-template')) {
            $css .= $global_settings->default_heading_selector . ' { display:none; }';
        }

        // Save the css
        if(!empty($css)) {
            $css = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css);
            $css = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $css);
            file_put_contents($asset_info['css'], $css);
        }
	}

	/**
     * @method render_row_margins
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
     * @method render_row_padding
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
     * @method render_row_bg_positions
     */
    static public function render_row_bg_positions($row)
    {
        $settings   = $row->settings;
        $positions  = '';
        $css        = '';

        if($settings->margin_top != '' || $settings->border_top != '') {
            $positions  .= 'top:' . ($settings->margin_top + $settings->border_top) . 'px;';
        }
        if($settings->margin_bottom != ''|| $settings->border_bottom != '') {
            $positions  .= 'bottom:' . ($settings->margin_bottom + $settings->border_bottom) . 'px;';
        }
        if($settings->margin_left != ''|| $settings->border_left != '') {
            $positions  .= 'left:' . ($settings->margin_left + $settings->border_left) . 'px;';
        }
        if($settings->margin_right != ''|| $settings->border_right != '') {
            $positions  .= 'right:' . ($settings->margin_right + $settings->border_right) . 'px;';
        }
        if($positions != '') {
            $css .= '.fl-node-' . $row->node . ' .fl-bg-video {' . $positions . '}';
            $css .= '.fl-node-' . $row->node . ' .fl-bg-slideshow {' . $positions . '}';
        }

        return $css;
    }

	/**
     * @method render_responsive_row_bg_positions
     */
    static public function render_responsive_row_bg_positions($row)
    {
        $global_settings    = FLBuilderModel::get_global_settings();
        $settings           = $row->settings;
        $positions          = '';
        $css                = '';

        if($settings->border_top != '') {
            $positions  .= 'top:' . $settings->border_top . 'px;';
        }
        if($settings->border_bottom != '') {
            $positions  .= 'bottom:' . $settings->border_bottom . 'px;';
        }
        if($positions != '') {
            $css .= '@media (max-width: '. $global_settings->responsive_breakpoint .'px) { ';
            $css .= '.fl-node-' . $row->node . ' .fl-bg-video {' . $positions . '}';
            $css .= '.fl-node-' . $row->node . ' .fl-bg-slideshow {' . $positions . '}';
            $css .= ' }';
        }

        return $css;
    }

	/**
     * @method render_column_margins
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
     * @method render_column_padding
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
     * @method render_module_margins
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
     * @method render_responsive_module_margins
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
     * @method render_js
     */
    static public function render_js()
    {
        // Delete the old file.
        FLBuilderModel::delete_asset_cache('js');

        // Get info on the new file.
        $global_settings    = FLBuilderModel::get_global_settings();
        $rows               = FLBuilderModel::get_nodes('row');
        $modules            = FLBuilderModel::get_all_modules();
        $asset_info         = FLBuilderModel::get_asset_info();
        $compiled           = array();
        $js                 = '';

        // Main JS
        $js .= file_get_contents(FL_BUILDER_DIR . 'js/fl-builder-layout.js');

        // Instance Row JS
        foreach($rows as $row) {

            $settings   = $row->settings;
            $id         = $row->node;

            ob_start();
            include FL_BUILDER_DIR . 'includes/row-js.php';
            $js .= ob_get_clean();
        }

        // Modules
        foreach($modules as $module) {

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

        // Add the AJAX url global.
        $js .= "var wpAjaxUrl = '" . admin_url('admin-ajax.php') . "';";

        // Add the builder url global.
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
     * @method log
     */
    static public function log($data)
    {
        ob_start();
        print_r($data);
        error_log(ob_get_clean());
    }
}