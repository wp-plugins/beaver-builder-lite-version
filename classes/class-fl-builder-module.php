<?php

/**
 * Base class that gets extended by all module classes.
 *
 * @class FLBuilderModule
 */
class FLBuilderModule {
    
    /** 
     * A unique ID for the module. 
     *
     * @property $node
     * @type string
     */
    public $node;
    
    /** 
     * A unique ID for the module's parent. 
     *
     * @property $parent
     * @type number
     */
    public $parent;
    
    /** 
     * The sort order for this module. 
     *
     * @property $position
     * @type number
     */
    public $position;
    
    /** 
     * A display name for the module.
     *
     * @property $name
     * @type string
     */
    public $name;
    
    /** 
     * A description to display for the module.
     *
     * @property $description
     * @type string
     */
    public $description;
    
    /** 
     * The category this module belongs to.
     *
     * @property $category
     * @type string
     */
    public $category;
    
    /** 
     * Must be the module's folder name.
     *
     * @property $slug
     * @type string
     */
    public $slug;
    
    /** 
     * The module's directory path. 
     *
     * @property $dir
     * @type string
     */
    public $dir;
    
    /** 
     * The module's directory url. 
     *
     * @property $url
     * @type string
     */
    public $url;
    
    /** 
     * An array of form settings.
     *
     * @property $form
     * @type array
     */
    public $form = array();
    
    /** 
     * Whether this module is enabled on the 
     * frontend or not.
     *
     * @property $enabled
     * @type boolean
     */
    public $enabled = true;
    
    /** 
     * Whether this module's content should
     * be exported to the WP editor or not.
     *
     * @property $editor_export
     * @type boolean
     */
    public $editor_export = true;
    
    /** 
     * The module settings object.
     *
     * @property $settings
     * @type object
     */
    public $settings;
    
    /** 
     * Additional CSS to enqueue.
     *
     * @property $css
     * @type array
     */
    public $css = array();
    
    /** 
     * Additional JS to enqueue.
     *
     * @property $js
     * @type array
     */
    public $js = array();

    /** 
     * @method __construct
     */
    public function __construct($params)
    {
        $class_info             = new ReflectionClass($this);
        $class_path             = $class_info->getFileName();
        $dir_path               = dirname($class_path);
        $this->name             = $params['name'];
        $this->description      = $params['description'];
        $this->category         = $params['category'];
        $this->slug             = basename($class_path, '.php');
        $this->enabled          = isset($params['enabled']) ? $params['enabled'] : true;
        $this->editor_export    = isset($params['editor_export']) ? $params['editor_export'] : true;
        
        if(is_child_theme() && stristr($dir_path, get_stylesheet_directory())) {
	        $this->url = str_replace(get_stylesheet_directory(), get_stylesheet_directory_uri(), $dir_path) . '/';
	        $this->dir = $dir_path . '/';
        }
        else if(stristr($dir_path, get_template_directory())) {
            $this->url = str_replace(get_template_directory(), get_template_directory_uri(), $dir_path) . '/';
            $this->dir = $dir_path . '/';
            error_log($this->url);
        }
        else {                
            $this->url = isset($params['url']) ? $params['url'] : FL_BUILDER_URL . 'modules/' . $this->slug . '/';
            $this->dir = isset($params['dir']) ? $params['dir'] : FL_BUILDER_DIR . 'modules/' . $this->slug . '/';
        }
    }

    /** 
     * Used to enqueue additional frontend styles. Do not enqueue 
     * frontend.css or frontend.responsive.css as those will be 
     * enqueued automatically.
     *
     * @method add_css
     */   
    public function add_css($handle, $src = null, $deps = null, $ver = null, $media = null)
    {
        $this->css[$handle] = array($src, $deps, $ver, $media);
    }

    /** 
     * Used to enqueue additional frontend scripts. Do not enqueue
     * frontend.js as that will be enqueued automatically.
     *
     * @method add_js
     */   
    public function add_js($handle, $src = null, $deps = null, $ver = null, $in_footer = null)
    {
        $this->js[$handle] = array($src, $deps, $ver, $in_footer);
    }

    /** 
     * Enqueues the needed styles for any icon fields
     * in this module.
     *
     * @method enqueue_icon_styles
     */      
    public function enqueue_icon_styles()
    {
        FLBuilderIcons::enqueue_styles_for_module( $this );
    }

    /** 
     * Should be overridden by child classes to enqueue
     * additional css/js using the add_css and add_js methods.
     *
     * @method enqueue_scripts
     */      
    public function enqueue_scripts()
    {

    }

    /** 
     * Should be overridden by child classes to
     * work with settings data before it is saved. 
     *
     * @method update
     * @param $settings {object}
     */      
    public function update($settings)
    {
        return $settings;
    }

    /** 
     * Should be overridden by child classes to
     * work with a module before it is deleted.
     *
     * @method delete
     */      
    public function delete()
    {

    }
}