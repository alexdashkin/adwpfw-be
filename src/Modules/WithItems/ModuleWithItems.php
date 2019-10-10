<?php

namespace AlexDashkin\Adwpfw\Modules\WithItems;

use AlexDashkin\Adwpfw\App;
use AlexDashkin\Adwpfw\Modules\Basic\ModuleWithLogger;
use AlexDashkin\Adwpfw\Traits\ItemWithItemsTrait;

/**
 * Module with Items
 */
abstract class ModuleWithItems extends ModuleWithLogger
{
    use ItemWithItemsTrait;

    /**
     * Constructor.
     *
     * @param App $app
     */
    public function __construct(App $app)
    {
        parent::__construct($app);
    }

    /**
     * Init Module.
     */
    abstract protected function init();
}