<?php

/**
 * @class FLPhotoModule
 */
class FLPhotoModule extends FLBuilderModule {

	/**
	 * @property $data
	 */
	public $data = null;

	/**
	 * @property $_editor
	 * @protected
	 */
	protected $_editor = null;

	/**
	 * @method __construct
	 */
	public function __construct()
	{
		parent::__construct(array(
			'name'          => __('Photo', 'fl-builder'),
			'description'   => __('Upload a photo or display one from the media library.', 'fl-builder'),
			'category'      => __('Basic Modules', 'fl-builder')
		));
	}

	/**
	 * @method enqueue_scripts
	 */
	public function enqueue_scripts()
	{
		if($this->settings && $this->settings->link_type == 'lightbox') {
			$this->add_js('jquery-magnificpopup');
			$this->add_css('jquery-magnificpopup');
		}
	}

	/**
	 * @method update
	 * @param $settings {object}
	 */
	public function update($settings)
	{
		// Make sure we have a photo_src property.
		if(!isset($settings->photo_src)) {
			$settings->photo_src = '';
		}

		// Cache the attachment data.
		$data = FLBuilderPhoto::get_attachment_data($settings->photo);

		if($data) {
			$settings->data = $data;
		}

		// Save a crop if necessary.
		$this->crop();

		return $settings;
	}

	/**
	 * @method delete
	 */
	public function delete()
	{
		$cropped_path = $this->_get_cropped_path();

		if(file_exists($cropped_path['path'])) {
			unlink($cropped_path['path']);
		}
	}

	/**
	 * @method crop
	 */
	public function crop()
	{
		// Delete an existing crop if it exists.
		$this->delete();

		// Do a crop.
		if(!empty($this->settings->crop)) {

			$editor = $this->_get_editor();

			if(!$editor || is_wp_error($editor)) {
				return false;
			}

			$cropped_path = $this->_get_cropped_path();
			$size         = $editor->get_size();
			$new_width    = $size['width'];
			$new_height   = $size['height'];

			// Get the crop ratios.
			if($this->settings->crop == 'landscape') {
				$ratio_1 = 1.43;
				$ratio_2 = .7;
			}
			elseif($this->settings->crop == 'panorama') {
				$ratio_1 = 2;
				$ratio_2 = .5;
			}
			elseif($this->settings->crop == 'portrait') {
				$ratio_1 = .7;
				$ratio_2 = 1.43;
			}
			elseif($this->settings->crop == 'square') {
				$ratio_1 = 1;
				$ratio_2 = 1;
			}
			elseif($this->settings->crop == 'circle') {
				$ratio_1 = 1;
				$ratio_2 = 1;
			}

			// Get the new width or height.
			if($size['width'] / $size['height'] < $ratio_1) {
				$new_height = $size['width'] * $ratio_2;
			}
			else {
				$new_width = $size['height'] * $ratio_1;
			}

			// Make sure we have enough memory to crop.
			@ini_set('memory_limit', '300M');

			// Crop the photo.
			$editor->resize($new_width, $new_height, true);

			// Save the photo.
			$editor->save($cropped_path['path']);

			// Return the new url.
			return $cropped_path['url'];
		}

		return false;
	}

	/**
	 * @method get_data
	 */
	public function get_data()
	{
		if(!$this->data) {

			// Photo source is set to "url".
			if($this->settings->photo_source == 'url') {
				$this->data = new stdClass();
				$this->data->alt = $this->settings->caption;
				$this->data->caption = $this->settings->caption;
				$this->data->link = $this->settings->photo_url;
				$this->data->url = $this->settings->photo_url;
				$this->settings->photo_src = $this->settings->photo_url;
			}

			// Photo source is set to "library".
			else if(is_object($this->settings->photo)) {
				$this->data = $this->settings->photo;
			}
			else {
				$this->data = FLBuilderPhoto::get_attachment_data($this->settings->photo);
			}

			// Data object is empty, use the settings cache.
			if(!$this->data && isset($this->settings->data)) {
				$this->data = $this->settings->data;
			}
		}

		return $this->data;
	}

	/**
	 * @method get_src
	 */
	public function get_src()
	{
		$src = $this->_get_uncropped_url();

		// Return a cropped photo.
		if($this->_has_source() && !empty($this->settings->crop)) {

			$cropped_path = $this->_get_cropped_path();

			// See if the cropped photo already exists.
			if(file_exists($cropped_path['path'])) {
				$src = $cropped_path['url'];
			}
			// It doesn't, check if this is a demo image.
			elseif(stristr($src, FL_BUILDER_DEMO_URL) && !stristr(FL_BUILDER_DEMO_URL, $_SERVER['HTTP_HOST'])) {
				$src = $this->_get_cropped_demo_url();
			}
			// It doesn't, check if this is a OLD demo image.
			elseif(stristr($src, FL_BUILDER_OLD_DEMO_URL)) {
				$src = $this->_get_cropped_demo_url();
			}
			// A cropped photo doesn't exist, try to create one.
			else {

				$url = $this->crop();

				if($url) {
					$src = $url;
				}
			}
		}

		return $src;
	}

	/**
	 * @method get_link
	 */
	public function get_link()
	{
		$photo = $this->get_data();

		if($this->settings->link_type == 'url') {
			$link = $this->settings->link_url;
		}
		else if($this->settings->link_type == 'lightbox') {
			$link = $photo->url;
		}
		else if($this->settings->link_type == 'file') {
			$link = $photo->url;
		}
		else if($this->settings->link_type == 'page') {
			$link = $photo->link;
		}
		else {
			$link = '';
		}

		return $link;
	}

	/**
	 * @method get_alt
	 */
	public function get_alt()
	{
		$photo = $this->get_data();

		if(!empty($photo->alt)) {
			return htmlspecialchars($photo->alt);
		}
		else if(!empty($photo->description)) {
			return htmlspecialchars($photo->description);
		}
		else if(!empty($photo->caption)) {
			return htmlspecialchars($photo->caption);
		}
		else if(!empty($photo->title)) {
			return htmlspecialchars($photo->title);
		}
	}

	/**
	 * @method get_attributes
	 */
	public function get_attributes()
	{
		$attrs = '';
		
		if ( isset( $this->settings->attributes ) ) {
			foreach ( $this->settings->attributes as $key => $val ) {
				$attrs .= $key . '="' . $val . '" ';
			}
		}
		
		return $attrs;
	}

	/**
	 * @method _has_source
	 * @protected
	 */
	protected function _has_source()
	{
		if($this->settings->photo_source == 'url' && !empty($this->settings->photo_url)) {
			return true;
		}
		else if($this->settings->photo_source == 'library' && !empty($this->settings->photo_src)) {
			return true;
		}

		return false;
	}

	/**
	 * @method _get_editor
	 * @protected
	 */
	protected function _get_editor()
	{
		if($this->_has_source() && $this->_editor === null) {

			$url_path  = $this->_get_uncropped_url();
			$file_path = str_ireplace(home_url(), ABSPATH, $url_path);

			if(file_exists($file_path)) {
				$this->_editor = wp_get_image_editor($file_path);
			}
			else {
				$this->_editor = wp_get_image_editor($url_path);
			}
		}

		return $this->_editor;
	}

	/**
	 * @method _get_cropped_path
	 * @protected
	 */
	protected function _get_cropped_path()
	{
		$crop        = empty($this->settings->crop) ? 'none' : $this->settings->crop;
		$url         = $this->_get_uncropped_url();
		$cache_dir   = FLBuilderModel::get_cache_dir();

		if(empty($url)) {
			$filename    = uniqid(); // Return a file that doesn't exist.
		}
		else {
			$pathinfo    = pathinfo($url);
			$dir         = $pathinfo['dirname'];
			$ext         = $pathinfo['extension'];
			$name        = wp_basename($url, ".$ext");
			$new_ext     = strtolower($ext);
			$filename    = "{$name}-{$crop}.{$new_ext}";
		}

		return array(
			'filename' => $filename,
			'path'     => $cache_dir['path'] . $filename,
			'url'      => $cache_dir['url'] . $filename
		);
	}

	/**
	 * @method _get_uncropped_url
	 * @protected
	 */
	protected function _get_uncropped_url()
	{
		if($this->settings->photo_source == 'url') {
			$url = $this->settings->photo_url;
		}
		else if(!empty($this->settings->photo_src)) {
			$url = $this->settings->photo_src;
		}
		else {
			$url = FL_BUILDER_URL . 'img/pixel.png';
		}

		return $url;
	}

	/**
	 * @method _get_cropped_demo_url
	 * @protected
	 */
	protected function _get_cropped_demo_url()
	{
		$info = $this->_get_cropped_path();

		return FL_BUILDER_DEMO_CACHE_URL . $info['filename'];
	}
}

/**
 * Register the module and its form settings.
 */
FLBuilder::register_module('FLPhotoModule', array(
	'general'       => array( // Tab
		'title'         => __('General', 'fl-builder'), // Tab title
		'sections'      => array( // Tab Sections
			'general'       => array( // Section
				'title'         => '', // Section Title
				'fields'        => array( // Section Fields
					'photo_source'  => array(
						'type'          => 'select',
						'label'         => __('Photo Source', 'fl-builder'),
						'default'       => 'library',
						'options'       => array(
							'library'       => __('Media Library', 'fl-builder'),
							'url'           => __('URL', 'fl-builder')
						),
						'toggle'        => array(
							'library'       => array(
								'fields'        => array('photo')
							),
							'url'           => array(
								'fields'        => array('photo_url', 'caption')
							)
						)
					),
					'photo'         => array(
						'type'          => 'photo',
						'label'         => __('Photo', 'fl-builder')
					),
					'photo_url'     => array(
						'type'          => 'text',
						'label'         => __('Photo URL', 'fl-builder'),
						'placeholder'   => __( 'http://www.example.com/my-photo.jpg', 'fl-builder' )
					),
					'crop'          => array(
						'type'          => 'select',
						'label'         => __('Crop', 'fl-builder'),
						'default'       => '',
						'options'       => array(
							''              => _x( 'None', 'Crop.', 'fl-builder' ),
							'landscape'     => __('Landscape', 'fl-builder'),
							'panorama'      => __('Panorama', 'fl-builder'),
							'portrait'      => __('Portrait', 'fl-builder'),
							'square'        => __('Square', 'fl-builder'),
							'circle'        => __('Circle', 'fl-builder')
						)
					),
					'align'         => array(
						'type'          => 'select',
						'label'         => __('Alignment', 'fl-builder'),
						'default'       => 'center',
						'options'       => array(
							'left'          => __('Left', 'fl-builder'),
							'center'        => __('Center', 'fl-builder'),
							'right'         => __('Right', 'fl-builder')
						)
					)
				)
			),
			'caption'       => array(
				'title'         => __('Caption', 'fl-builder'),
				'fields'        => array(
					'show_caption'  => array(
						'type'          => 'select',
						'label'         => __('Show Caption', 'fl-builder'),
						'default'       => '0',
						'options'       => array(
							'0'             => __('Never', 'fl-builder'),
							'hover'         => __('On Hover', 'fl-builder'),
							'below'         => __('Below Photo', 'fl-builder')
						)
					),
					'caption'       => array(
						'type'          => 'text',
						'label'         => __('Caption', 'fl-builder')
					)
				)
			),
			'link'          => array(
				'title'         => 'Link',
				'fields'        => array(
					'link_type'     => array(
						'type'          => 'select',
						'label'         => __('Link Type', 'fl-builder'),
						'options'       => array(
							''              => _x( 'None', 'Link type.', 'fl-builder' ),
							'url'           => __('URL', 'fl-builder'),
							'lightbox'      => __('Lightbox', 'fl-builder'),
							'file'          => __('Photo File', 'fl-builder'),
							'page'          => __('Photo Page', 'fl-builder')
						),
						'toggle'        => array(
							''              => array(),
							'url'           => array(
								'fields'        => array('link_url', 'link_target')
							),
							'file'          => array(),
							'page'          => array()
						),
						'help'          => __('Link type applies to how the image should be linked on click. You can choose a specific URL, the individual photo or a separate page with the photo.', 'fl-builder'),
						'preview'         => array(
							'type'            => 'none'
						)
					),
					'link_url'     => array(
						'type'          => 'link',
						'label'         => __('Link URL', 'fl-builder'),
						'preview'         => array(
							'type'            => 'none'
						)
					),
					'link_target'   => array(
						'type'          => 'select',
						'label'         => __('Link Target', 'fl-builder'),
						'default'       => '_self',
						'options'       => array(
							'_self'         => __('Same Window', 'fl-builder'),
							'_blank'        => __('New Window', 'fl-builder')
						),
						'preview'         => array(
							'type'            => 'none'
						)
					)
				)
			)
		)
	)
));