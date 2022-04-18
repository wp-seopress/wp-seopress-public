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
            case '_seopress_robots_odp':
                return 'noodp';
            case '_seopress_robots_archive':
                return 'noarchive';
            case '_seopress_robots_snippet':
                return 'nosnippet';
            case '_seopress_robots_imageindex':
                return 'noimageindex';
            case '_seopress_robots_canonical':
                return 'canonical';
        }

        return null;
    }

    /**
     *
     * @param array $context
     * @return string|null
     */
    public function getValue($context)
    {
        $data = [];
        if(!isset($context['post'])){
            return $data;
        }

        $id = $context['post']->ID;

        $metas = RobotSettings::getMetaKeys($id);

        $data = [];
        foreach ($metas as $key => $value) {
            $name = $this->getKeyValue($value['key']);
            if($name === null){
                continue;
            }
            if ($value['use_default']) {
                $data[$name] = $value['default'];
            } else {
                $result = get_post_meta($id, $value['key'], true);
                $data[$name] = 'checkbox' === $value['type'] ? ($result === true || $result === 'yes' ? true : false) : $result;
            }
        }

        return $data;
    }
}



