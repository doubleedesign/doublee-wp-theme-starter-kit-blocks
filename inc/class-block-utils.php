<?php

class Doublee_Block_Utils {

	public function __construct() {
		add_action('doublee_block_layout_start', [$this, 'block_layout_start'], 10, 3);
		add_action('doublee_block_layout_end', [$this, 'block_layout_end'], 10, 2);
	}


	/**
	 * Outputs the opening tags for a top-level ACF-driven custom block
	 * Usage: do_action('doublee_block_layout_start', $block, $context, $parent)
	 * @param $block
	 * @param $context - editor or frontend
     * @param $parent - parent block name if applicable
	 *
	 * @return void
	 */
	function block_layout_start($block, $context, $parent): void {
		$name = self::get_short_name($block, $context);
		if (!empty($name)) {
			$section_classes = self::get_section_classes($block, $context);
			$row_classes = self::get_row_classes($block, $context, $parent);
			$column_classes = self::get_column_classes($block, $context);

            if ($name === 'call-to-action' && self::get_acf_field_for_block('partly_overlay_previous_block', $block, $context)) {
                $section_classes[] = 'is-with-previous';
            }

            $output = '<section class="' . implode(' ', $section_classes) . '">';
            $output .= '<div class="' . implode(' ', $row_classes) . '">';

            if ($parent === 'doublee/columns') {
                // This is just so I don't have to pass the parent to block_layout_end to adjust how many elements there are to close
                // CSS display:contents makes it functionally ignored, layout-wise
                $output .= '<div class="doublee-columns-inner-wrapper">';
            }
            else {
                $output .= '<div class="' . implode(' ', $column_classes) . '">';
            }
		}
		else {
			$output = '<section class="block">';
			$output .= '<div class="row">';
			$output .= '<div class="entry-content col-12">';
		}

		echo $output;
	}


	/**
	 * Outputs the closing tags for a top-level ACF-driven custom block
	 * Usage: do_action('doublee_block_layout_end');
	 * @return void
	 */
	function block_layout_end(): void {
		$output = '</div>';
		$output .= '</div>';
		$output .= '</section>';

		echo $output;
	}


	/**
	 * Custom block output, to be used both for initial block render
	 * and innerBlocks output within those blocks
	 * @param $blocks
	 * @param array $args - optional arguments to pass to the template part, e.g., background colour from the parent
	 *
	 * @return void
	 */
	static function output_custom_blocks($blocks, array $args = []): void {
		$count = 1;
		foreach ($blocks as $block) {
			// Render both custom and core blocks using custom template parts where available
			if (file_exists(get_stylesheet_directory() . '/components/blocks/' . $block['blockName'] . '/index.php')) {
				get_template_part('components/blocks/' . $block['blockName'] . '/index', '', array(
					'block' => $block,
					'args'  => array_merge($args, array('position' => $count))
				));
			}
			else if (file_exists(get_template_directory() . '/blocks/' . $block['blockName'] . '/index.php')) {
				get_template_part('blocks/' . $block['blockName'] . '/index', '', array(
					'block' => $block,
					'args'  => array_merge($args, array('position' => $count))
				));
			}
			// TODO: Make this generic
			else if (isset($block['blockName']) && file_exists(DOUBLEE_PLUGIN_PATH . '/blocks/' . str_replace('custom/', '', $block['blockName']) . '/index.php')) {
				$args = array(
					'block' => $block,
					'args'  => array_merge($args, array('position' => $count))
				);
				include(DOUBLEE_PLUGIN_PATH . '/blocks/' . str_replace('custom/', '', $block['blockName']) . '/index.php');
			}
			else {
				echo render_block($block);
			}

			$count++;
		}
	}


	/**
	 * Utility function to get the value of a given ACF field value for a block
	 * @param $field
	 * @param $block
	 * @param $context
	 *
	 * @return string | bool | null | array
	 */
	static function get_acf_field_for_block($field, $block, $context): bool|string|null|array {
		$value = null;
		if ($context === 'editor') {
			$data = $block;
		}
		else if ($context === 'frontend') {
			$data = $block['attrs'];
		}
		else {
			error_log('get_acf_field_for_block called with unsupported context ' . $context);
		}

		if (isset($data['data'][$field])) {
			$value = $data['data'][$field];
		}

		return $value;
	}


	/**
	 * Work out whether a block is fullwidth based on its style class name from the editor
	 * (to be used for the .row)
	 * @param $block
	 * @param $context
	 *
	 * @return string
	 */
	static function get_block_width($block, $context): string {
		if ($context === 'editor') {
			$data = $block;
		}
		else if ($context === 'frontend') {
			$data = $block['attrs'];
		}
		else {
			error_log('get_is_fullwidth called with unsupported context ' . $context);
		}

		if (isset($data['className'])) {
			if (str_contains($data['className'], 'is-style-fullwidth')) {
				return 'fullwidth';
			}
			if (str_contains($data['className'], 'is-style-contained')) {
				return 'contained';
			}
			if (str_contains($data['className'], 'is-style-narrow')) {
				return 'narrow';
			}
			if (str_contains($data['className'], 'is-style-wide')) {
				return 'wide';
			}
		}

		return 'contained';
	}


	/**
	 * Utility function to get the name of the block without the namespace prefix
	 * e.g. doublee/page-header -> page-header
	 * @param $block
	 * @param $context
	 *
	 * @return string
	 */
	static function get_short_name($block, $context): string {
		if ($context === 'editor') {
			return explode('/', $block['name'])[1];
		}
		else if ($context === 'frontend') {
			return explode('/', $block['blockName'])[1];
		}
		else {
			error_log('get_short_name called with unsupported context ' . $context);
		}

		return '';
	}


	/**
	 * Work out background classes based on combination of colour settings from the editor
	 * and ACF fields where applicable
	 * @param $block
	 * @param string $default
	 * @return array|string[]
	 */
	static function get_background_classes($block, string $default = 'transparent'): array {
		$bg_classes = [];

		if (isset($block['attrs'])) {
			if (isset($block['attrs']['backgroundColor'])) {
				$background = $block['attrs']['backgroundColor'];
				$bg_classes[] = 'has-' . $background . '-background-color';
			}
			else if (isset($block['attrs']['gradient'])) {
				$gradient = $block['attrs']['gradient'];
				$bg_classes[] = 'has-' . $gradient . '-gradient-background';
			}
		}

		if (empty($bg_classes)) {
			$bg_classes = ["has-$default-background-color"];
		}

		return $bg_classes;
	}


	/**
	 * Get a block's custom classes assigned in the editor in the same format as the background and width classes
	 * @param $block
	 * @param $context
	 * @return array
	 */
	static function get_custom_classes($block, $context): array {
		$custom_classes = [];
		if ($context === 'editor') {
			$data = $block;
		}
		else if ($context === 'frontend') {
			$data = $block['attrs'];
		}
		else {
			error_log('get_custom_classes called with unsupported context ' . $context);
		}

		if (isset($data['className'])) {
			$custom_classes = explode(' ', $data['className']);
		}

		return $custom_classes;
	}


	/**
	 * Get the classes for the outermost block element (e.g., .block__media-text)
	 * usually a <section> if it's at the top level
	 * @param $block
	 * @param $context
	 *
	 * @return array
	 */
	static function get_section_classes($block, $context): array {
		$name = self::get_short_name($block, $context);
		// TODO: Sponsors is from a client plugin, this needs to be made generic so it can find this for any client plugin's custom blocks
		$always_fullwidth_bg = ['page-header', 'in-this-section', 'latest-posts', 'sponsors'];
		$block_classes = array('block', "wp-block-$name");
		$block_classes = array_merge($block_classes, self::get_custom_classes($block, $context), self::get_background_classes($block));

		if (in_array($name, $always_fullwidth_bg) || self::get_acf_field_for_block('full_width_background', $block, $context)) {
			$block_classes[] = 'has-fullwidth-background';
		}
		else {
			$block_classes[] = 'has-contained-background';
		}

		return $block_classes;
	}


	/**
	 * Get the classes for the direct .row child element(s) of a block
	 * @param $block
	 * @param $context
     * @param $parent
	 *
	 * @return array
	 */
	static function get_row_classes($block, $context, $parent): array {
        $name = self::get_short_name($block, $context);
        if($name === 'social-icons' || $name === 'contact-details') {
            return [];
        }

		$block_row_classes = ['row'];
		$width = self::get_block_width($block, $context);
		if ($width == 'fullwidth') {
			$block_row_classes[] = 'row--fullwidth';
		}
		else if ($width == 'wide') {
			$block_row_classes[] = 'row--wide';
		}

        if($parent === 'doublee/columns') {
            $block_row_classes[] = 'row--inner';
        }

		$fullwidth_bg = self::get_acf_field_for_block('full_width_background', $block, $context);
		if (!$fullwidth_bg) {
			array_merge($block_row_classes, self::get_background_classes($block));
		}

		return $block_row_classes;
	}


	/**
	 * Get the classes for the content .col div(s) inside the direct .row child element(s) of a block
	 * @param $block
	 * @param $context
	 *
	 * @return array
	 */
	static function get_column_classes($block, $context): array {
		if ($context === 'editor') {
			$data = $block;
		}
		else if ($context === 'frontend') {
			$data = $block['attrs'];
		}
		else {
			error_log('get_width_classes called with unsupported context ' . $context);
		}

        $name = self::get_short_name($block, $context);
        if($name === 'social-icons' || $name === 'contact-details') {
            return ['entry-content'];
        }

		$column_classes = ['entry-content col-12'];

		if (isset($data['className']) && self::get_block_width($block, $context) == 'narrow') {
			$column_classes = array_merge($column_classes, ['col-lg-10', 'col-xl-9']);
		}

		if (self::get_inner_content_classes($block, $context)) {
			$column_classes = array_merge($column_classes, self::get_inner_content_classes($block, $context));
		}

		return $column_classes;
	}


	/**
	 *
	 * @param $block
	 * @param $context
	 *
	 * @return array
	 */
	static function get_inner_content_classes($block, $context): array {
		$inner_classes = [];
		$inner_background = self::get_acf_field_for_block('inner_content_background', $block, $context);
        // $inner_background = self::get_acf_field_for_block('inner_content_background_0_background_colour', $block, $context);
        if ($inner_background) {
			$inner_classes[] = 'has-' . $inner_background . '-background-color';
		}

		return $inner_classes;
	}


	/**
	 * Utility function to flatten a multidimensional indexed array
	 * @param $array
	 * @param array $flatArray
	 *
	 * @return array|mixed
	 */
	static function flattenArray($array, array &$flatArray = []): mixed {
		foreach ($array as $element) {
			if (is_array($element)) {
				// If the element is an array, recursively call the function
				self::flattenArray($element, $flatArray);
			}
			else {
				// If the element is not an array, add it to the result array
				$flatArray[] = $element;
			}
		}

		return $flatArray;
	}

}
