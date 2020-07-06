<?php

namespace AlexDashkin\Adwpfw;

use AlexDashkin\Adwpfw\Abstracts\Module;
use AlexDashkin\Adwpfw\Exceptions\AppException;

class App
{
    /**
     * @var self Single instance
     */
    private static $instance;

    /**
     * @var array
     */
    private $config = [];

    /**
     * @var array
     */
    private $classes;

    /**
     * @var Module[]
     */
    private $modules = [];

    /**
     * App constructor
     */
    private function __construct()
    {
        $this->classes = require __DIR__ . '/../config/classes.php';
    }

    /**
     * Add params to App global config
     *
     * @param array $config
     */
    public function setConfig(array $config)
    {
        $this->config = array_merge($this->config, $config);
    }

    /**
     * Get a Config value
     *
     * @param string $key
     * @return array|mixed|null
     */
    public function getConfig(string $key = '')
    {
        if (!$key) {
            return $this->config;
        }

        return array_key_exists($key, $this->config) ? $this->config[$key] : null;
    }

    /**
     * Get Module
     *
     * @param string $alias
     * @return object
     * @throws AppException
     */
    public static function get($alias, array $args = [])
    {
        $app = self::the();

        // If already exists - return it
        if (!empty($app->modules[$alias])) {
            return $app->modules[$alias];
        }

        // If not listed in config - error
        if (empty($app->classes[$alias]) || !class_exists($app->classes[$alias]['class'])) {
            throw new AppException(sprintf('Class %s not found', $alias));
        }

        // Shorthand
        $classData = $app->classes[$alias];

        // Create instance and provide data
        try {
            // Create instance
            $instance = new $classData['class']();

            // Set data
            if (method_exists($instance, 'setMany')) {
                // Add Global Config values to args
                $args = array_merge($app->getConfig(), $args);

                // Set Module Data
                $instance->setMany($args);
            }

            // Init instance
            if (method_exists($instance, 'init')) {
                $instance->init();
            }
        } catch (\Exception $e) {
            throw new AppException(sprintf('Unable to create instance for "%s": %s', $alias, $e->getMessage()));
        }

        // Store Singletons in Modules prop
        if (!empty($classData['single'])) {
            $app->modules[$alias] = $instance;
        }

        // Return instance
        return $instance;
    }

    /**
     * Get App instance
     *
     * @return self
     */
    public static function the(): self
    {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }
}
