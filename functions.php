<?php
require_once('inc/class-admin.php');
require_once('inc/class-assets.php');
require_once('inc/class-block-editor.php');
require_once('inc/class-block-utils.php');
require_once('inc/class-shared-content.php');
require_once('inc/class-colour-utils.php');
require_once('common/types.php');

function starterkit_setup(): void {
	new Starterkit_Blocks\Starterkit_Admin();
    new Starterkit_Blocks\Starterkit_Assets();
	new Starterkit_Block_Editor();
	new Starterkit_Block_Utils();
    new Starterkit_Shared_Content();
	new Starterkit_Colour_Utils();
}
add_action('after_setup_theme', 'starterkit_setup', 10);


/**
 * Define constants
 * @wp-hook
 * See https://stackoverflow.com/questions/1290318/php-constants-containing-arrays if using PHP < 7
 */
function doublee_register_constants(): void {
	define('THEME_STARTERKIT_VERSION', '2.0.0');
	define('PAGE_FOR_POSTS', get_option('page_for_posts'));

    if(class_exists('ACF')) {
        // Get it from options table instead of using ACF get_field()
        // due to loading order of ACF and theme
        $acf_gmaps_key = get_option('options_google_maps_api_key');
    }
    if(isset($acf_gmaps_key)) {
        define('GMAPS_KEY', $acf_gmaps_key);
    }
    else {
        define('GMAPS_KEY', '');
    }
}
add_action('after_setup_theme', 'doublee_register_constants');
