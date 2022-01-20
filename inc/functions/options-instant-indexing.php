<?php

defined('ABSPATH') or exit('Please don&rsquo;t call the plugin directly. Thanks :)');

require_once SEOPRESS_PLUGIN_DIR_PATH . '/vendor/autoload.php';
use Google;
use Google_Service_Indexing;
use Google_Service_Indexing_UrlNotification;

///////////////////////////////////////////////////////////////////////////////////////////////////
//Generate dynamically the Instant Indexing API key
///////////////////////////////////////////////////////////////////////////////////////////////////
function seopress_instant_indexing_generate_api_key_fn() {
    $options            = get_option('seopress_instant_indexing_option_name');

    $api_key = wp_generate_uuid4();
    $api_key = preg_replace('[-]', '', $api_key);
    $options['seopress_instant_indexing_bing_api_key'] = base64_encode($api_key);

    update_option('seopress_instant_indexing_option_name', $options);
}

///////////////////////////////////////////////////////////////////////////////////////////////////
//Create the virtual Instant Indexing API key txt file
///////////////////////////////////////////////////////////////////////////////////////////////////
function seopress_instant_indexing_api_key_txt() {
    $options            = get_option('seopress_instant_indexing_option_name');
    $api_key            = isset($options['seopress_instant_indexing_bing_api_key']) ? base64_decode(esc_attr($options['seopress_instant_indexing_bing_api_key'])) : null;

    if ($api_key === null) {
        return;
    }

    global $wp;
    $current_url = home_url( $wp->request );

    if ( isset( $current_url ) && trailingslashit( get_home_url() ) . $api_key . '.txt' === $current_url ) {
        header( 'Content-Type: text/plain' );
        header( 'X-Robots-Tag: noindex' );
        status_header( 200 );
        esc_html_e($api_key);

        exit();
    }
}
add_action('template_redirect', 'seopress_instant_indexing_api_key_txt');

///////////////////////////////////////////////////////////////////////////////////////////////////
//Batch Instant Indexing
///////////////////////////////////////////////////////////////////////////////////////////////////
function seopress_instant_indexing_fn() {
    $options            = get_option('seopress_instant_indexing_option_name');
    $log                = get_option('seopress_instant_indexing_log_option_name');
    $actions            = isset($options['seopress_instant_indexing_google_action']) ? esc_attr($options['seopress_instant_indexing_google_action']) : 'URL_UPDATED';
    $urls               = isset($options['seopress_instant_indexing_manual_batch']) ? esc_attr($options['seopress_instant_indexing_manual_batch']) : null;
    $google_api_key     = isset($options['seopress_instant_indexing_google_api_key']) ? $options['seopress_instant_indexing_google_api_key'] : null;
    $bing_api_key       = isset($options['seopress_instant_indexing_bing_api_key']) ? base64_decode(esc_attr($options['seopress_instant_indexing_bing_api_key'])) : null;
    $bing_url = 'https://api.indexnow.org/indexnow/';
    $google_url = 'https://indexing.googleapis.com/v3/urlNotifications:publish';

    $json = json_decode($google_api_key, true);
    $json = $json['project_id'];

    //Check we have URLs to submit
    if ($urls === null) {
        return;
    }

    //Check we have setup at least one API
    if ($google_api_key === null || $bing_api_key === null) {
        return;
    }

    //Clean logs
    $log['urls'] = '';

    //Prepare the URLS
    $urls 	= preg_split('/\r\n|\r|\n/', $urls);

    $urls = array_slice($urls, 0, 100);

    if ($bing_api_key !== null) {

        $host = wp_parse_url(get_home_url(), PHP_URL_HOST);

        $body   = [
            'host' => $host,
            'key' => $bing_api_key,
            'keyLocation'  => trailingslashit( get_home_url() ) . $bing_api_key . '.txt',
            'urlList' => $urls
        ];

        //Build the POST request
        $args = [
            'body'    => json_encode($body),
            'timeout' => 30,
            'headers' => [
                'Content-Type'  => 'application/json'
            ],
        ];

        $args = apply_filters( 'seopress_instant_indexing_post_request_args', $args );

        //IndexNow (Bing)
        $response = wp_remote_post( $bing_url, $args );

        //Check the response is ok first
        if (is_wp_error($response)) {
            $message = $response->get_error_message();
            $log['bing']['status'] = $message;

            update_option('seopress_instant_indexing_log_option_name', $log);
        }

        $log['bing']['response'] = $response;
    } else {
        $log['bing']['response'] = __('Bing API key is missing', 'wp-seopress');
    }

    //Google
    if ($google_api_key !== null) {
        try {
            $client = new Google_Client();

            $client->setAuthConfig( json_decode($google_api_key, true) );
            $client->setScopes( Google_Service_Indexing::INDEXING );

            $client->setUseBatch( true );

            $service = new Google_Service_Indexing( $client );
            $batch = $service->createBatch();

            $postBody = new Google_Service_Indexing_UrlNotification();

            foreach($urls as $url) {
                $postBody->setUrl( $url );
                $postBody->setType( $actions );
                $batch->add( $service->urlNotifications->publish( $postBody ) );

            }
            $results = $batch->execute();
        }
        catch (\Exception $e) {
            $results = $e->getMessage();
        }

        $log['google']['response'] = $results;
    } else {
        $log['google']['response'] = __('Google API key is missing', 'wp-seopress');
    }

    //Log URLs submitted
    $log['log']['urls'] = $urls;
    $log['log']['date'] = current_time( 'F j, Y, g:i a' );


    update_option('seopress_instant_indexing_log_option_name', $log);
    exit();
}

///////////////////////////////////////////////////////////////////////////////////////////////////
//Ajax Batch Instant Indexing
///////////////////////////////////////////////////////////////////////////////////////////////////
function seopress_instant_indexing_post()
{
    check_ajax_referer('seopress_instant_indexing_post_nonce');
    if (current_user_can(seopress_capability('manage_options', 'instant-indexing')) && is_admin()) {
        seopress_instant_indexing_fn();
    }

    wp_send_json_success();
}
add_action('wp_ajax_seopress_instant_indexing_post', 'seopress_instant_indexing_post');

///////////////////////////////////////////////////////////////////////////////////////////////////
//Ajax Generate Instant Indexing API Key
///////////////////////////////////////////////////////////////////////////////////////////////////
function seopress_instant_indexing_generate_api_key()
{
    check_ajax_referer('seopress_instant_indexing_generate_api_key_nonce');
    if (current_user_can(seopress_capability('manage_options', 'instant-indexing')) && is_admin()) {
        seopress_instant_indexing_generate_api_key_fn();
    }

    wp_send_json_success();
}
add_action('wp_ajax_seopress_instant_indexing_generate_api_key', 'seopress_instant_indexing_generate_api_key');
