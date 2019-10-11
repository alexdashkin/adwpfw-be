<?php

namespace AlexDashkin\Adwpfw\Modules\Basic;

use AlexDashkin\Adwpfw\App;

/**
 * Basic Module
 *
 * Singleton will not work as multiple Apps are possible
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
     * Constructor.
     * Must not be called directly, only via App::m()
     *
     * @param App $app
     */
    protected function __construct(App $app)
    {
        $this->app = $app;
        $this->config = $app->config;
    }

    /**
     * Get Module.
     *
     * @param string $moduleName
     * @return Module
     * @throws \AlexDashkin\Adwpfw\Exceptions\AdwpfwException
     */
    protected function m($moduleName)
    {
        return $this->app->m($moduleName);
    }
}