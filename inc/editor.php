<?php
/**
 * Editor
 *
 * @package MyTheme
 */

namespace MyTheme;

/**
 * Setup
 */
function block_editor_setup() {

	// Set editor colors.
	add_theme_support(
		'editor-color-palette',
		array(
			array(
				'name'  => __( 'Primary', 'my-theme' ),
				'slug'  => 'primary',
				'color' => '#007bff', // Value of $primary.
			),
			array(
				'name'  => __( 'Secondary', 'my-theme' ),
				'slug'  => 'secondary',
				'color' => '#6c757d', // Value of $secondary.
			),
			array(
				'name'  => __( 'Success', 'my-theme' ),
				'slug'  => 'success',
				'color' => '#28a745', // Value of $success.
			),
			array(
				'name'  => __( 'Info', 'my-theme' ),
				'slug'  => 'info',
				'color' => '#17a2b8', // Value of $info.
			),
			array(
				'name'  => __( 'Warning', 'my-theme' ),
				'slug'  => 'warning',
				'color' => '#ffc107', // Value of $warning.
			),
			array(
				'name'  => __( 'Danger', 'my-theme' ),
				'slug'  => 'danger',
				'color' => '#dc3545', // Value of $danger.
			),
			array(
				'name'  => __( 'Light', 'my-theme' ),
				'slug'  => 'light',
				'color' => '#f8f9fa', // Value of $light.
			),
			array(
				'name'  => __( 'Dark', 'my-theme' ),
				'slug'  => 'dark',
				'color' => '#343a40', // Value of $dark.
			),
			array(
				'name'  => __( 'White', 'my-theme' ),
				'slug'  => 'white',
				'color' => '#ffffff', // Value of $white.
			),
		)
	);

	// Set editor font sizes.
	add_theme_support(
		'editor-font-sizes',
		array(
			// 'normal' slug is required to set default font size
			array(
				'name'      => __( 'Small', 'my-theme' ),
				'shortName' => __( 'SM', 'my-theme' ),
				'size'      => 16 * 0.875, // Value of $font-size-sm.
				'slug'      => 'small',
			),
			array(
				'name'      => __( 'Normal', 'my-theme' ),
				'shortName' => __( 'MD', 'my-theme' ),
				'size'      => 16, // Value of $font-size-base.
				'slug'      => 'normal',
			),
			array(
				'name'      => __( 'Large', 'my-theme' ),
				'shortName' => __( 'LG', 'my-theme' ),
				'size'      => 16 * 1.25, // Value of $font-size-lg.
				'slug'      => 'large',
			),
		)
	);

	// Add support for Block Styles.
	add_theme_support( 'wp-block-styles' );

	// Enable align 'wide' and 'full' block settings.
	add_theme_support( 'align-wide' );

	// Enable responsive embeds.
	add_theme_support( 'responsive-embeds' );
}

add_action( 'after_setup_theme', __NAMESPACE__ . '\block_editor_setup' );

/**
 * Enqueue block assets for both editor and front-end.
 */
function block_assets() {

	wp_dequeue_style( 'wp-block-library' );

	if ( ! is_admin() ) {
		$theme         = wp_get_theme();
		$theme_version = $theme->get( 'Version' );

		$css_version = filemtime( get_template_directory() . '/dist/styles/blocks.css' );
		wp_enqueue_style( 'my_theme-blocks', get_template_directory_uri() . '/dist/styles/blocks.css', array(), "{$theme_version}.{$css_version}" );
	}
}

add_action( 'enqueue_block_assets', __NAMESPACE__ . '\block_assets' );

/**
 * Enqueue block assets for the editing interface
 */
function block_editor_assets() {

	$theme         = wp_get_theme();
	$theme_version = $theme->get( 'Version' );

	$css_version = filemtime( get_template_directory() . '/dist/styles/editor.css' );
	wp_enqueue_style( 'my_theme-editor', get_template_directory_uri() . '/dist/styles/editor.css', array(), "{$theme_version}.{$css_version}" );
}

add_action( 'enqueue_block_editor_assets', __NAMESPACE__ . '\block_editor_assets' );
