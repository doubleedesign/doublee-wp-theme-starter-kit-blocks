<?php

class Doublee_Menus {

	public function __construct() {
		add_action('init', [$this, 'register_menus'], 20);
		add_filter('nav_menu_link_attributes', [$this, 'menu_link_classes'], 10, 4);
		add_filter('nav_menu_submenu_css_class', [$this, 'menu_submenu_classes'], 10, 2);
	}


	/**
	 * Register menus in the back-end
	 * @wp-hook
	 *
	 * @return void
	 */
	function register_menus(): void {
		register_nav_menus(array(
			'primary' => 'Primary menu',
			'footer'  => 'Footer menu'
		));
	}


	/**
	 * Add classes to menu <a> tags
	 *
	 * @param $atts
	 * @param $item
	 * @param $args
	 * @param $depth
	 *
	 * @return array
	 */
	function menu_link_classes($atts, $item, $args, $depth): array {

		// Header menu
		if ($args->theme_location == 'header' && $depth == 0 && in_array('menu-item-has-children', $item->classes)) {
			$atts['class'] = 'menu-dropdown-link';
		}

		return $atts;
	}

	/**
	 * Add classes to sub-menu <ul>
	 *
	 * @param $classes
	 * @param $args
	 *
	 * @return array
	 */
	function menu_submenu_classes($classes, $args): array {

		// Header menu
		if ($args->theme_location == 'header') {
			$classes[] = 'dropdown-menu';
		}

		return $classes;
	}
}
