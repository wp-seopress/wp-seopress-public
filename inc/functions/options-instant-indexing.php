<?php

defined('ABSPATH') or exit('Please don&rsquo;t call the plugin directly. Thanks :)');

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
    $engines            = isset($options['engines']) ? $options['engines'] : null;
    $actions            = isset($options['seopress_instant_indexing_google_action']) ? esc_attr($options['seopress_instant_indexing_google_action']) : 'URL_UPDATED';
    $urls               = isset($options['seopress_instant_indexing_manual_batch']) ? esc_attr($options['seopress_instant_indexing_manual_batch']) : '';
    $google_api_key     = isset($options['seopress_instant_indexing_google_api_key']) ? $options['seopress_instant_indexing_google_api_key'] : '';
    $bing_api_key       = isset($options['seopress_instant_indexing_bing_api_key']) ? base64_decode(esc_attr($options['seopress_instant_indexing_bing_api_key'])) : '';
    $bing_url           = 'https://api.indexnow.org/indexnow/';
    $google_url         = 'https://indexing.googleapis.com/v3/urlNotifications:publish';

    //Clean logs
    delete_option('seopress_instant_indexing_log_option_name');

    //Check we have URLs to submit
    if ($urls === '') {
        $log['error'] = __('No URLs to submit','wp-seopress');
        update_option('seopress_instant_indexing_log_option_name', $log);
        return;
    }

    //Check we have at least one search engine selected
    if (empty($engines)) {
        $log['error'] = __('No search engines selected','wp-seopress');
        update_option('seopress_instant_indexing_log_option_name', $log);
        return;
    }

    //Check we have setup at least one API key
    if ($google_api_key === '' && $bing_api_key === '') {
        $log['error'] = __('No API key defined from the settings tab','wp-seopress');
        update_option('seopress_instant_indexing_log_option_name', $log);
        return;
    }

    $json = json_decode($google_api_key, true);
    $json = $json['project_id'];


    //Prepare the URLS
    $urls 	= preg_split('/\r\n|\r|\n/', $urls);

    $urls = array_slice($urls, 0, 100);

    //Bing API
    if ($bing_api_key !== '' && $engines['bing'] === '1') {
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
        }

        $log['bing']['response'] = $response;
    } elseif ($engines['bing'] === '1') {
        $log['bing']['response']['error'] = [
            'code' => 401,
            'message' => __('Bing API key is missing', 'wp-seopress')
        ];
    }

    //Google API
    if ($google_api_key !== '' && $engines['google'] === '1') {
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
    } elseif ($engines['google'] === '1') {
        $log['google']['response']['error'] = [
            'code' => 401,
            'message' => __('Google API key is missing', 'wp-seopress')
        ];
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
    require_once WP_PLUGIN_DIR . '/wp-seopress/vendor/autoload.php';
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
