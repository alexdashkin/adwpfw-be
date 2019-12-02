<?php

namespace AlexDashkin\Adwpfw\Items;

use AlexDashkin\Adwpfw\Abstracts\BasicItem;
use AlexDashkin\Adwpfw\App;
use AlexDashkin\Adwpfw\Exceptions\AdwpfwException;

/**
 * Basic Item
 */
abstract class Item extends BasicItem
{
    /**
     * Constructor.
     *
     * @param App $app
     * @param array $data
     * @param array $props
     *
     * @throws AdwpfwException
     */
    protected function __construct(App $app, array $data, array $props = [])
    {
        parent::__construct($app, $data, $props);
    }
}
