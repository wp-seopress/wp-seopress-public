<?php

namespace SEOPress\Actions\Table;

defined('ABSPATH') or exit('Cheatin&#8217; uh?');

use SEOPress\Core\Hooks\ExecuteHooks;
use SEOPress\Core\Hooks\ActivationHook;

class CreateTable implements ExecuteHooks, ActivationHook {
    public function hooks() {
        add_action('admin_init', [$this, 'init']);
    }

    public function init() {
        if ( ! is_user_logged_in() || ! is_seopress_page()) {
            return;
        }
        
        $this->createTables();
    }

    public function activate() {
        $this->createTables();
    }

    private function createTables() {
        $tables = seopress_get_service('TableList')->getTables();
        seopress_get_service('TableManager')->createTablesIfNeeded($tables);
    }
}
