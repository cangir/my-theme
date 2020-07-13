<?php
/**
 * Nav Menus
 *
 * @package My/Theme
 */

namespace My\Theme;

/**
 * Register nav menu locations.
 * @link https://developer.wordpress.org/reference/functions/register_nav_menus/
 */
add_action('after_setup_theme', function () {
    register_nav_menus([
        'top-left'     => esc_html__('Top Left', 'my-theme'),
        'top-right'    => esc_html__('Top Right', 'my-theme'),
        'main-left'    => esc_html__('Primary Left', 'my-theme'),
        'main-right'   => esc_html__('Primary Right', 'my-theme'),
        'footer-left'  => esc_html__('Footer Left', 'my-theme'),
        'footer-right' => esc_html__('Footer Right', 'my-theme'),
    ]);
});

/**
 * Set Walker
 *
 * @param array $args Array of wp_nav_menu() arguments.
 * @return array
 */
add_filter('wp_nav_menu_args', function ($args) {
    if (empty($args['walker']) && preg_match('/(^| )(nav|navbar-nav)( |$)/', $args['menu_class'])) {
        $args['walker'] = new NavWalker();
    }
    return $args;
}, PHP_INT_MAX);

/**
 * Nav Menu Link Attributes
 *
 * @param array    $atts      The HTML attributes applied to the menu item's <a> element.
 * @param WP_Post  $item      The current menu item.
 * @param stdClass $nav_menu  An object of wp_nav_menu() arguments.
 * @param int      $depth     Depth of menu item.
 *
 * @return array
 */
add_filter('nav_menu_link_attributes', function ($atts, $item, $nav_menu, $depth) {
    /**
     * Use CSS class toggle-modal to open modal on click.
     */
    if (in_array('toggle-modal', $item->classes)) {
        $atts['data-toggle'] = 'modal';
    }

    return $atts;
}, PHP_INT_MAX, 4);

/**
 * Nav Menu Item Title
 *
 * @param string   $title     The menu item's title.
 * @param WP_Post  $item      The current menu item.
 * @param stdClass $nav_menu  An object of wp_nav_menu() arguments.
 * @param int      $depth     Depth of menu item. Used for padding.
 */
add_filter('nav_menu_item_title', function ($title, $item, $nav_menu, $depth) {

    /**
     * Make title only available for screenreaders. Use CSS class -sr-only.
     */
    if (in_array('-sr-only', $item->classes)) {
        $title = sprintf('<span class="sr-only">%s</span>', $title);
    }

    return $title;
}, 5, 4);
