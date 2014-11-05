<?php

/**
 * Post admin class.
 *
 * @class FLBuilderAdminPosts
 */
final class FLBuilderAdminPosts {
    
    /** 
     * @method init
     */
    static public function init()
    {
        global $pagenow;
        
        if(in_array($pagenow, array('post.php', 'post-new.php'))) {
            
            $post_types = FLBuilderModel::get_post_types();
            $screen     = get_current_screen();

            if(in_array($screen->post_type, $post_types)) {
                add_filter('admin_body_class', 'FLBuilderAdminPosts::body_class');
                add_action('admin_enqueue_scripts', 'FLBuilderAdminPosts::styles_scripts');
                add_action('edit_form_after_title', 'FLBuilderAdminPosts::render');
            }
        }
    }
	 
	/**
     * @method styles_scripts
     */	 
	static public function styles_scripts()
	{
        global $wp_version;
        
        // Styles
		wp_enqueue_style('fl-builder-admin-posts', FL_BUILDER_URL . 'css/fl-builder-admin-posts.css', array(), FL_BUILDER_VERSION);
		
		// Legacy WP Styles (3.7 and below)
        if(version_compare($wp_version, '3.7', '<=')) {
            wp_enqueue_style('fl-builder-admin-posts-legacy', FL_BUILDER_URL . 'css/fl-builder-admin-posts-legacy.css', array(), FL_BUILDER_VERSION);
        }
		
		// Scripts
		wp_enqueue_script('json2');
		wp_enqueue_script('fl-builder-admin-posts', FL_BUILDER_URL . 'js/fl-builder-admin-posts.js', array(), FL_BUILDER_VERSION);
	}
	
	/**
     * @method body_class
     */	 
	static public function body_class($classes)
	{
        global $wp_version;
        
        // Builder body class
        if(FLBuilderModel::is_builder_enabled()) {
            $classes .= ' fl-builder-enabled';
        }
        
        // Pre WP 3.8 body class
        if(version_compare($wp_version, '3.8', '<')) {
            $classes .= ' fl-pre-wp-3-8';
        }
        
        return $classes;
	}
	 
	/**
     * @method render
     */	 
	static public function render()
	{
	    global $post;
	    
        $enabled = FLBuilderModel::is_builder_enabled();
        
		include FL_BUILDER_DIR . 'includes/admin-posts.php';
	}
	
	/**
     * @method render_row_actions_link
     */	 
	static public function render_row_actions_link($actions) 
    {
        global $post;
        	
        if(current_user_can('edit_post', $post->ID) && wp_check_post_lock($post->ID) === false) {
        	
        	$post_types = FLBuilderModel::get_post_types();

            if(in_array($post->post_type, $post_types)) {
                $actions['fl-builder'] = '<a href="' . FLBuilderModel::get_edit_url() . '">' . FLBuilderModel::get_branding() . '</a>';
            }
        }
        
    	return $actions;
    }
	
	/**
     * @method redirect_post_location
     */	 
	static public function redirect_post_location($location) 
    {
    	if(isset($_POST['fl-builder-redirect'])) {
        	$location = $_POST['fl-builder-redirect'];
    	}
    	
    	return $location;
    }
}