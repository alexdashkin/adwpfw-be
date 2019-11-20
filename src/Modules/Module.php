<?php

namespace AlexDashkin\Adwpfw\Modules;

use AlexDashkin\Adwpfw\Abstracts\Basic;
use AlexDashkin\Adwpfw\App;

/**
 * Basic Module
 *
 * Singleton would not work as multiple Apps are possible
 */
abstract class Module extends Basic
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