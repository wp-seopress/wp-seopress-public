<?php

namespace SEOPress\Actions\Api\Options;

if ( ! defined('ABSPATH')) {
    exit;
}

use SEOPress\Core\Hooks\ExecuteHooks;

class WoocommerceSettings implements ExecuteHooks {
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
     * @since 7.10
     *
     * @return boolean
     */
    public function permissionCheck(\WP_REST_Request $request) {
        $nonce = $request->get_header('x-wp-nonce');
        if ($nonce && !wp_verify_nonce($nonce, 'wp_rest')) {
            return false;
        }

        $current_user = $this->current_user ? $this->current_user : wp_get_current_user()->ID;
        if ( ! user_can( $current_user, 'manage_options' )) {
            return false;
        }

        return true;
    }

    /**
     * @since 7.10
     *
     * @return void
     */
    public function register() {
        register_rest_route('seopress/v1', '/options/woocommerce-settings', [
            'methods'             => 'GET',
            'callback'            => [$this, 'processGet'],
            'permission_callback' => [$this, 'permissionCheck'],
        ]);
    }

    /**
     * @since 7.10
     */
    public function processGet(\WP_REST_Request $request) {

        $data = [];

        if ( ! is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
            return new \WP_REST_Response($data);
        }

        if ( function_exists( 'wc_get_page_id' ) ) {
            $data['pages']['shop'] = wc_get_page_id( 'shop' );
            $data['pages']['cart'] = wc_get_page_id( 'cart' );
        }

        return new \WP_REST_Response($data);
    }
}
