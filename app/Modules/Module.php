<?php

namespace AlexDashkin\Adwpfw\Modules;

use AlexDashkin\Adwpfw\Core\App;
use AlexDashkin\Adwpfw\Core\Main;
use AlexDashkin\Adwpfw\Exceptions\AppException;

abstract class Module
{
    /**
     * @var Main
     */
    protected $main;

    /**
     * @var App
     */
    protected $app;

    /**
     * @var array Item Props
     */
    protected $props = [];

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
     * Get Single Prop
     *
     * @param string $key
     * @return mixed
     */
    public function getProp(string $key)
    {
        return array_key_exists($key, $this->props) ? $this->props[$key] : $this->getDefault($key);
    }

    /**
     * Get All Props
     *
     * @return array
     */
    public function getProps(): array
    {
        $set = $default = [];

        foreach ($this->defaults() as $key => $value) {
            $default[$key] = $this->getDefault($key);
        }

        foreach ($this->props as $key => $value) {
            $set[$key] = $this->getProp($key);
        }

        return array_merge($default, $set);
    }

    /**
     * Set Single Prop
     *
     * @param string $key
     * @param mixed $value
     */
    public function setProp(string $key, $value)
    {
        $this->props[$key] = $value;
    }

    /**
     * Set Many Props
     *
     * @param array $data
     */
    public function setProps(array $data)
    {
        foreach ($data as $key => $value) {
            $this->setProp($key, $value);
        }
    }

    /**
     * Get Default Prop value
     *
     * @param string $key
     * @return mixed
     */
    protected function getDefault(string $key)
    {
        $defaults = $this->defaults();

        if (array_key_exists($key, $defaults)) {
            return is_callable($defaults[$key]) ? $defaults[$key]() : $defaults[$key];
        }

        return null;
    }

    /**
     * Get a Config value.
     * Throws Exception if no value and no default provided
     *
     * @param string $key
     * @return mixed
     * @throws AppException
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

    /**
     * Get Default prop values, to be overridden
     *
     * @return array
     */
    protected function defaults(): array {
        return [];
    }
}
