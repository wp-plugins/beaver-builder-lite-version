<?php

/**
 * Helper class for working with color values.
 *
 * @since 1.0
 */
final class FLBuilderColor {

	/**
	 * Converts a hex string into an array of RGB values.
	 *
	 * @since 1.0
	 * @param string $hex A hex color value without the # sign.
	 * @return array An array of RGB values.
	 */
	static public function hex_to_rgb($hex)
	{
		return array(
			'r' => hexdec(substr($hex,0,2)),
			'g' => hexdec(substr($hex,2,2)),
			'b' => hexdec(substr($hex,4,2))
		);
	}

	/**
	 * Adjusts the brightness of a hex color value based on 
	 * the number of steps provided.
	 *
	 * @since 1.0
	 * @param string $hex A hex color value without the # sign.
	 * @param int $steps The number of steps to adjust the color.
	 * @param string $type The type of adjustment to make. Either lighten, darken or reverse.
	 * @return string The adjusted hex string.
	 */
	static public function adjust_brightness($hex, $steps, $type)
	{
		// Get rgb vars.
		extract(self::hex_to_rgb($hex));
		
		// Should we darken the color?
		if($type == 'reverse' && $r + $g + $b > 382){
			$steps = -$steps;
		}
		else if($type == 'darken') {
			$steps = -$steps;
		}
		
		// Build the new color.
		$steps = max(-255, min(255, $steps));
		
		$r = max(0,min(255,$r + $steps));
		$g = max(0,min(255,$g + $steps));  
		$b = max(0,min(255,$b + $steps));
		
		$r_hex = str_pad(dechex($r), 2, '0', STR_PAD_LEFT);
		$g_hex = str_pad(dechex($g), 2, '0', STR_PAD_LEFT);
		$b_hex = str_pad(dechex($b), 2, '0', STR_PAD_LEFT);
		
		return $r_hex . $g_hex . $b_hex;
	}
}