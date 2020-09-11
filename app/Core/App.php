<?php

namespace AlexDashkin\Adwpfw\Core;

use AlexDashkin\Adwpfw\Exceptions\AppException;
use AlexDashkin\Adwpfw\Modules\Module;

class App
{
    /**
     * @var array
     */
    private $config;

    /**
     * @var array
     */
    private $classDefs;

    /**
     * @var Main
     */
    public $main;

    /**
     * @var Logger
     */
    public $logger;

    /**
     * @var Module[]
     */
    private $modules = [];

    /**
     * App constructor
     */
    public function __construct(array $config)
    {
        $this->config = $config;

        $this->classDefs = require __DIR__ . '/../../config/modules.php';

        $this->main = new Main($this);

        $this->logger = new Logger($this);
    }

    /**
     * Get a Config value.
     *
     * @param string $key
     * @return mixed
     */
    public function config(string $key, $default = null)
    {
        return array_key_exists($key, $this->config) ? $this->config[$key] : $default;
    }

    /**
     * Make/Get a Module
     *
     * @param string $alias
     * @param array $args
     * @throws AppException
     */
    public function make(string $alias, array $args = [])
    {
        // If already exists - return it
        if (!empty($this->modules[$alias])) {
            return $this->modules[$alias];
        }

        // If not listed in config - error
        if (empty($this->classDefs[$alias]) || !class_exists($this->classDefs[$alias]['class'])) {
            throw new AppException(sprintf('Class %s not found', $alias));
        }

        // Shorthand
        $classData = $this->classDefs[$alias];

        // Create instance and provide data
        try {
            // Create instance
            $instance = new $classData['class']($this);

            // Set data
            if (method_exists($instance, 'setProps')) {
                // Set Module Data
                $instance->setProps($args);
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
