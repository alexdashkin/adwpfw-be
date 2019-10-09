<?php

namespace AlexDashkin\Adwpfw\Modules;

use AlexDashkin\Adwpfw\App;

/**
 * Basic Module Class
 */
abstract class Module
{
    protected static $instances = [];

    /**
     * @var App
     */
    protected $app;

    /**
     * @var array Config
     */
    protected $config;

    /**
     * Get Single Instance
     *
     * @param App $app
     * @return Module
     */
    public static function the(App $app)
    {
        $class = strtolower(get_called_class());

        if (!isset(static::$instances[$class])) {
            static::$instances[$class] = new static($app);
        }

        return static::$instances[$class];
    }

    /**
     * Constructor
     *
     * @param App $app
     */
    protected function __construct(App $app)
    {
        $this->app = $app;
        $this->config = $app->config;
    }

    /**
     * Get Module
     *
     * @param string $moduleName
     * @return Module
     */
    protected function m($moduleName)
    {
        return $this->app->m($moduleName);
    }

    /**
     * Add log entry
     *
     * @param mixed $message
     */
    public function log($message, $values = [], $type = 4) // todo make protected
    {
        $this->m('Logger')->log($message, $values, $type); // todo maybe make logger independent (not Module)
    }
}