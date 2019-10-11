<?php

namespace AlexDashkin\Adwpfw\Modules\Basic;

use AlexDashkin\Adwpfw\App;

/**
 * Module with Logger
 */
abstract class ModuleWithLogger extends Module
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

    /**
     * Add a log entry.
     *
     * @param mixed $message Text or any other type including WP_Error.
     * @param array $values If passed, vsprintf() func is applied.
     * @param int $type 1 = Error, 2 = Warning, 4 = Notice.
     *
     * @throws \AlexDashkin\Adwpfw\Exceptions\AdwpfwException
     */
    protected function log($message, $values = [], $type = 4)
    {
        $this->m('Logger')->log($message, $values, $type);
    }
}