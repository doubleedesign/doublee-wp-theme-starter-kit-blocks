<?php

/**
 * Customisations to block and block editor functionality/display.
 */
class Doublee_Block_Editor {

	public function __construct() {
		if (!function_exists('register_block_type')) {
			// Block editor is not available.
			return;
		}

		add_action('init', [$this, 'register_custom_blocks'], 10);
		add_action('init', [$this, 'register_page_template'], 20);
		add_action('init', [$this, 'register_shared_blocks'], 15);
		add_filter('allowed_block_types_all', [$this, 'allowed_blocks'], 10, 2);
		add_filter('block_categories_all', [$this, 'customise_block_categories']);
		add_action('init', [$this, 'allowed_block_patterns'], 10, 2);
		add_filter('should_load_remote_block_patterns', '__return_false');
		add_action('after_setup_theme', [$this, 'disable_block_template_editor']);
		add_filter('block_editor_settings_all', [$this, 'disable_block_code_editor'], 10, 2);
		add_action('enqueue_block_editor_assets', [$this, 'block_editor_scripts'], 100);
		add_action('admin_enqueue_scripts', [$this, 'admin_scripts']);
		add_filter('script_loader_tag', [$this, 'script_type_module'], 10, 3);
		add_action('enqueue_block_editor_assets', [$this, 'disable_editor_fullscreen_mode']);
		add_action('admin_enqueue_scripts', [$this, 'editor_css']);
	}


	/**
	 * Register custom blocks
	 * @return void
	 * @uses Advanced Custom Fields Pro
	 */
	function register_custom_blocks(): void {
		$block_folders = array_diff(scandir(dirname(__DIR__, 1) . '/blocks/doublee'), ['.', '..']);

		foreach ($block_folders as $block_name) {
			if (file_exists(dirname(__DIR__, 1) . '/blocks/doublee/' . $block_name . '/editor.js')) {
				wp_register_script($block_name . '-editor-js',
					get_template_directory_uri() . '/blocks/doublee/' . $block_name . '/editor.js',
					array('wp-dom', 'wp-blocks', 'wp-element', 'wp-editor', 'wp-block-editor'),
					THEME_FOUNDATION_VERSION
				);
			}

			register_block_type(dirname(__DIR__, 1) . '/blocks/doublee/' . $block_name);
		}
	}


	/**
	 * Set up default blocks for new pages
	 * @return void
	 */
	function register_page_template(): void {
		$template = array(
			// Locked items
			array(
				'doublee/page-header',
				array(
					'lock'            => array(
						'move'   => true,
						'remove' => true,
					),
					'templateLock'    => 'all',
					'backgroundColor' => 'primary'
				),
			),
			// Unlocked contents
			array(
				'core/group',
				array(
					'lock'         => array(
						'move'   => true,
						'remove' => false,
					),
					'templateLock' => false
				),
				array(//array('doublee/copy', array())
				)
			),
		);
		$post_type_object = get_post_type_object('page');
		$post_type_object->template = $template;
	}


	/**
	 * Register shared blocks based on Shared Content post type
	 * Note: In the absence of block.json (because they're so dynamic), these also have to be registered in blocks.js
	 * @return void
	 */
	function register_shared_blocks(): void {
		$query = new WP_Query(array(
			'post_type'   => array('shared_content'),
			'post_status' => array('publish'),
		));

		$items = array_map(function ($post) {
			return array(
				'id'      => $post->ID,
				'key'     => is_numeric($post->post_name) ? sanitize_title($post->post_title) : $post->post_name,
				'title'   => $post->post_title,
				'content' => $post->post_content,
			);
		}, $query->posts);

		foreach ($items as $item) {
			register_block_type('doublee-shared/' . $item['key'], array(
				'title'           => $item['title'],
				'description'     => 'This content is shared between multiple pages. To edit it, go to the Shared Content section of the admin menu.',
				'category'        => 'shared-content',
				'render_callback' => function ($attributes, $content) use ($item) {
					$args = array(
						'block' => array('innerBlocks' => parse_blocks($item['content'])),
						'args'  => $attributes,
					);
					include_once(get_template_directory() . '/blocks/shared.php');
				},
				'keywords'        => array($item['title'], $item['key'], 'shared'),
			));
		}
	}


	/**
	 * Limit available blocks for simplicity
	 * NOTE: This is not the only place a block may be explicitly allowed.
	 * Most notably, ACF-driven custom blocks and page/post type templates may use/allow them directly.
	 * Some core blocks also have child blocks that already only show up in the right context.
	 *
	 * @param $allowed_block_types
	 * @param $block_editor_context
	 *
	 * @return mixed
	 */
	function allowed_blocks($allowed_block_types, $block_editor_context): array {
		$all_block_types = WP_Block_Type_Registry::get_instance()->get_all_registered();
		// Custom block types added to my forked Gutenberg editor plugin or site-specific plugin. Don't know why filtering $all_block_types doesn't work here
		$in_plugin = array('custom/tiles', 'custom/tile', 'custom/sponsors');
		// Custom block types registered in this parent theme
		$custom = array_filter($all_block_types, fn($block_type) => str_starts_with($block_type->name, 'doublee/'));
		$shared = array_filter($all_block_types, fn($block_type) => str_starts_with($block_type->name, 'doublee-shared/'));

		return array_merge(
			$in_plugin,
			array_column($custom, 'name'),
			array_column($shared, 'name'),
			// add core or plugin blocks here if:
			// 1. They are to be allowed at the top level
			// 2. They Are allowed to be inserted as child blocks of a core block (note: set custom parents for core blocks in addCoreBlockParents() in blocks.js if not allowing them at the top level)
			// No need to include them here if they are only being used in one or more of the below contexts:
			// 1. As direct $allowed_blocks within custom ACF-driven blocks and/or
			// 2. In a page/post type template defined programmatically and locked there (so users can't delete something that can't be re-inserted)
			array('core/group',
				'core/columns',
				'core/column',
				'core/image',
				'core/media-text',
				'core/latest-posts',
				'core/cover',
				'core/heading',
				'core/paragraph',
				'core/list',
				'core/table',
				'core/buttons',
				'core/button'
			)
		);
	}


	/**
	 * Register custom block categories and customise some existing ones
	 *
	 * @param $categories
	 *
	 * @return array
	 */
	function customise_block_categories($categories): array {
		$categories[] = array(
			'slug'  => 'page-layout', // because the built-in Design category uses 'layout'
			'title' => 'Layout panels'
		);
		$categories[] = array(
			'slug'  => 'shared-content',
			'title' => 'Shared content'
		);

		return array_reverse($categories);
	}


	/**
	 * Disable some core Block Patterns for simplicity
	 * and register custom patterns
	 * Note: Also ensure loading of remote patterns is disabled using add_filter('should_load_remote_block_patterns', '__return_false');
	 *
	 * @return void
	 */
	function allowed_block_patterns(): void {
		unregister_block_pattern('core/social-links-shared-background-color');
		unregister_block_pattern('core/query-offset-posts');
		unregister_block_pattern('core/query-large-title-posts');
		unregister_block_pattern('core/query-grid-posts');
		unregister_block_pattern('core/query-standard-posts');
		unregister_block_pattern('core/query-medium-posts');
		unregister_block_pattern('core/query-small-posts');

		// TODO: Register custom block patterns
	}


	/**
	 * Disable block template editor option
	 * @return void
	 */
	function disable_block_template_editor(): void {
		remove_theme_support('block-templates');
	}


	/**
	 * Disable access to the block code editor
	 */
	function disable_block_code_editor($settings, $context) {
		$settings['codeEditingEnabled'] = false;

		return $settings;
	}


	/**
	 * Load the JS that modifies block stuff that can't be done in PHP or theme.json
	 * @return void
	 */
	function block_editor_scripts(): void {
		//wp_enqueue_script('evatt-block-editor-js', get_template_directory_uri() . '/js/dist/editor.bundle.js',
		wp_enqueue_script('doublee-block-editor-js', get_template_directory_uri() . '/blocks/blocks.js',
			array(
				'wp-dom',
				'wp-dom-ready',
				'wp-blocks',
				'wp-edit-post',
				'wp-element',
				'wp-plugins',
				'wp-edit-post',
				'wp-components',
				'wp-data',
				'wp-compose',
				'wp-i18n',
				'wp-hooks',
				'wp-block-editor',
				'wp-block-library',
			),
			THEME_FOUNDATION_VERSION,
			false
		);
	}


	/**
	 * Script to hackily remove menu items (e.g., the disabled code editor button) for simplicity
	 * @return void
	 */
	function admin_scripts(): void {
		wp_enqueue_script('doublee-admin-js', get_template_directory_uri() . '/common/js/admin-hacks.js');
	}


	/**
	 * Add type=module to admin JS script tag
	 *
	 * @param $tag
	 * @param $handle
	 * @param $src
	 *
	 * @return mixed|string
	 */
	function script_type_module($tag, $handle, $src): mixed {
		if (in_array($handle, ['doublee-admin-js', 'doublee-block-editor-js', 'page-header-editor-js'])) {
			$tag = '<script type="module" src="' . esc_url($src) . '" id="' . $handle . '" ></script>';
		}

		return $tag;
	}


	/**
	 * Disable fullscreen mode - keep dashboard menu visible
	 * @return void
	 */
	function disable_editor_fullscreen_mode(): void {
		$script = "window.onload = function() { const isFullscreenMode = wp.data.select( 'core/edit-post' ).isFeatureActive( 'fullscreenMode' ); if ( isFullscreenMode ) { wp.data.dispatch( 'core/edit-post' ).toggleFeature( 'fullscreenMode' ); } }";
		wp_add_inline_script('wp-blocks', $script);
	}


	/**
	 * Enqueue editor CSS
	 * @return void
	 */
	function editor_css(): void {
		wp_enqueue_style('doublee-editor-css', get_template_directory_uri() . '/assets/editor.css');
	}

}
