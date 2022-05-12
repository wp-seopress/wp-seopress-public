<?php

namespace SEOPress\Actions\Api\Options;

if ( ! defined('ABSPATH')) {
    exit;
}

use SEOPress\Core\Hooks\ExecuteHooks;

class AdvancedSettings implements ExecuteHooks {
    /**
	 * Current user ID
	 *
	 * @var int
	 */
    private $current_user = '';

    public function hooks() {
        $this->current_user = wp_get_current_user()->ID;
        add_action('rest_api_init', [$this, 'register']);
    }

    /**
     * @since 5.5
     *
     * @return boolean
     */
    public function permissionCheck(\WP_REST_Request $request) {
        $nonce = $request->get_header('x-wp-nonce');
        if ( ! wp_verify_nonce($nonce, 'wp_rest')) {
            return false;
        }

        if ( ! user_can( $this->current_user, 'manage_options' )) {
            return false;
        }

        return true;
    }

    /**
     * @since 5.5
     *
     * @return void
     */
    public function register() {
        register_rest_route('seopress/v1', '/options/advanced-settings', [
            'methods'             => 'GET',
            'callback'            => [$this, 'processGet'],
            'permission_callback' => [$this, 'permissionCheck'],
        ]);
    }

    /**
     * @since 5.5
     */
    public function processGet(\WP_REST_Request $request) {
        $options  = get_option('seopress_advanced_option_name');

        if (empty($options)) {
            return;
        }

        $data = [];

        foreach($options as $key => $value) {
            $data[$key] = $value;
        }

        return new \WP_REST_Response($data);
    }
}
