<?php

/**
 * Misc helper methods.
 *
 * @class FLBuilderUtils
 */

final class FLBuilderUtils {

    /**
	 * Get an instance of WP_Filesystem_Direct.
	 *
     * @method get_filesystem
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
     * @method filesystem_method
     */
    static public function filesystem_method()
    {
	    return 'direct';
    }

    /**
	 * Return a snippet without punctuation at the end.
	 *
     * @method snippetwop
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
}