<?php

namespace AlexDashkin\Adwpfw\Items;

use AlexDashkin\Adwpfw\Abstracts\BasicItemWithItems;
use AlexDashkin\Adwpfw\App;
use AlexDashkin\Adwpfw\Exceptions\AdwpfwException;

/**
 * Item with Items
 */
abstract class ItemWithItems extends BasicItemWithItems
{
    /**
     * Constructor.
     *
     * @param array $data
     * @param App $app
     * @param array $props
     * @throws AdwpfwException
     */
    protected function __construct(App $app, array $data, array $props = [])
    {
        parent::__construct($app, $data, $props);
    }
}
