<?php

namespace AlexDashkin\Adwpfw\Items;

use AlexDashkin\Adwpfw\App;
use AlexDashkin\Adwpfw\Exceptions\AdwpfwException;

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
    protected $props = [];

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

        $this->data = $this->validate($data);
    }

    protected function validate($data)
    {
        foreach ($this->props as $name => $def) {
            $field = array_merge([
                'type' => 'string',
                'required' => false,
                'default' => null,
            ], $def);

            if (!isset($data[$name])) {
                if ($field['required']) {
                    throw new AdwpfwException("Field $name is required"); // todo
                } else {
                    $data[$name] = $field['default'];
                }
            }

            $item =& $data[$name];

            if ('callable' === $field['type'] && !is_callable($item)) {
                throw new AdwpfwException("Field $name is not callable"); // todo
            }

            switch ($field['type']) {
                case 'string':
                    $item = trim($item);
                    break;

                case 'int':
                    $item = (int)$item;
                    break;

                case 'bool':
                    $item = (bool)$item;
                    break;

                case 'array':
                    $item = (array)$item;

                    if (!empty($field['def'])) {
                        foreach ($item as &$subItem) {
                            $subItem = array_merge($field['def'], $subItem);
                        }
                    }

                    break;
            }
        }

        return $data;
    }

    protected function getDefaultSlug($base = 'item')
    {
        return $this->config['prefix'] . '-' . sanitize_title($base);
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
    protected function log($message, $values = [], $type = 4)
    {
        $this->m('Logger')->log($message, $values, $type);
    }
}
