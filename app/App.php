<?php

namespace AlexDashkin\Adwpfw;

use AlexDashkin\Adwpfw\Exceptions\AppException;
use AlexDashkin\Adwpfw\Modules\Module;

class App
{
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
    public function __construct()
    {
        $this->classes = require __DIR__ . '/../config/modules.php';
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
     * @throws AppException
     */
    public function getModule($alias, array $args = [])
    {
        // If already exists - return it
        if (!empty($this->modules[$alias])) {
            return $this->modules[$alias];
        }

        // If not listed in config - error
        if (empty($this->classes[$alias]) || !class_exists($this->classes[$alias]['class'])) {
            throw new AppException(sprintf('Class %s not found', $alias));
        }

        // Shorthand
        $classData = $this->classes[$alias];

        // Create instance and provide data
        try {
            // Create instance
            $instance = new $classData['class']($this);

            // Set data
            if (method_exists($instance, 'spm')) {
                // Add Global Config values to args
                $args = array_merge($this->getConfig(), $args);

                // Set Module Data
                $instance->spm($args);
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
            $this->modules[$alias] = $instance;
        }

        // Return instance
        return $instance;
    }
}
