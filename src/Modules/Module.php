<?php

namespace AlexDashkin\Adwpfw\Modules;

use AlexDashkin\Adwpfw\App;
use AlexDashkin\Adwpfw\Exceptions\ModuleException;

/**
 * Basic Module Class
 */
abstract class Module
{
    /**
     * @var array Module Items
     */
    protected $items = [];

    /**
     * @var string Class name of related Entity
     */
    protected $itemClass;

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
        $this->run();
    }

    /**
     * Add an item
     *
     * @param array $data
     */
    public function add(array $data)
    {
        $this->items[] = new $this->itemClass($data);
    }

    /**
     * Add multiple items
     *
     * @param $items[] $data
     */
    public function addMany(array $data)
    {
        foreach ($data as $item) {
            $this->add($item);
        }
    }

    protected function searchItems($conditions, $single = false)
    {
        $found = [];
        $condition = array_pop($conditions);
        $searchValue = end($condition);
        $searchField = key($condition);

        foreach ($this->items as $item) {
            if (isset($item->data[$searchField]) && $item->data[$searchField] == $searchValue) {
                $found[] = $item;
            }
        }

        if (0 === count($found)) {
            return [];
        }

        if (0 !== count($conditions)) {
            $found = $this->search($found, $conditions);
        }

        return $single ? reset($found) : $found;
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
        $this->m('Logger')->log($message);
    }

    /**
     * Run the Module (typically hooks)
     */
    abstract protected function run();
}