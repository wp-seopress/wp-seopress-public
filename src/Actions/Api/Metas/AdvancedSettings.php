<?php

if (! defined('ABSPATH')) {
    exit;
}

register_post_meta( 'post', '_seopress_robots_primary_cat',
    [
        'show_in_rest' => true,
        'single'       => true,
        'type'         => 'string',
    ]
);
