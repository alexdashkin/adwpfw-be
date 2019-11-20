<?php

namespace AlexDashkin\Adwpfw\Items;

use AlexDashkin\Adwpfw\Abstracts\BasicItem;
use AlexDashkin\Adwpfw\App;

/**
 * Basic Item
 */
abstract class Item extends BasicItem
{
    protected function __construct(App $app, array $data, array $props = [])
    {
        parent::__construct($app, $data, $props);
    }
}
