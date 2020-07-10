<?php

namespace My\Theme;

/**
 * Enqueue block assets for front-end and editing interface.
 */
add_action('enqueue_block_assets', function () {
    wp_enqueue_style('my-theme-blocks', asset_path('styles/block-style.css'), [], null);
});
