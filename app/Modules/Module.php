<?php

namespace AlexDashkin\Adwpfw\Modules;

use AlexDashkin\Adwpfw\{Exceptions\AppException, Logger};
use AlexDashkin\Adwpfw\Traits\Props;

abstract class Module
{
    use Props;

    /**
     * Module constructor
     *
     * @param array $props
     * @throws AppException
     */
    public function __construct(array $props)
    {
        $this->props = $props;

        foreach ($props as $name => $value) {
            $this->setProp($name, $value);
        }

        $this->checkRequiredProps();

        if (method_exists($this, 'init')) {
            $this->init();
        }
    }

    /**
     * Add Hook (action/filter)
     *
     * @param string $tag
     * @param callable $callback
     * @param int $priority
     * @return Hook
     */
    public function addHook(string $tag, callable $callback, int $priority = 10): Hook
    {
        return new Hook(
            [
                'tag' => $tag,
                'callback' => $callback,
                'priority' => $priority,
            ]
        );
    }

    /**
     * Add a log entry
     *
     * @param mixed $message Text or any other type including WP_Error.
     * @param array $values If passed, vsprintf() func is applied. Default [].
     */
    protected function log($message, array $values = [])
    {
        Logger::log($message, $values);
    }

    /**
     * Get prop definitions
     *
     * @return array
     */
    protected function getPropDefs(): array
    {
        return [];
    }
}
