<?php

if ( ! defined('ABSPATH')) {
    exit;
}

use SEOPress\Core\Kernel;

/**
 * Get a service.
 *
 * @since 4.3.0
 *
 * @param string $service
 *
 * @return object
 */
function seopress_get_service($service) {
    return Kernel::getContainer()->getServiceByName($service);
}
