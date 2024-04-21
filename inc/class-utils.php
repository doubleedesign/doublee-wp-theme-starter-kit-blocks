<?php

class Doublee_Theme_CMS_Utils {

	public function __construct() {
	}

	/**
	 * Utility function to get theme design tokens from theme.json as an associative array
	 * @wp-hook
	 * @return array
	 */
	static function get_theme(): array {
		$json = file_get_contents(get_stylesheet_directory() . '/theme-vars.json');
        if(!$json) {
            $json = file_get_contents(get_template_directory() . '/theme-vars.json');
        }

		return json_decode($json, true);
	}
}
