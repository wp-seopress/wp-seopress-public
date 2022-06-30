<?php

if (! defined('ABSPATH')) {
    exit;
}

add_action( 'init', 'seopress_register_meta' );
/**
 * Registers meta in Rest API
 */
function seopress_register_meta() {
    register_post_meta( 'post', '_seopress_robots_primary_cat',
        [
            'show_in_rest' => true,
            'single'       => true,
            'type'         => 'string',
        ]
    );
}
