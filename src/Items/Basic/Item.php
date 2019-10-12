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
     * @var string Prefix
     */
    protected $prefix;

    /**
     * Constructor
     *
     * @param array $data
     * @throws AdwpfwException
     */
    protected function __construct(array $data, App $app, array $props = [])
    {
        $this->app = $app;
        $this->config = $app->config;
        $this->prefix = $app->config['prefix'];

        $defaults = [
            'id' => [
                'required' => true,
            ],
        ];

        $this->props = array_merge($defaults, $props);

        $this->data = $this->validateProps($data);
    }

    protected function getDefaultId($base)
    {
        return esc_attr(sanitize_key(str_replace(' ', '-', $base)));
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
