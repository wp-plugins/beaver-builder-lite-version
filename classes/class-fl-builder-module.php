<?php

/**
 * Base class that gets extended by all module classes.
 *
 * @since 1.0
 */
class FLBuilderModule {
	
	/** 
	 * A unique ID for the module. 
	 *
	 * @since 1.0
	 * @var string $node
	 */
	public $node;
	
	/** 
	 * A unique ID for the module's parent. 
	 *
	 * @since 1.0
	 * @var int $parent
	 */
	public $parent;
	
	/** 
	 * The sort order for this module. 
	 *
	 * @since 1.0
	 * @var int $position
	 */
	public $position;
	
	/** 
	 * A display name for the module.
	 *
	 * @since 1.0
	 * @var string $name
	 */
	public $name;
	
	/** 
	 * A description to display for the module.
	 *
	 * @since 1.0
	 * @var string $description
	 */
	public $description;
	
	/** 
	 * The category this module belongs to.
	 *
	 * @since 1.0
	 * @var string $category
	 */
	public $category;
	
	/** 
	 * Must be the module's folder name.
	 *
	 * @since 1.0
	 * @var string $slug
	 */
	public $slug;
	
	/** 
	 * The module's directory path. 
	 *
	 * @since 1.0
	 * @var string $dir
	 */
	public $dir;
	
	/** 
	 * The module's directory url. 
	 *
	 * @since 1.0
	 * @var string $url
	 */
	public $url;
	
	/** 
	 * An array of form settings.
	 *
	 * @since 1.0
	 * @var array $form
	 */
	public $form = array();
	
	/** 
	 * Whether this module is enabled on the 
	 * frontend or not.
	 *
	 * @since 1.0
	 * @var boolean $enabled
	 */
	public $enabled = true;
	
	/** 
	 * Whether this module's content should
	 * be exported to the WP editor or not.
	 *
	 * @since 1.0
	 * @var boolean $editor_export
	 */
	public $editor_export = true;
	
	/** 
	 * The module settings object.
	 *
	 * @since 1.0
	 * @var object $settings
	 */
	public $settings;
	
	/** 
	 * Additional CSS to enqueue.
	 *
	 * @since 1.0
	 * @var array $css
	 */
	public $css = array();
	
	/** 
	 * Additional JS to enqueue.
	 *
	 * @since 1.0
	 * @var array $js
	 */
	public $js = array();

	/** 
	 * Module constructor. 
	 * 
	 * @since 1.0
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
		
		// We need to normalize the paths here since path comparisons 
		// break on Windows because they use backslashes.
		$dir_path                   = str_replace( '\\', '/', $dir_path );
		$stylesheet_directory       = str_replace( '\\', '/', get_stylesheet_directory() );
		$stylesheet_directory_uri   = str_replace( '\\', '/', get_stylesheet_directory_uri() );
		$template_directory         = str_replace( '\\', '/', get_template_directory() );
		$template_directory_uri     = str_replace( '\\', '/', get_template_directory_uri() );
		
		// Find the right paths.
		if(is_child_theme() && stristr($dir_path, $stylesheet_directory)) {
			$this->url = str_replace($stylesheet_directory, $stylesheet_directory_uri, $dir_path) . '/';
			$this->dir = $dir_path . '/';
		}
		else if(stristr($dir_path, $template_directory)) {
			$this->url = str_replace($template_directory, $template_directory_uri, $dir_path) . '/';
			$this->dir = $dir_path . '/';
		}
		else {                
			$this->url = isset($params['url']) ? $params['url'] : FL_BUILDER_URL . 'modules/' . $this->slug . '/';
			$this->dir = isset($params['dir']) ? $params['dir'] : FL_BUILDER_DIR . 'modules/' . $this->slug . '/';
		}
	}

	/** 
	 * Used to enqueue additional frontend styles. Do not enqueue 
	 * frontend.css or frontend.responsive.css as those will be 
	 * enqueued automatically. Params are the same as those used in 
	 * WordPress' wp_enqueue_style function.
	 *
	 * @since 1.0
	 * @param string $handle
	 * @param string $src
	 * @param array $deps
	 * @param string $ver
	 * @param string $media
	 * @return void
	 */   
	public function add_css($handle, $src = null, $deps = null, $ver = null, $media = null)
	{
		$this->css[$handle] = array($src, $deps, $ver, $media);
	}

	/** 
	 * Used to enqueue additional frontend scripts. Do not enqueue
	 * frontend.js as that will be enqueued automatically. Params 
	 * are the same as those used in WordPress' wp_enqueue_script function.
	 *
	 * @since 1.0
	 * @param string $handle
	 * @param string $src
	 * @param array $deps
	 * @param string $ver
	 * @param bool $in_footer
	 * @return void
	 */   
	public function add_js($handle, $src = null, $deps = null, $ver = null, $in_footer = null)
	{
		$this->js[$handle] = array($src, $deps, $ver, $in_footer);
	}

	/** 
	 * Enqueues the needed styles for any icon fields
	 * in this module.
	 *
	 * @since 1.4.6
	 * @return void
	 */      
	public function enqueue_icon_styles()
	{
		FLBuilderIcons::enqueue_styles_for_module( $this );
	}

	/** 
	 * Should be overridden by subclasses to enqueue
	 * additional css/js using the add_css and add_js methods.
	 *
	 * @since 1.0
	 * @return void
	 */      
	public function enqueue_scripts()
	{

	}

	/** 
	 * Should be overridden by subclasses to
	 * work with settings data before it is saved. 
	 *
	 * @since 1.0
	 * @param object A settings object that is going to be saved.
	 * @return object
	 */      
	public function update($settings)
	{
		return $settings;
	}

	/** 
	 * Should be overridden by subclasses to
	 * work with a module before it is deleted.
	 *
	 * @since 1.0
	 * @return void
	 */      
	public function delete()
	{

	}
}