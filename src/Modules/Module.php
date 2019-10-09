<?php

namespace AlexDashkin\Adwpfw\Modules;

use AlexDashkin\Adwpfw\App;

/**
 * Basic Module Class
 */
abstract class Module
{
    /**
     * @var App
     */
    protected $app;

    /**
     * @var array Config
     */
    protected $config;

    /**
     * Constructor
     *
     * @param App $app
     */
    public function __construct(App $app)
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
    protected function log($message, $values = [], $type = 4)
    {
        $this->m('Logger')->log($message, $values, $type);
    }
}