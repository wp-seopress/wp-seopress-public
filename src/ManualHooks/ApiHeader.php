<?php

namespace SEOPress\ManualHooks;

if (! defined('ABSPATH')) {
    exit;
}


class ApiHeader
{
    public function hooks()
    {
        add_filter('http_request_args', [$this, 'addHeaderRequest']);
    }

    public function addHeaderRequest($arguments)
    {
        $body = $arguments['body'];

        if (is_array($body)) {
            $body = implode('', $body);
        }

        $arguments['headers']['expect'] = !empty($body) && strlen($body) > 1048576 ? '100-Continue' : '';

        return $arguments;
    }
}
