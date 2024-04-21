<?php
require_once('inc/class-admin.php');
require_once('inc/class-site-health.php');
require_once('inc/class-utils.php');
require_once('inc/class-tinymce.php');
require_once('inc/class-block-editor.php');
require_once('inc/class-shared-content.php');
require_once('inc/class-block-utils.php');
require_once('inc/class-colour-utils.php');
require_once('inc/class-frontend.php');
require_once('common/types.php');

function init_theme_foundation(): void {
	new Doublee_Admin();
	new Doublee_Site_Health();
	new Doublee_Theme_CMS_Utils();
	new Doublee_Block_Editor();
	new Doublee_Shared_Content();
	new Doublee_Block_Utils();
	new Doublee_Colour_Utils();
	new Doublee_TinyMCE();
	new Doublee_Frontend();
}
add_action('after_setup_theme', 'init_theme_foundation', 10);

/**
 * Define constants
 * @wp-hook
 * See https://stackoverflow.com/questions/1290318/php-constants-containing-arrays if using PHP < 7
 */
function doublee_register_constants(): void {
	define('THEME_FOUNDATION_VERSION', '0.0.1');
	define('PAGE_FOR_POSTS', get_option('page_for_posts'));
}
add_action('after_setup_theme', 'doublee_register_constants');
