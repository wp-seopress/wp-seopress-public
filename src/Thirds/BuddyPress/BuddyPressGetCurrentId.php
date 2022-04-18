<?php

namespace SEOPress\Thirds\BuddyPress;

defined('ABSPATH') or exit('Cheatin&#8217; uh?');


class BuddyPressGetCurrentId {

    /**
     * @since 4.4.0
     *
     * @return int|null
     */
    public function getCurrentId() {
        $id = null;

        if (function_exists('bp_is_current_component') && true == bp_is_current_component('activity')) {
            $id = buddypress()->pages->activity->id;
        }
        //IS BUDDYPRESS MEMBERS PAGE
        if (function_exists('bp_is_current_component') && true == bp_is_current_component('members')) {
            $id = buddypress()->pages->members->id;
        }

        //IS BUDDYPRESS GROUPS PAGE
        if (function_exists('bp_is_current_component') && true == bp_is_current_component('groups')) {
            $id = buddypress()->pages->groups->id;
        }

        return $id;
    }
}
