<?php

namespace SEOPress\Services;

if ( ! defined('ABSPATH')) {
    exit;
}

class WordPressData
{
    public function getPostTypes( $return_all = false, $args = array() ) {
        global $wp_post_types;

        $default_args = [
            'show_ui' => true,
            'public'  => true,
        ];

        $args = wp_parse_args( $args, $default_args );

        if ( '' === $args['public'] ) {
            unset( $args['public'] );
        }

        $post_types = get_post_types($args, 'objects', 'and');

        if ( ! $return_all ) {
            unset(
                $post_types['attachment'],
                $post_types['seopress_rankings'],
                $post_types['seopress_backlinks'],
                $post_types['seopress_404'],
                $post_types['elementor_library'],
                $post_types['customer_discount'],
                $post_types['cuar_private_file'],
                $post_types['cuar_private_page'],
                $post_types['ct_template'],
                $post_types['bricks_template']
            );
        }

        $post_types = apply_filters( 'seopress_post_types', $post_types, $return_all, $args );

        return $post_types;
    }

    public function getTaxonomies($with_terms = false, $return_all = false) {
        $args = [
            'show_ui' => true,
            'public'  => true,
        ];
        $args = apply_filters('seopress_get_taxonomies_args', $args);

        $output     = 'objects'; // or objects
        $operator   = 'and'; // 'and' or 'or'
        $taxonomies = get_taxonomies($args, $output, $operator);

        if ( ! $return_all ) {
            unset(
                $taxonomies['seopress_bl_competitors'],
                $taxonomies['template_tag'],
                $taxonomies['template_bundle']
            );
        }

        $taxonomies = apply_filters( 'seopress_get_taxonomies_list', $taxonomies, $return_all );

        if ( ! $with_terms) {
            return $taxonomies;
        }

        foreach ($taxonomies as $_tax_slug => &$_tax) {
            $_tax->terms = get_terms(['taxonomy' => $_tax_slug]);
        }

        return $taxonomies;
    }
}
