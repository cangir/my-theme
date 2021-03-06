<?php
/**
 * Widgets
 *
 * @package My/Theme
 */

namespace My\Theme;

class Widgets
{
    /**
     * Initialize
     */
    public static function init()
    {
        add_action('after_setup_theme', [__CLASS__, 'setup']);
        add_action('widgets_init', [__CLASS__, 'registerSidebars']);
    }

    /**
     * Setup
     */
    public static function setup()
    {
      /**
       * Enable selective refresh for widgets in customizer
       * @link https://developer.wordpress.org/themes/advanced-topics/customizer-api/#theme-support-in-sidebars
       */
        add_theme_support('customize-selective-refresh-widgets');
    }

    /**
     * Register widget areas.
     */
    public static function registerSidebars()
    {
        $args = [
          'before_widget' => '<section id="%1$s" class="widget %2$s">',
          'after_widget'  => '</section>',
          'before_title'  => '<h2 class="widget-title">',
          'after_title'   => '</h2>',
        ];

        register_sidebar(
            [
              'id'          => 'header',
              'name'        => esc_html__('Header', 'my-theme'),
              'description' => esc_html__('Header section.', 'my-theme'),
            ] + $args
        );

        register_sidebar(
            [
              'id'          => 'sidebar-left',
              'name'        => esc_html__('Left Sidebar', 'my-theme'),
              'description' => esc_html__('Section on the left side.', 'my-theme'),
            ] + $args
        );

        register_sidebar(
            [
              'id'          => 'sidebar-right',
              'name'        => esc_html__('Right Sidebar', 'my-theme'),
              'description' => esc_html__('Section on the right side.', 'my-theme'),
            ] + $args
        );

        register_sidebar(
            [
              'id'          => 'footer',
              'name'        => esc_html__('Footer', 'my-theme'),
              'description' => esc_html__('Footer section.', 'my-theme'),
            ] + $args
        );

        self::registerFooterColumns(3, $args);
    }

    /**
     * Register a series of sidebars described as footer columns.
     *
     * @param int   $amount The amount of sidebars to register.
     * @param array $args   Extra arguments for register_sidebar function.
     */
    public static function registerFooterColumns($amount, $args = [])
    {
        $ordinals = [
          1 => __('First', 'my-theme'),
          2 => __('Second', 'my-theme'),
          3 => __('Thirth', 'my-theme'),
          4 => __('Fourth', 'my-theme'),
          5 => __('Fifth', 'my-theme'),
          6 => __('Sixth', 'my-theme'),
          7 => __('Seventh', 'my-theme'),
          8 => __('Eighth', 'my-theme'),
          9 => __('Ninth', 'my-theme'),
        ];

        for ($n=1; $n <= $amount; $n++) {
            register_sidebar(
                [
                  'id'          => 'footer-column-' . $n,
                  // translators: %s: column number
                  'name'        => sprintf(esc_html__('Footer Column %s', 'my-theme'), $n),
                  // translators: %s: column ordinal number
                  'description' => sprintf(esc_html__('%s column in footer section.', 'my-theme'), $ordinals[$n]),
                ] + $args
            );
        }
    }
}
