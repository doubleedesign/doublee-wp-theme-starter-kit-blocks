<?php
namespace Starterkit_Blocks;

class Starterkit_Admin {

    public function __construct() {
        add_filter('use_block_editor_for_post_type', [$this, 'selective_gutenberg'], 10, 2);
        add_action('acf/update_field_group', [$this, 'save_acf_fields_to_parent_theme'], 20, 1);
        add_filter('acf/settings/save_json', [$this, 'override_acf_json_save_location'], 300);
        add_filter('acf/settings/load_json', [$this, 'load_acf_fields_from_parent_theme']);
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
