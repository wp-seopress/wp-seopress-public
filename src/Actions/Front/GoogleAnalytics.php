<?php

namespace SEOPress\Actions\Front;

if (!defined('ABSPATH')) {
    exit;
}

use SEOPress\Core\Hooks\ExecuteHooksFrontend;
use SEOPress\ManualHooks\Thirds\WooCommerce\WooCommerceAnalytics;

class GoogleAnalytics implements ExecuteHooksFrontend {
    /**
     * Registers hooks for Google Analytics.
     *
     * @since 4.4.0
     * @return void
     */
    public function hooks(): void {
        $gaOptionService = seopress_get_service('GoogleAnalyticsOption');
        $disableOption = $gaOptionService->getDisable();
        $halfDisableOption = $gaOptionService->getHalfDisable();

        // Check user consent or GA disable setting
        if (
            $halfDisableOption === '1' ||
            ($this->isUserConsentGiven() && $disableOption === '1') ||
            $disableOption !== '1'
        ) {
            if (!$this->shouldExcludeCurrentUser($gaOptionService)) {
                add_action('init', [$this, 'analytics']);
            }
        }
    }

    /**
     * Handles Google Analytics logic.
     *
     * @since 4.4.0
     * @return void
     */
    public function analytics(): void {
        $gaOptionService = seopress_get_service('GoogleAnalyticsOption');

        // Ensure GA4 and GA option is enabled
        if ($gaOptionService->getGA4() !== '' && $gaOptionService->getEnableOption() === '1') {
            if (seopress_get_service('WooCommerceActivate')->isActive()) {
                (new WooCommerceAnalytics())->hooks();
            }
        }
    }

    /**
     * Checks if the current user should be excluded based on role.
     *
     * @since 4.4.0
     * @param object $gaOptionService The Google Analytics option service.
     * @return bool True if the user should be excluded, false otherwise.
     */
    private function shouldExcludeCurrentUser(object $gaOptionService): bool {
        if (!is_user_logged_in()) {
            return false;
        }

        $user = wp_get_current_user();
        $rolesToExclude = $gaOptionService->getRoles();

        if (!empty($rolesToExclude) && isset($user->roles[0])) {
            return array_key_exists($user->roles[0], $rolesToExclude);
        }

        return false;
    }

    /**
     * Checks if user consent has been given.
     *
     * @since 4.4.0
     * @return bool True if consent is given, false otherwise.
     */
    private function isUserConsentGiven(): bool {
        return isset($_COOKIE['seopress-user-consent-accept']) && $_COOKIE['seopress-user-consent-accept'] === '1';
    }
}
