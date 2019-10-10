<?php

namespace AlexDashkin\Adwpfw\Items\Basic;

use AlexDashkin\Adwpfw\App;
use AlexDashkin\Adwpfw\Exceptions\AdwpfwException;
use AlexDashkin\Adwpfw\Traits\ItemTrait;

/**
 * Basic Item
 */
abstract class Item
{
    use ItemTrait;

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
     * @throws AdwpfwException
     */
    public function __construct(array $data, App $app, array $props = [])
    {
        $this->app = $app;
        $this->config = $app->config;
        $this->props = $props;

        $this->data = $this->validate($data);
    }

    protected function getDefaultSlug($base = 'item')
    {
        return $this->config['prefix'] . '-' . sanitize_title($base);
    }

    /**
     * Get Module
     *
     * @param string $moduleName Module Name
     * @return \AlexDashkin\Adwpfw\Modules\Basic\Module
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
        $this->app->log($message, $values, $type);
    }
}
