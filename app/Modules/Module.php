<?php

namespace AlexDashkin\Adwpfw\Modules;

use AlexDashkin\Adwpfw\{App, Exceptions\AppException};
use AlexDashkin\Adwpfw\Traits\Props;

abstract class Module
{
    use Props;

    /** @var App */
    private $app;

    /**
     * Module constructor
     *
     * @param array $props
     * @throws AppException
     */
    public function __construct(array $props, App $app)
    {
        $this->props = $props;
        $this->app = $app;

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
        return $this->app->addHook($tag, $callback, $priority);
    }

    /**
     * Add a log entry
     *
     * @param mixed $message Text or any other type including WP_Error.
     * @param array $values If passed, vsprintf() func is applied. Default [].
     */
    protected function log($message, array $values = [])
    {
        $this->app->log($message, $values);
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
