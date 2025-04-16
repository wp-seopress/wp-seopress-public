<?php

namespace SEOPress\Services\Metas;

if (! defined('ABSPATH')) {
    exit;
}

use SEOPress\Helpers\Metas\RobotSettings;

class RobotMeta
{
    protected function getKeyValue($meta){
        switch($meta){
            case '_seopress_robots_index':
                return 'noindex';
            case '_seopress_robots_follow':
                return 'nofollow';
            case '_seopress_robots_snippet':
                return 'nosnippet';
            case '_seopress_robots_imageindex':
                return 'noimageindex';
            case '_seopress_robots_canonical':
                return 'canonical';
            case '_seopress_robots_primary_cat':
                return 'primarycat';
            case '_seopress_robots_breadcrumbs':
                return 'breadcrumbs';
        }

        return null;
    }

    /**
     *
     * @param array $context
     * @param bool $useDefault Use default value only if you get the value from the database after this function
     * @return string|null
     */
    public function getValue($context , $useDefault = true)
    {
        $data = [];

        $id = null;

        $callback = 'get_post_meta';
        if(isset($context['post'])){
            $id = $context['post']->ID;
        }
        else if(isset($context['term_id'])){
            $id = $context['term_id'];
            $callback = 'get_term_meta';
        }

        if(!$id){
            return $data;
        }

        $metas = RobotSettings::getMetaKeys($id);

        $data = [];
        foreach ($metas as $key => $value) {
            $name = $this->getKeyValue($value['key']);
            if($name === null){
                continue;
            }

            if ($value['use_default'] && $useDefault) {
                $data[$name] = $value['default'] === true || $value['default'] === 'yes' ? true : $value['default'];
            } else {
                $result = $callback($id, $value['key'], true);

                $data[$name] = 'checkbox' === $value['type'] ? ($result === true || $result === 'yes' ? true : false) : $result;
            }
        }

        return $data;
    }
}



