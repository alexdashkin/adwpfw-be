<?php

namespace AlexDashkin\Adwpfw\Modules;

use AlexDashkin\Adwpfw\App;

/**
 * Ajax Base Module. To be extended.
 */
abstract class ModuleAjax extends ModuleWithItems
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
     * Return Success array.
     *
     * @param string $message Message.
     * @param array $data Data to return as JSON.
     * @param bool $echo Whether to echo Response right away without returning.
     * @return array
     */
    protected function success($message = '', $data = [], $echo = false)
    {
        return Helpers::returnSuccess($message, $data, $echo);
    }

    /**
     * Return Error array.
     *
     * @param string $message Error message.
     * @param bool $echo Whether to echo Response right away without returning.
     * @return array
     */
    protected function error($message = '', $echo = false)
    {
        return Helpers::returnError($message, $echo);
    }
}