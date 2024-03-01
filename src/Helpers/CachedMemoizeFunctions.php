<?php

namespace SEOPress\Helpers;

if ( ! defined('ABSPATH')) {
    exit;
}

abstract class CachedMemoizeFunctions {
   protected static $cache = [];

   public static function memoize($func){
        $cache = &self::$cache;
        return function() use ($func, &$cache){
            $args = func_get_args();
            $key = md5(serialize($args));

            if ( ! isset($cache[$key])) {
                $cache[$key] = call_user_func_array($func, $args);
            }

            return $cache[$key];
        };
   }
}
