<?php

/**
 * Helper class for working with photos.
 *
 * @class FLBuilderPhoto
 */

final class FLBuilderPhoto {

    /**
     * @method sizes
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
     * @method get_attachment_data
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
     * @method get_thumb
     */
    static public function get_thumb($photo)
    {
        if(empty($photo)) {
            echo FL_BUILDER_URL . 'img/spacer.png';
        }
        else if(!empty($photo->sizes->thumbnail)) {
            echo $photo->sizes->thumbnail->url;
        }
        else {
            echo $photo->sizes->full->url;
        }
    }

    /**
     * @method get_src_options
     */
    static public function get_src_options($selected, $photo)
    {
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