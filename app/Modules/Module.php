<?php

namespace AlexDashkin\Adwpfw\Modules;

use AlexDashkin\Adwpfw\Core\App;
use AlexDashkin\Adwpfw\Core\Main;
use AlexDashkin\Adwpfw\Traits\Props;

abstract class Module
{
    use Props;

    /**
     * @var Main
     */
    protected $main;

    /**
     * @var App
     */
    protected $app;

    /**
     * @var string
     */
    protected $prefix;

    /**
     * Module constructor
     *
     * @param App $app
     */
    public function __construct(App $app)
    {
        $this->app = $app;

        $this->main = $app->getMain();

        $this->prefix = $this->config('prefix');
    }

    /**
     * Get a Config value
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    protected function config(string $key, $default = null)
    {
        return $this->app->config($key, $default);
    }

    /**
     * Get a Module
     *
     * @param string $alias
     * @param array $args
     */
    protected function m(string $alias, array $args = [])
    {
        return $this->app->make($alias, $args);
    }

    /**
     * Add Hook
     *
     * @param string $tag
     * @param callable $callback
     * @param int $priority
     * @return Hook
     */
    protected function addHook(string $tag, callable $callback, int $priority = 10): Hook
    {
        return $this->m(
            'hook',
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
     * @param int $level 1 = Error, 2 = Warning, 4 = Notice. Default 4.
     */
    protected function log($message, array $values = [], int $level = 4)
    {
        $this->app->getLogger()->log($message, $values, $level);
    }
}
