<?php

namespace AlexDashkin\Adwpfw\Core;

use AlexDashkin\Adwpfw\Exceptions\AppException;
use AlexDashkin\Adwpfw\Modules\Module;

class App
{
    /**
     * @var Main
     */
    private $main;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @var Twig
     */
    private $twig;

    /**
     * @var array
     */
    private $config;

    /**
     * @var array
     */
    private $classDefs;

    /**
     * @var Module[]
     */
    private $modules = [];

    /**
     * App constructor
     * Config: env = dev/prod, prefix, log[size,level], template_path
     */
    public function __construct(array $config)
    {
        $this->config = $config;

        $this->classDefs = require __DIR__ . '/../../config/modules.php';
    }

    public function getMain()
    {
        if (!$this->main) {
            $this->main = new Main($this);
        }
        
        return $this->main;
    }

    public function getLogger()
    {
        if (!$this->logger) {
            $this->logger = new Logger($this);
        }
        
        return $this->logger;
    }

    public function getTwig()
    {
        if (!$this->twig) {
            $this->twig = new Twig($this);
        }
        
        return $this->twig;
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
        // If not listed in config - error
        if (empty($this->classDefs[$alias]) || !class_exists($this->classDefs[$alias])) {
            throw new AppException(sprintf('Class %s not found', $alias));
        }

        // Shorthand
        $class = $this->classDefs[$alias];

        // Create instance and provide data
        try {
            // Create instance
            $instance = new $class($this);

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

        // Store in Modules prop
        $this->modules[$alias][] = $instance;

        // Return instance
        return $instance;
    }
}
