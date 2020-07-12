<?php
/**
 * Helpers
 *
 * @package My/Theme
 */

namespace My\Theme;

/**
 * @param string $id
 * @param mixed $concrete
 * @param bool $shared
 * @return mixed
 */
function app($key = null, $value = null, $shared = false)
{
    $container = Container::getInstance();

    if (is_null($key)) {
        return $container;
    }

    if (is_null($value)) {
        return $container->get($key);
    }

    return $container->set($key, $value, $shared);
}

/**
 * @param string $key
 * @param mixed $default
 * @return mixed
 */
function config($key, $default = null)
{
    return app('config')->get($key, $default);
}

/**
 * @param string $asset
 * @return string
 */
function asset_path($asset)
{
    return app('assets')->getURI($asset);
}

/**
 * @param array $attributes
 * @return string
 */
function html_atts($attributes)
{
    $str = '';

    foreach ($attributes as $name => $value) {
        $str .= sprintf(' %s="%s"', esc_attr($name), esc_attr($value));
    }

    return $str;
}

/**
 * @example
 * breakpoint_classes(['xs' => 1, 'sm' => 2], 'prefix');
 * returns
 * prefix-1 prefix-sm-2
 *
 * @param array $data
 * @param string $prefix
 * @return string
 */
function breakpoint_classes($data, $prefix = '')
{
    $breakpoints = ['xs', 'sm', 'md', 'lg', 'xl'];
    $classes = [];
    foreach ($breakpoints as $breakpoint) {
        $infix = $breakpoint === 'xs' ? '' : "$breakpoint-";
        if (isset($data[$breakpoint])) {
            $value = $data[$breakpoint];
            $classes[] = "{$prefix}-{$infix}{$value}";
        }
    }

    return implode(' ', $classes);
}

/**
 * Register a series of sidebars described as footer columns.
 *
 * @param int   $amount The amount of sidebars to register.
 * @param array $args   Extra arguments for register_sidebar function.
 */
function register_footer_columns($amount, $args = [])
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
