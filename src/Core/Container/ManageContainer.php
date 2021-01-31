<?php

namespace SEOPress\Core\Container;

defined('ABSPATH') or exit('Cheatin&#8217; uh?');

interface ManageContainer {
    public function getActions();

    public function getServices();

    public function getServiceByName($name);
}
