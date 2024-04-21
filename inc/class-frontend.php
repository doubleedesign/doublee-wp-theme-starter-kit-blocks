<?php

class Doublee_Frontend {

	public function __construct() {
		add_action('wp_enqueue_scripts', [$this, 'enqueue_frontend']);
	}


	/**
	 * Enqueue frontend scripts and styles
	 * @return void
	 */
	function enqueue_frontend(): void {
		wp_enqueue_style('doublee-base-css', get_template_directory_uri() . '/style.css', array(), THEME_FOUNDATION_VERSION);
		wp_enqueue_script('doublee-frontend-block-hacks', get_template_directory_uri() . '/common/js/frontend-block-hacks.js', array(), THEME_FOUNDATION_VERSION, true);
	}


	/**
	 * Get nav menu items by location
	 *
	 * @param $location string menu location name
	 * @param array $args args to pass to WordPress function wp_get_nav_menu_items
	 *
	 * @return false|array
	 */
	static function get_nav_menu_items_by_location(string $location, array $args = []): false|array {
		$locations = get_nav_menu_locations();
		$object = wp_get_nav_menu_object($locations[$location]);
		$items = wp_get_nav_menu_items($object->name, $args);
		$current = get_queried_object();
		$default_category_id = get_option('default_category');

		foreach ($items as $item) {
			if (isset($current->post_type) && $current->post_type == 'page') {
				$post_id = $current->ID;
				if ($post_id == $item->object_id) {
					$item->classes[] = 'current-menu-item';
				}
				if ($post_id == $item->post_parent) {
					$item->classes[] = 'current-menu-parent';
				}
			}
			else if (isset($current->taxonomy) && $current->taxonomy == 'category') {
				if ($item->object_id == PAGE_FOR_POSTS || $item->object_id == $default_category_id) {
					$item->classes[] = 'current-menu-item';
				}
			}
			else if (isset($current->post_type) && $current->post_type == 'post') {
				if ($item->object_id == PAGE_FOR_POSTS || $item->object_id == $default_category_id) {
					$item->classes[] = 'current-menu-parent';
				}
			}
			else if (isset($current->post_type) && $item->type == 'post_type_archive') {
				if($current->post_type == $item->object) {
					$item->classes[] = 'current-menu-parent';
				}
			}
			else if($item->type == 'post_type_archive' && $current->name == $item->object) {
				$item->classes[] = 'current-menu-item';
			}


			if ($item->url) {
				if (parse_url($item->url)['host'] !== parse_url(get_bloginfo('url'))['host']) {
					$item->classes[] = 'external';
				}
			}
		}

		return $items;
	}


	/**
	 * Excerpt customiser
	 * Strips headings and sets a custom length
	 * * Template usage:
	 * if(has_excerpt()) { the_excerpt(); }
	 * * You can also use the function to shorten the manual excerpt:
	 * starterkit_custom_excerpt(get_the_excerpt());
	 * * Or simply shorten the content:
	 * starterkit_custom_excerpt(get_the_content());
	 *
	 * @param $text - the string to strip headings and shorten, generally get_the_excerpt or get_the_content
	 * @param $word_count - how many words to include in the output
	 *
	 * @return string
	 */
	static function get_custom_excerpt($text, $word_count) {

		// Remove shortcode tags from the given content
		$text = strip_shortcodes($text);
		$text = apply_filters('the_content', $text);
		$text = str_replace(']]>', ']]&gt;', $text);

		// Regular expression that strips the header tags and their content
		$regex = '#(<h([1-6])[^>]*>)\s?(.*)?\s?(</h\2>)#';
		$text = preg_replace($regex, '', $text);

		// Set the word count
		$excerpt_length = apply_filters('excerpt_length', $word_count); // WP default word count is 55

		// Set the ending
		$excerpt_end = '...';                                           // The WP default is [...]
		$excerpt_more = apply_filters('excerpt_more', ' ' . $excerpt_end);

		$excerpt = wp_trim_words($text, $excerpt_length, $excerpt_more);

		return wpautop(apply_filters('wp_trim_excerpt', $excerpt));
	}

}
