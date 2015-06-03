<?php

/**
 * Helper class for working with photos.
 *
 * @since 1.0
 */
final class FLBuilderPhoto {

	/**
	 * Returns an array of data for sizes that are 
	 * defined for WordPress images.
	 *
	 * @since 1.0
	 * @return array
	 */
	static public function sizes()
	{
		global $_wp_additional_image_sizes;

		$sizes = array();

		foreach(get_intermediate_image_sizes() as $size) {

			$sizes[$size] = array(0, 0);

			if(in_array($size, array('thumbnail', 'medium', 'large'))) {
				$sizes[$size][0] = get_option($size . '_size_w');
				$sizes[$size][1] = get_option($size . '_size_h');
			}
			else if(isset($_wp_additional_image_sizes) && isset($_wp_additional_image_sizes[$size])) {
				$sizes[$size] = array(
					$_wp_additional_image_sizes[$size]['width'],
					$_wp_additional_image_sizes[$size]['height']
				);
			}
		}

		return $sizes;
	}

	/**
	 * Returns an object with data for an attachment using
	 * wp_prepare_attachment_for_js based on the provided id.
	 *
	 * @since 1.0
	 * @param string $id The attachment id.
	 * @return object
	 */
	static public function get_attachment_data($id)
	{
		$data = wp_prepare_attachment_for_js($id);

		if(gettype($data) == 'array') {
			return json_decode(json_encode($data));
		}

		return $data;
	}

	/**
	 * Renders the thumb URL for a photo object.
	 *
	 * @since 1.0
	 * @param object $photo An object with photo data.
	 * @return void
	 */
	static public function get_thumb($photo)
	{
		if ( empty( $photo ) ) {
			echo FL_BUILDER_URL . 'img/spacer.png';
		}
		else if ( ! isset( $photo->sizes ) ) {
			echo $photo->url;
		}
		else if ( ! empty( $photo->sizes->thumbnail ) ) {
			echo $photo->sizes->thumbnail->url;
		}
		else {
			echo $photo->sizes->full->url;
		}
	}

	/**
	 * Renders the options for a photo select field.
	 *
	 * @since 1.0
	 * @param string $selected The selected URL.
	 * @param object $photo An object with photo data.
	 * @return void
	 */
	static public function get_src_options($selected, $photo)
	{
		if ( ! isset( $photo->sizes ) ) {
			echo '<option value="' . $photo->url . '" selected="selected">' . _x( 'Full Size', 'Image size.', 'fl-builder' ) . '</option>';   
		}
		else {
			
			$titles = array(
				'full'      => _x( 'Full Size', 'Image size.', 'fl-builder' ),
				'large'     => _x( 'Large', 'Image size.', 'fl-builder' ),
				'medium'    => _x( 'Medium', 'Image size.', 'fl-builder' ),
				'thumbnail' => _x( 'Thumbnail', 'Image size.', 'fl-builder' )
			);
	
			foreach($photo->sizes as $key => $val) {
	
				if(!isset($titles[$key])) {
					$titles[$key] = ucwords(str_replace(array('_', '-'), ' ', $key));
				}
	
				echo '<option value="' . $val->url . '" ' . selected($selected, $val->url) . '>' . $titles[$key]  . ' - ' . $val->width . ' x ' . $val->height . '</option>';
			}
		}
	}
}