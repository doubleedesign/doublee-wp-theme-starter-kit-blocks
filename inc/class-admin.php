<?php

class Doublee_Admin {

    public function __construct() {
        add_action('admin_notices', [$this, 'required_plugins_notification'], 10);
        add_action('admin_init', [$this, 'disable_customiser']);
        add_filter('use_block_editor_for_post_type', [$this, 'selective_gutenberg'], 10, 2);
        add_action('admin_enqueue_scripts', [$this, 'admin_css']);
        add_action('login_enqueue_scripts', [$this, 'login_logo']);
        add_action('admin_menu', [$this, 'remove_metaboxes']);
        add_action('add_meta_boxes', [$this, 'remove_metaboxes'], 99);
        add_action('after_setup_theme', [$this, 'register_theme_support'], 11);
        add_action('acf/update_field_group', [$this, 'save_acf_fields_to_parent_theme'], 20, 1);
        add_filter('acf/settings/save_json', [$this, 'override_acf_json_save_location'], 300);
        add_filter('acf/settings/load_json', [$this, 'load_acf_fields_from_parent_theme']);
    }


    static function required_plugins_status(): array {
        $successes = array();
        $errors = array();
        $warnings = array();
        if (is_plugin_active('advanced-custom-fields-pro/acf.php')) {
            $successes[] = 'Advanced Custom Fields Pro';
        }
        else {
            $errors[] = 'Advanced Custom Fields Pro';
        }

        if (is_plugin_active('tinymce-advanced/tinymce-advanced.php')) {
            $successes[] = 'Advanced Editor Tools';
        }
        else {
            $warnings[] = 'Advanced Editor Tools';
        }

        if (is_plugin_active('doublee-breadcrumbs/breadcrumbs.php')) {
            $successes[] = 'Double-E Breadcrumbs';
        }
        else {
            $warnings[] = 'Double-E Breadcrumbs';
        }

        return array(
            'successes' => $successes,
            'errors'    => $errors,
            'warnings'  => $warnings
        );
    }


    /**
     * Add admin notices for required and strongly recommended plugins
     * @return void
     */
    function required_plugins_notification(): void {
        $status = self::required_plugins_status();

        if (count($status['errors']) > 0) {
            echo '<div class="notice notice-error">';
            echo '<p>The following plugins are required to be installed and activated for full functionality of your Double-E Design theme. Without them, some features may be missing or not work as expected.</p>';
            echo '<ul>';
            foreach ($status['errors'] as $error) {
                echo '<li>' . $error . '</li>';
            }
            echo '</ul>';
            echo '</div>';
        }

        if (count($status['warnings']) > 0) {
            echo '<div class="notice notice-warning">';
            echo '<p>The following plugins are recommended for full functionality of your Double-E Design theme.</p>';
            echo '<ul>';
            foreach ($status['warnings'] as $warning) {
                echo '<li>' . $warning . '</li>';
            }
            echo '</ul>';
            echo '</div>';
        }
    }


    /**
     * Disable the Customiser in favour of having most things together in an ACF options page
     * and because it will probably be deprecated as the block editor takes over
     * Largely lifted from the Customizer Disabler plugin, except I haven't bothered to remove capabilities
     * https://wordpress.org/plugins/customizer-disabler/
     *
     * @return void
     */
    function disable_customiser(): void {
        remove_action('plugins_loaded', '_wp_customize_include');
        remove_action('admin_enqueue_scripts', '_wp_customize_loader_settings', 11);
        add_action('load-customize.php', [$this, 'override_load_customizer_action']);
        add_action('admin_init', [$this, 'remove_customiser_from_menu'], 99);
    }

    function override_load_customizer_action(): void {
        wp_die(__('The Customiser is currently disabled.', 'customizer-disabler'));
    }

    function remove_customiser_from_menu(): void {
        $customize_url = add_query_arg('return', urlencode(remove_query_arg(wp_removable_query_args(), wp_unslash($_SERVER['REQUEST_URI']))), 'customize.php');
        remove_submenu_page('themes.php', $customize_url);
    }


    /**
     * Only use the block editor for pages
     *
     * @param $current_status
     * @param $post_type
     *
     * @return bool
     */
    function selective_gutenberg($current_status, $post_type): bool {
        if ($post_type === 'page' || $post_type === 'shared_content') {
            return true;
        }

        return false;
    }


    /**
     * Enqueue admin CSS
     *
     * @return void
     */
    function admin_css(): void {
        wp_enqueue_style('doublee-admin-css', get_template_directory_uri() . '/assets/admin.css');
    }


    /**
     * Customise login screen logo
     */
    function login_logo(): void {
        $custom_logo_id = get_option('options_logo');
        if ($custom_logo_id) {
            $logo = wp_get_attachment_image_src($custom_logo_id, 'full');
            ?>
            <style>
				body.login.wp-core-ui {
				}

				#login h1 a {
					width: 75%;
					min-height: 80px;
					background-image: url('<?php echo $logo[0]; ?>') !important;
					padding-bottom: 0 !important;
					background-size: contain !important;
				}

				#login #nav a, #login #backtoblog a {
				}
            </style>
        <?php }
    }


    /**
     * Remove unwanted metaboxes
     */
    function remove_metaboxes(): void {
        remove_meta_box('nf_admin_metaboxes_appendaform', array('page', 'post'), 'side');
    }


    /**
     * Register theme support for the relevant backend features
     * Note: I'm leaning towards using an ACF options page for most things
     * rather than the Customizer (which does support adding a logo for example),
     * simply to keep things in one place
     */
    function register_theme_support(): void {
        add_theme_support('title-tag');
        add_theme_support('post-thumbnails', array('post', 'page', 'event'));
        add_post_type_support('page', 'excerpt');
    }


    /**
     * Shared utility function to conditionally change the ACF JSON save location
     * - to be used for field groups introduced by this parent theme (e.g., for blocks)
     * - when called, must be wrapped in a relevant conditional to identify the group to save to the plugin
     * @return string
     */
    function override_acf_json_save_location(): string {
        // remove this filter so it will not affect other groups
        remove_filter('acf/settings/save_json', 'override_acf_json_save_location', 300);

        return get_template_directory() . '/acf-json';
    }

    /**
     * Override the save location for ACF JSON files for field groups that are stored in this theme
     *
     * @param $group
     *
     * @return void
     */
    function save_acf_fields_to_parent_theme($group): void {
        // Assume all groups' filenames match their field group key
        $groups = array_diff(scandir(get_template_directory() . '/acf-json'), ['..', '.']);

        if (in_array($group['key'] . '.json', array_values($groups))) {
            self::override_acf_json_save_location();
        }
    }


    /**
     * Enable loading JSON files of ACF fields from the parent theme
     *
     * @param $paths
     *
     * @return array
     */
    function load_acf_fields_from_parent_theme($paths): array {
        $paths[] = get_template_directory() . '/acf-json';

        return $paths;
    }

}
