<?php

namespace AlexDashkin\Adwpfw\Modules;

use AlexDashkin\Adwpfw\Abstracts\BasicWithItems;
use AlexDashkin\Adwpfw\App;

/**
 * Module with Items
 */
abstract class ModuleWithItems extends BasicWithItems
{
    /**
     * Constructor.
     *
     * @param App $app
     */
    protected function __construct(App $app)
    {
        parent::__construct($app);
    }
}