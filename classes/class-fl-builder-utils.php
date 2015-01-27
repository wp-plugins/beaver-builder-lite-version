<?php

/**
 * Misc helper methods.
 *
 * @class FLBuilderUtils
 */

final class FLBuilderUtils {

    /**
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

    /**
     * @method array_to_object
     */
    static public function array_to_object($array)
    {
        $object = new StdClass();

        foreach($array as $key => $val) {
            $object->$key = $val;
        }

        return $object;
    }
}