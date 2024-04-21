<?php

class Doublee_Site_Health {
    public function __construct() {
        add_filter('site_status_tests', [$this, 'required_plugins_site_health'], 20, 1);
    }

    function required_plugins_site_health($tests): array {
        $tests['direct']['doublee_required_plugins'] = array(
            'label' => __('Required plugins'),
            'test'  => [$this, 'site_health_plugin_check_required'],
        );

        $tests['direct']['doublee_recommended_plugins'] = array(
            'label' => __('Recommended plugins'),
            'test'  => [$this, 'site_health_plugin_check_recommended'],
        );

        $tests['direct']['doublee_installed_theme_plugins'] = array(
            'label' => __('Required and recommended plugins'),
            'test'  => [$this, 'site_health_plugin_check_correct'],
        );

        return $tests;
    }

    function site_health_plugin_check_required(): array {
        $status = Doublee_Admin::required_plugins_status();

        if ($status['errors']) {
            $required = implode(', ', $status['errors']);
            return array(
                'label'       => __('Required plugins'),
                'status'      => 'critical',
                'badge'       => array(
                    'label' => __('Performance'),
                    'color' => 'blue',
                ),
                'description' => sprintf(
                    '<p>%s</p>',
                    __("The following plugins to be installed and activated for full functionality: $required. Without them, some features may be missing or not work as expected.")
                ),
                'test'        => 'doublee_required_plugins'
            );
        }

        return array();
    }

    function site_health_plugin_check_recommended(): array {
        $status = Doublee_Admin::required_plugins_status();

        if ($status['warnings']) {
            $recommended = implode(', ', $status['warnings']);
            return array(
                'label'       => __('Recommended plugins'),
                'status'      => 'recommended',
                'badge'       => array(
                    'label' => __('Performance'),
                    'color' => 'blue',
                ),
                'description' => sprintf(
                    '<p>%s</p>',
                    __("The following plugins are strongly recommended to be installed and activated for full functionality: $recommended. Without them, some features may be missing or not work as expected.")
                ),
                'test'        => 'doublee_recommended_plugins'
            );
        }

        return array();
    }

    function site_health_plugin_check_correct(): array {
        $status = Doublee_Admin::required_plugins_status();

        if ($status['successes']) {
            $successes = implode(', ', $status['successes']);
            return array(
                'label'       => __('Required and recommended plugins'),
                'status'      => 'good',
                'badge'       => array(
                    'label' => __('Performance'),
                    'color' => 'blue',
                ),
                'description' => sprintf(
                    '<p>%s</p>',
                    __("The following required and recommended plugins for your theme are installed and activated: $successes.")
                ),
                'test'        => 'doublee_installed_theme_plugins'
            );
        }

        return array();
    }
}
