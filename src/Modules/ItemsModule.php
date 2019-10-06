<?php

namespace AlexDashkin\Adwpfw\Modules;

use AlexDashkin\Adwpfw\App;

/**
 * Basic Module with Items Class
 */
abstract class ItemsModule extends Module
{
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
        parent::__construct($app);
    }

    /**
     * Add multiple Items
     *
     * @param $items[] $data
     */
    public function addMany(array $data, App $app)
    {
        foreach ($data as $item) {
            $this->add($item, $app);
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
            $found = $this->searchItems($found, $conditions);
        }

        return $single ? reset($found) : $found;
    }

    /**
     * Add an item
     *
     * @param array $data
     */
    abstract public function add(array $data, App $app);
}