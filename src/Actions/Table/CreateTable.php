<?php

namespace SEOPress\Actions\Table;

defined('ABSPATH') or exit('Cheatin&#8217; uh?');

use SEOPress\Core\Hooks\ExecuteHooks;

class CreateTable implements ExecuteHooks {
    public function hooks() {
        add_action('init', [$this, 'init']);
    }

    public function init() {
        if ( ! is_user_logged_in()) {
            return;
        }

        $tables = seopress_get_service('TableList')->getTables();
        seopress_get_service('TableManager')->createTablesIfNeeded($tables);
    }
}
