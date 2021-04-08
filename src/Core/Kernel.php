<?php

namespace SEOPress\Core;

defined('ABSPATH') or exit('Cheatin&#8217; uh?');

use SEOPress\Core\Container\ContainerSeopress;
use SEOPress\Core\Hooks\ActivationHook;
use SEOPress\Core\Hooks\DeactivationHook;
use SEOPress\Core\Hooks\ExecuteHooks;
use SEOPress\Core\Hooks\ExecuteHooksBackend;
use SEOPress\Core\Hooks\ExecuteHooksFrontend;

abstract class Kernel {
    protected static $container = null;

    protected static $data = ['slug' => null, 'main_file' => null, 'file' => null, 'root' => null];

    public static function setContainer(ManageContainer $container) {
        self::$container = self::getDefaultContainer();
    }

    protected static function getDefaultContainer() {
        return new ContainerSeopress();
    }

    public static function getContainer() {
        if (null === self::$container) {
            self::$container = self::getDefaultContainer();
        }

        return self::$container;
    }

    public static function handleHooksPlugin() {
        switch (current_filter()) {
            case 'plugins_loaded':
                foreach (self::getContainer()->getActions() as $key => $class) {
                    try {
                        if ( ! class_exists($class)) {
                            continue;
                        }

                        $class = new $class();
                        switch (true) {
                            case $class instanceof ExecuteHooksBackend:
                                if (is_admin()) {
                                    $class->hooks();
                                }
                                break;

                            case $class instanceof ExecuteHooksFrontend:
                                if ( ! is_admin()) {
                                    $class->hooks();
                                }
                                break;

                            case $class instanceof ExecuteHooks:
                                $class->hooks();
                                break;
                        }
                    } catch (\Exception $e) {
                    }
                }
                break;
            case 'activate_' . self::$data['slug'] . '/' . self::$data['main_file'] . '.php':
                foreach (self::getContainer()->getActions() as $key => $class) {
                    try {
                        if ( ! class_exists($class)) {
                            continue;
                        }
                        $class = new $class();

                        if ($class instanceof ActivationHook) {
                            $class->activate();
                        }
                    } catch (\Exception $e) {
                    }
                }
                break;
            case 'deactivate_' . self::$data['slug'] . '/' . self::$data['main_file'] . '.php':
                foreach (self::getContainer()->getActions() as $key => $class) {
                    try {
                        if ( ! class_exists($class)) {
                            continue;
                        }
                        $class = new $class();
                        if ($class instanceof DeactivationHook) {
                            $class->deactivate();
                        }
                    } catch (\Exception $e) {
                    }
                }
                break;
        }
    }

    /**
     * @static
     *
     * @return void
     */
    public static function buildContainer() {
        self::buildClasses(self::$data['root'] . '/src/Services', 'services', 'Services\\');
        self::buildClasses(self::$data['root'] . '/src/Thirds', 'services', 'Thirds\\');
        self::buildClasses(self::$data['root'] . '/src/Actions', 'actions', 'Actions\\');
    }

    /**
     * @static
     *
     * @param string $path
     * @param string $type
     * @param string $namespace
     *
     * @return void
     */
    public static function buildClasses($path, $type, $namespace = '') {
        try {
            $files      = array_diff(scandir($path), ['..', '.']);
            foreach ($files as $filename) {
                $pathCheck = $path . '/' . $filename;

                if (is_dir($pathCheck)) {
                    self::buildClasses($pathCheck, $type, $namespace . $filename . '\\');
                    continue;
                }

                $pathinfo = pathinfo($filename);
                if (isset($pathinfo['extension']) && 'php' !== $pathinfo['extension']) {
                    continue;
                }

                $data = '\\SEOPress\\' . $namespace . str_replace('.php', '', $filename);

                switch ($type) {
                    case 'services':
                        self::getContainer()->setService($data);
                        break;
                    case 'actions':
                        self::getContainer()->setAction($data);
                        break;
                }
            }
        } catch (\Exception $e) {
        }
    }

    public static function execute($data) {
        self::$data = array_merge(self::$data, $data);

        self::buildContainer();

        add_action('plugins_loaded', [__CLASS__, 'handleHooksPlugin']);
        register_activation_hook($data['file'], [__CLASS__, 'handleHooksPlugin']);
        register_deactivation_hook($data['file'], [__CLASS__, 'handleHooksPlugin']);
    }
}
