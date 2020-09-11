<?php

namespace AlexDashkin\Adwpfw\Modules;

use AlexDashkin\Adwpfw\App;
use AlexDashkin\Adwpfw\Exceptions\AppException;

abstract class Module
{
    /**
     * @var App
     */
    protected $app;

    /**
     * @var array Item Props
     */
    private $props = [];

    /**
     * Module constructor
     *
     * @param App $app
     */
    public function __construct(App $app)
    {
        $this->app = $app;
    }

    /**
     * Get Single Prop
     * Throws Exception if no value and no default provided
     *
     * @param string $key
     * @return mixed
     * @throws AppException
     */
    protected function getProp(string $key)
    {
        if (array_key_exists($key, $this->props)) {
            return $this->props[$key];
        }

        if (!is_null($default = $this->getDefault($key))) {
            return $default;
        }

        throw new AppException(sprintf('Prop %s not found', $key));
    }

    /**
     * Get All Props
     *
     * @return array
     */
    protected function getProps(): array
    {
        return $this->props;
    }

    /**
     * Set Single Prop
     *
     * @param string $key
     * @param mixed $value
     */
    protected function setProp(string $key, $value)
    {
        $this->props[$key] = $value;
    }

    /**
     * Set Many Props
     *
     * @param array $data
     */
    protected function setProps(array $data)
    {
        foreach ($data as $key => $value) {
            $this->setProp($key, $value);
        }
    }

    /**
     * Get Default Prop value. To be overridden.
     *
     * @param string $key
     * @return mixed
     */
    protected function getDefault(string $key)
    {
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
        $this->app->logger->log($message, $values, $level);
    }
}
