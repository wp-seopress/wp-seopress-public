<?php

namespace SEOPress\Services\Metas;

if (! defined('ABSPATH')) {
    exit;
}

use SEOPress\Helpers\Metas\RedirectionSettings;

class RedirectionMeta
{
    protected function getKeyValue($meta){
        switch($meta){
            case '_seopress_redirections_enabled':
                return 'enabled';
            case '_seopress_redirections_logged_status':
                return 'status';
            case '_seopress_redirections_type':
                return 'type';
            case '_seopress_redirections_value':
                return 'value';
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

        $metas = RedirectionSettings::getMetaKeys($id);

        $data = [];
        foreach ($metas as $key => $value) {
            $name = $this->getKeyValue($value['key']);
            if($name === null){
                continue;
            }
            if ($value['use_default']) {
                $data[$name] = $value['default'];
            } else {
                $result = $callback($id, $value['key'], true);
                $data[$name] = 'checkbox' === $value['type'] ? ($result === true || $result === 'yes' ? true : false) : $result;
            }
        }

        return $data;
    }
}



