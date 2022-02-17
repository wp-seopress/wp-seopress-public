<?php

namespace SEOPress\Helpers\Metas;

if ( ! defined('ABSPATH')) {
	exit;
}

abstract class MetasHelper {
	/**
	 * @param int|null $id
	 *
	 * @return array[]
	 * @since 5.0.0
	 *
	 */
	public static function getEntityMetasData(int $id = null)
	{
		$context = seopress_get_service('ContextPage')->buildContextWithCurrentId($id)->getContext();

		$title = seopress_get_service('TitleMeta')->getValue($context);
		$description = seopress_get_service('DescriptionMeta')->getValue($context);
		$social = seopress_get_service('SocialMeta')->getValue($context);
		$robots = seopress_get_service('RobotMeta')->getValue($context);

		$canonical =  '';
		if(isset($robots['canonical'])){
			$canonical = $robots['canonical'];
			unset($robots['canonical']);
		}

		$data = [
			"title" => $title,
			"description" => $description,
			"canonical" => $canonical,
			"og" => $social['og'],
			"twitter" => $social['twitter'],
			"robots" => $robots
		];

		return apply_filters('seopress_headless_get_post', $data, $id, $context);
	}
}
