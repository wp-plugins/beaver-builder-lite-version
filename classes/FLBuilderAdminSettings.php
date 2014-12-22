<?php

/**
 * Settings admin class.
 *
 * @class FLBuilderAdminSettings
 */
final class FLBuilderAdminSettings {
    
    /** 
     * @method init
     */
    static public function init()
    {
        add_action('admin_menu', 'FLBuilderAdminSettings::menu');
            
        if(isset($_REQUEST['page']) && $_REQUEST['page'] == 'fl-builder-settings') {
            add_action('admin_enqueue_scripts', 'FLBuilderAdminSettings::styles_scripts');
            self::save();
        }
    }
	 
	/**
     * @method styles_scripts
     */	 
	static public function styles_scripts()
	{
        // Styles
		wp_enqueue_style('fl-builder-admin-settings', FL_BUILDER_URL . 'css/fl-builder-admin-settings.css', array(), FL_BUILDER_VERSION);

		// Scripts
		wp_enqueue_script('fl-builder-admin-settings', FL_BUILDER_URL . 'js/fl-builder-admin-settings.js', array(), FL_BUILDER_VERSION);
	}
	
	/**
     * @method menu
     */
	static public function menu() 
	{
	    if(current_user_can('delete_plugins')) {
    	    
    	    $title = FLBuilderModel::get_branding();
    	    $cap   = 'delete_plugins';
    	    $slug  = 'fl-builder-settings';
    	    $func  = 'FLBuilderAdminSettings::render';
    	    
            add_submenu_page('options-general.php', $title, $title, $cap, $slug, $func);
		}
	}
	 
	/**
     * @method render
     */	 
	static public function render()
	{
	    if(file_exists(FL_BUILDER_DIR . 'includes/admin-settings.php')) {
		    include FL_BUILDER_DIR . 'includes/admin-settings.php';
		}
	}
	 
	/**
     * @method save
     * @private
     */	 
	static private function save()
	{
	    // Only admins can save settings.
	    if(!current_user_can('delete_plugins')) {
    	    return;
	    }
	    
		self::saveEnabledModules();
		self::saveEnabledTemplates();
		self::saveEnabledPostTypes();
		self::saveEditingCapability();
		self::saveBranding();
		self::uninstall();
	}
	 
	/**
     * @method saveEnabledModules
     * @private
     */	 
	static private function saveEnabledModules()
	{
    	$modules = array();
    	
    	if(isset($_POST['fl-modules-nonce']) && wp_verify_nonce($_POST['fl-modules-nonce'], 'modules')) {
        
            if(class_exists('FLBuilderMultisiteSettings') && !isset($_POST['fl-override-ms'])) {
                delete_option('_fl_builder_enabled_modules');
            }
            else {
                
                if(is_array($_POST['fl-modules'])) {
                    $modules = array_map('sanitize_text_field', $_POST['fl-modules']);
                }
                
                update_option('_fl_builder_enabled_modules', $modules);
            }
        }
    }
	 
	/**
     * @method saveEnabledTemplates
     * @private
     */	 
	static private function saveEnabledTemplates()
	{
    	if(isset($_POST['fl-templates-nonce']) && wp_verify_nonce($_POST['fl-templates-nonce'], 'templates')) {
        
            if(class_exists('FLBuilderMultisiteSettings') && !isset($_POST['fl-override-ms'])) {
                delete_option('_fl_builder_enabled_templates');
            }
            else {
        	
        	    $enabled_templates = sanitize_text_field($_POST['fl-template-settings']);
        	
                update_option('_fl_builder_enabled_templates', $enabled_templates);
            }
        }
    }
	 
	/**
     * @method saveEnabledPostTypes
     * @private
     */	 
	static private function saveEnabledPostTypes()
	{
    	$post_types = array();
    	
    	if(isset($_POST['fl-post-types-nonce']) && wp_verify_nonce($_POST['fl-post-types-nonce'], 'post-types')) {
        
            if(class_exists('FLBuilderMultisiteSettings') && !isset($_POST['fl-override-ms'])) {
                delete_option('_fl_builder_post_types');
            }
            else {
                
                if(is_array($_POST['fl-post-types'])) {
                    $post_types = array_map('sanitize_text_field', $_POST['fl-post-types']);
                }
                
                update_option('_fl_builder_post_types', $post_types);
            }
        }
    }
	 
	/**
     * @method saveEditingCapability
     * @private
     */	 
	static private function saveEditingCapability()
	{
        if(isset($_POST['fl-editing-nonce']) && wp_verify_nonce($_POST['fl-editing-nonce'], 'editing')) {
            
            if(class_exists('FLBuilderMultisiteSettings') && !isset($_POST['fl-override-ms'])) {
                delete_option('_fl_builder_editing_capability');
            }
            else {
                
                $capability = sanitize_text_field($_POST['fl-editing-capability']);
                
                update_option('_fl_builder_editing_capability', $capability);
            }
        }
    }
	 
	/**
     * @method saveBranding
     * @private
     */	 
	static private function saveBranding()
	{
        if(isset($_POST['fl-branding-nonce']) && wp_verify_nonce($_POST['fl-branding-nonce'], 'branding')) {
            
            $branding       = wp_kses_post($_POST['fl-branding']);
            $branding_icon  = sanitize_text_field($_POST['fl-branding-icon']);
            
            update_option('_fl_builder_branding', $branding);
            update_option('_fl_builder_branding_icon', $branding_icon);
        }
    }
	 
	/**
     * @method uninstall
     * @private
     */	 
	static private function uninstall()
	{
        if(!current_user_can('delete_plugins')) {
            return;	
        }
        else if(isset($_POST['fl-uninstall']) && wp_verify_nonce($_POST['fl-uninstall'], 'uninstall')) {
            if(is_multisite() && class_exists('FLBuilderMultisite')) {
                FLBuilderMultisite::uninstall();
            }
            else {
                self::uninstall();
            }
        }
    }
}