<?php

namespace AlexDashkin\Adwpfw\Modules\Basic;

use AlexDashkin\Adwpfw\App;

/**
 * Module with Logger shortcut
 */
abstract class ModuleWithLogger extends Module
{
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
     * Add log entry
     *
     * @param mixed $message
     */
    protected function log($message, $values = [], $type = 4)
    {
        $this->m('Logger')->log($message, $values, $type);
    }
}