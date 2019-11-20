<?php

namespace AlexDashkin\Adwpfw\Abstracts;

use AlexDashkin\Adwpfw\App;
use AlexDashkin\Adwpfw\Modules\Module;

/**
 * Basic Class
 */
abstract class Basic
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
     * @var string Prefix
     */
    protected $prefix;

    /**
     * Constructor
     *
     * @param App $app
     */
    protected function __construct(App $app)
    {
        $this->app = $app;
        $this->config = $app->config;
        $this->prefix = $app->config['prefix'];
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
     * Add a log entry.
     *
     * @param mixed $message Text or any other type including WP_Error.
     * @param array $values If passed, vsprintf() func is applied.
     * @param int $type 1 = Error, 2 = Warning, 4 = Notice.
     */
    protected function log($message, $values = [], $type = 4)
    {
        $this->app->log($message, $values, $type);
    }
}