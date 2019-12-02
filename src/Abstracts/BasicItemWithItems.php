<?php

namespace AlexDashkin\Adwpfw\Abstracts;

use AlexDashkin\Adwpfw\App;
use AlexDashkin\Adwpfw\Exceptions\AdwpfwException;

/**
 * Basic Class with Item functionality containing Items
 */
abstract class BasicItemWithItems extends BasicItem
{
    /**
     * @var array Items
     */
    protected $items = [];

    /**
     * Constructor
     *
     * @param App $app
     * @param array $data
     * @param array $props
     *
     * @throws AdwpfwException
     */
    protected function __construct(App $app, array $data = [], array $props = [])
    {
        parent::__construct($app, $data, $props);
    }

    /**
     * Add an Item.
     *
     * @param array $data
     */
    public function add(array $data)
    {
    }

    /**
     * Add multiple Items.
     *
     * @param array $data
     */
    public function addMany(array $data)
    {
        foreach ($data as $item) {
            $this->add($item);
        }
    }

    /**
     * Search Items by conditions.
     *
     * @param array $conditions
     * @param bool $single Whether to return one single item.
     * @return BasicItem|BasicItem[]
     */
    protected function searchItems($conditions, $single = false)
    {
        $found = [];
        $searchValue = end($conditions);
        $searchField = key($conditions);

        array_pop($conditions);

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
}