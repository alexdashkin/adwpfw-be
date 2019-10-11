<?php

namespace AlexDashkin\Adwpfw\Items\WithItems;

use AlexDashkin\Adwpfw\App;
use AlexDashkin\Adwpfw\Exceptions\AdwpfwException;
use AlexDashkin\Adwpfw\Items\Basic\Item;
use AlexDashkin\Adwpfw\Traits\ItemWithItemsTrait;

/**
 * Item with Items
 */
abstract class ItemWithItems extends Item
{
    use ItemWithItemsTrait;

    /**
     * Constructor.
     *
     * @param array $data
     * @param App $app
     * @param array $props
     * @throws AdwpfwException
     */
    protected function __construct(array $data, App $app, array $props = [])
    {
        parent::__construct($data, $app, $props);
    }
}
