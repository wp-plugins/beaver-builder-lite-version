<?php

/**
 * Misc helper methods.
 *
 * @since 1.0
 */

final class FLBuilderUtils {

	/**
	 * Get an instance of WP_Filesystem_Direct.
	 *
	 * @since 1.4.6
	 * @return object A WP_Filesystem_Direct instance.
	 */
	static public function get_filesystem()
	{
		global $wp_filesystem;
		
		require_once ABSPATH .'/wp-admin/includes/file.php';
		
		add_filter('filesystem_method', 'FLBuilderUtils::filesystem_method');
				
		WP_Filesystem();
		
		remove_filter('filesystem_method', 'FLBuilderUtils::filesystem_method');
		
		return $wp_filesystem;
	}

	/**
	 * Sets the filesystem method to direct.
	 *
	 * @since 1.4.6
	 * @return string
	 */
	static public function filesystem_method()
	{
		return 'direct';
	}

	/**
	 * Return a snippet without punctuation at the end.
	 *
	 * @since 1.2.3
	 * @param string $text The text to truncate.
	 * @param int $length The number of characters to return.
	 * @param string $tail The trailing characters to append.
	 * @return string
	 */
	static public function snippetwop($text, $length = 64, $tail = "...")
	{
		$text = trim($text);
		$txtl = strlen($text);

		if($txtl > $length) {
			for($i=1;$text[$length-$i]!=" ";$i++) {
				if($i == $length) {
					return substr($text,0,$length) . $tail;
				}
			}
			for(;$text[$length-$i]=="," || $text[$length-$i]=="." || $text[$length-$i]==" ";$i++) {;}
			$text = substr($text,0,$length-$i+1) . $tail;
		}
		
		return $text;
	}

	/**
	 * JSON decode multidimensional array values or object properties.
	 *
	 * @since 1.5.6
	 * @param mixed $data The data to decode.
	 * @return mixed The decoded data.
	 */
	static public function json_decode_deep( $data )
	{
		// First check if we have a string and try to decode that. 
		if ( is_string( $data ) ) {
			$data = json_decode( $data );
		}
		
		// Decode object properies or array values.
		if ( is_object( $data ) || is_array( $data ) ) {

			foreach ( $data as $key => $val ) {

				$new_val = null;

				if ( is_string( $val ) ) {

					$decoded = json_decode( $val );

					if ( is_object( $decoded ) || is_array( $decoded ) ) {
						$new_val = $decoded;
					}
				}
				else if ( is_object( $val ) || is_array( $val ) ) {
					$new_val = self::json_decode_deep( $val );
				}

				if ( $new_val ) {

					if ( is_object( $data ) ) {
						$data->{$key} = $new_val;
					}
					else if ( is_array( $data ) ) {
						$data[ $key ] = $new_val;
					}
				}
			}
		}

		return $data;
	}
}