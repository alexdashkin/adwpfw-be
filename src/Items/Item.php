<?php

namespace AlexDashkin\Adwpfw\Items;

use AlexDashkin\Adwpfw\App;
use AlexDashkin\Adwpfw\Modules\Helpers;

/**
 * Module Item Basic Class
 */
abstract class Item
{
    /**
     * @var array Entity Data
     */
    public $data = [];

    /**
     * @var array Entity Defaults
     */
    protected $defaults = [];

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
     * @param array $data
     */
    public function __construct(array $data, App $app)
    {
        $this->app = $app;
        $this->config = $app->config;

        $this->data = Helpers::arrayMerge($this->defaults, $data);

        $this->hooks();
    }

    /**
     * Get Module
     *
     * @param string $moduleName Module Name
     * @return \AlexDashkin\Adwpfw\Modules\Module
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
        $this->app->log($message);
    }

    /**
     * Hooks to register Item in WP
     */
    abstract protected function hooks();
}
