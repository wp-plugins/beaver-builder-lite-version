<?php
	
/**
 * Misc functions for compatibility with other plugins.
 */

/**
 * Support for Option Tree. 
 *
 * Older versions of Option Tree don't declare the ot_get_media_post_ID
 * function on the frontend which is needed for the media uploader and 
 * throws an error if it doesn't exist.
 */
function fl_builder_option_tree_support() {
	
	if ( !function_exists( 'ot_get_media_post_ID' ) ) {
		
		function ot_get_media_post_ID() {
	
			// Option ID
			$option_id = 'ot_media_post_ID';
			
			// Get the media post ID
			$post_ID = get_option( $option_id, false );
			
			// Add $post_ID to the DB
			if ( $post_ID === false ) {
				
				global $wpdb;
				
				// Get the media post ID
				$post_ID = $wpdb->get_var( "SELECT ID FROM $wpdb->posts WHERE `post_title` = 'Media' AND `post_type` = 'option-tree' AND `post_status` = 'private'" );
				
				// Add to the DB
				add_option( $option_id, $post_ID );
			}
			
			return $post_ID;
		}
	}
}
add_action('after_setup_theme', 'fl_builder_option_tree_support');
