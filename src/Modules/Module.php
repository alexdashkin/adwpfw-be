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
     * @var array Module Items
     */
    protected $items = [];

    /**
     * Constructor
     *
     * @param App $app
     */
    public function __construct(App $app)
    {
        $this->app = $app;
        $this->config = $app->config;
        $this->run();
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
    protected function log($message)
    {
        $this->m('Common\Log')->log($message);
    }

    protected function run()
    {
    }
}