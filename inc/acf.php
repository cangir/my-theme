<?php
/**
 * Advanced Custom Fields
 *
 * Dependency: Advanced Custom Fields
 *
 * @link https://www.advancedcustomfields.com
 *
 * @package My/Theme
 */

namespace My\Theme;

/**
 * ACF Init
 *
 * @uses acf_update_setting()
 * @uses acf_add_options_page()
 */
add_action('acf/init', function () {

    // Update settings
    if (function_exists('acf_update_setting')) {
        // acf_update_setting('google_api_key', 'xxx');
    }

    // Add options page
    if (function_exists('acf_add_options_page')) {
        acf_add_options_page([
            'page_title'  => __('Site Options', 'my-theme'),
            'menu_title'  => __('Site Options', 'my-theme'),
            'menu_slug'   => 'theme-options',
            'parent_slug' => null,
        ]);
    }
});

/**
 * Alter field settings
 *
 * @param array $field
 * @return array
 */
add_filter('acf/load_field', function ($field) {

    // Populate select field with editor color names. Usage: set field CSS class to my-theme-editor-colors.
    if ($field['type'] == 'select' && preg_match('/(^| )my-theme-editor-colors( |$)/', $field['wrapper']['class'])) {
        if (current_theme_supports('editor-color-palette')) {
            $field['choices'] = wp_list_pluck(get_theme_support('editor-color-palette')[0], 'name', 'slug');
        }
    }

    // Populate select field with editor font size names. Usage: set field CSS class to my-theme-editor-font-sizes.
    if ($field['type'] == 'select' && preg_match('/(^| )my-theme-editor-font-sizes( |$)/', $field['wrapper']['class'])) {
        if (current_theme_supports('editor-font-sizes')) {
            $field['choices'] = wp_list_pluck(get_theme_support('editor-font-sizes')[0], 'name', 'slug');
        }
    }

    // Populate select field with icon names. Usage: set field CSS class to my-theme-icons.
    if ($field['type'] == 'select' && preg_match('/(^| )my-theme-icons( |$)/', $field['wrapper']['class'])) {
        $choices = [];
        $icons = app('icons')->getIcons();
        foreach ($icons as $key => $svg) {
            $choices[$key] = ucwords(str_replace(['-', '_'], ' ', $key));
        }
        $field['choices'] = $choices;
    }

    return $field;
});