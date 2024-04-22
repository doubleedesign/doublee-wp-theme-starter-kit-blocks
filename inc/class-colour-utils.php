<?php

// With thanks to https://github.com/gdkraus/wcag2-color-contrast/
class Doublee_Colour_Utils {

	public function __construct() {
	}

	/**
	 * Get the theme colours
	 *
	 * @return array
	 */
	public static function get_theme_colours(): array {
		$theme = Doublee_Theme_CMS_Utils::get_theme();
		if (isset($theme['colours'])) {
			$colours = array();

			foreach ($theme['colours'] as $name => $value) {
				$colours[$name] = str_replace('#', '', $value);
			}

			return $colours;
		}

		return [];
	}


	/**
	 * Calculate the luminosity of a given RGB color
	 * http://www.w3.org/TR/WCAG20/#relativeluminancedef
	 * @param $color - the color in RRGGBB format
	 *
	 * @return float
	 */
	private static function calculate_luminosity($color): float {

		$r = hexdec(substr($color, 0, 2)) / 255; // red value
		$g = hexdec(substr($color, 2, 2)) / 255; // green value
		$b = hexdec(substr($color, 4, 2)) / 255; // blue value
		if ($r <= 0.03928) {
			$r = $r / 12.92;
		}
		else {
			$r = pow((($r + 0.055) / 1.055), 2.4);
		}

		if ($g <= 0.03928) {
			$g = $g / 12.92;
		}
		else {
			$g = pow((($g + 0.055) / 1.055), 2.4);
		}

		if ($b <= 0.03928) {
			$b = $b / 12.92;
		}
		else {
			$b = pow((($b + 0.055) / 1.055), 2.4);
		}

		return 0.2126 * $r + 0.7152 * $g + 0.0722 * $b;
	}


	/**
	 * Calculate the luminosity ratio of two given colours
	 * @param $color1 - first colour in RRGGBB format
	 * @param $color2 - second colour in RRGGBB format
	 *
	 * @return float
	 */
	private static function calculate_luminosity_ratio($color1, $color2): float {
		$l1 = self::calculate_luminosity($color1);
		$l2 = self::calculate_luminosity($color2);

		if ($l1 > $l2) {
			$ratio = (($l1 + 0.05) / ($l2 + 0.05));
		}
		else {
			$ratio = (($l2 + 0.05) / ($l1 + 0.05));
		}

		return $ratio;
	}


	/**
	 * Evaluate the contrast between two given colours according to AA and AAA WCAG 2 requirements
	 * http://www.w3.org/TR/WCAG20/#contrast-ratiodef
	 * @param $color1 - first colour in RRGGBB format
	 * @param $color2 - second colour in RRGGBB format
	 *
	 * @return array
	 */
	public static function evaluate_colour_contrast($color1, $color2): array {
		$ratio = self::calculate_luminosity_ratio($color1, $color2);

		return array(
			'AA'    => array(
				'normal' => ($ratio >= 4.5),
				'large'  => ($ratio >= 3),
			),
			'AAA'   => array(
				'normal' => ($ratio >= 7),
				'large'  => ($ratio >= 4.5),
			),
			'ratio' => $ratio
		);
	}


	/**
	 * Get light/dark colour to use for text based on background colour
	 * similar to the SCSS color-contrast() function
	 * @param $background_colour
	 * @param $dark_colour
	 * @param $light_colour
	 * @param $level
	 *
	 * @return string
	 */
	public static function get_text_colour_for_background($background_colour, $dark_colour = '000000', $light_colour = 'ffffff', $level = ['AA', 'normal']): string {
		$light_result = self::evaluate_colour_contrast($background_colour, $light_colour)[$level[0]][$level[1]];
		return $light_result ? $light_colour : $dark_colour;
	}
}
