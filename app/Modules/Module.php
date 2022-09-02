<?php

namespace AlexDashkin\Adwpfw\Modules;

use AlexDashkin\Adwpfw\{App, Exceptions\AppException};

abstract class Module
{
    /** @var App */
    protected $app;

    /**
     * Module constructor
     *
     * @param array $props
     * @param App $app
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
     * Get Config
     *
     * @param string $key
     * @return mixed
     */
    public function config(string $key = '')
    {
        return $this->app->config($key);
    }

    /**
     * Prefix a string
     *
     * @param string $string
     * @param string $separator
     * @param bool $leadingUnderscore
     * @return string
     */
    public function prefixIt(string $string, string $separator = '_', bool $leadingUnderscore = false): string
    {
        return $this->app->prefixIt($string, $separator, $leadingUnderscore);
    }

    /**
     * Get prefixed option
     *
     * @param string $name
     * @return mixed
     */
    protected function getOption(string $name)
    {
        return $this->app->getOption($name);
    }

    /**
     * Update prefixed option
     *
     * @param string $name
     * @param $value
     * @return bool
     */
    protected function updateOption(string $name, $value): bool
    {
        return $this->app->updateOption($name, $value);
    }

    /**
     * Get prefixed transient
     *
     * @param string $name
     * @return mixed
     */
    protected function getTransient(string $name)
    {
        return $this->app->getTransient($name);
    }

    /**
     * Set prefixed transient
     *
     * @param string $name
     * @param $value
     * @param int $expiration
     * @return bool
     */
    public function setTransient(string $name, $value, int $expiration = 0): bool
    {
        return $this->app->setTransient($name, $value, $expiration);
    }

    /**
     * Delete prefixed transient
     *
     * @param string $name
     * @return bool
     */
    public function deleteTransient(string $name): bool
    {
        return $this->app->deleteTransient($name);
    }

    /**
     * Get prefixed post meta
     *
     * @param int $postId
     * @param string $name
     * @return mixed
     */
    public function getPostMeta(int $postId, string $name)
    {
        return $this->app->getPostMeta($postId, $name);
    }

    /**
     * Update prefixed post meta
     *
     * @param int $postId
     * @param string $name
     * @param $value
     * @return bool|int
     */
    public function updatePostMeta(int $postId, string $name, $value)
    {
        return $this->app->updatePostMeta($postId, $name, $value);
    }

    /**
     * Get prefixed term meta
     *
     * @param int $termId
     * @param string $name
     * @return mixed
     */
    public function getTermMeta(int $termId, string $name)
    {
        return $this->app->getTermMeta($termId, $name);
    }

    /**
     * Update prefixed term meta
     *
     * @param int $termId
     * @param string $name
     * @param $value
     * @return bool|int
     */
    public function updateTermMeta(int $termId, string $name, $value)
    {
        return $this->app->updateTermMeta($termId, $name, $value);
    }

    /**
     * Get prefixed user meta
     *
     * @param int $userId
     * @param string $name
     * @return mixed
     */
    public function getUserMeta(int $userId, string $name)
    {
        return $this->app->getUserMeta($userId, $name);
    }

    /**
     * Update prefixed user meta
     *
     * @param int $userId
     * @param string $name
     * @param $value
     * @return bool|int
     */
    public function updateUserMeta(int $userId, string $name, $value)
    {
        return $this->app->updateUserMeta($userId, $name, $value);
    }

    /**
     * Add Hook (action/filter)
     *
     * @param string $tag
     * @param callable $callback
     * @param int $priority
     * @return Hook
     */
    protected function addHook(string $tag, callable $callback, int $priority = 10): Hook
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
     * @var array Item Props
     */
    protected $props = [];

    /**
     * Set Single Prop
     *
     * @param string $name
     * @param mixed $value
     */
    public function setProp(string $name, $value)
    {
        $this->validateProp($name, $value);

        $this->props[$name] = $value;
    }

    /**
     * Get Single Prop
     *
     * @param string $key
     * @return mixed
     */
    public function getProp(string $key)
    {
        if (isset($this->props[$key])) {
            return $this->props[$key];
        }

        $defs = $this->getPropDefs();

        if (!isset($defs[$key])) {
            return null;
        }

        if (isset($defs[$key]['default'])) {
            if ($defs[$key]['default'] instanceof \Closure) {
                return $defs[$key]['default']();
            } else {
                return $defs[$key]['default'];
            }
        }

        if (isset($defs[$key]['type'])) {
            switch ($defs[$key]['type']) {
                case 'string':
                    return '';
                case 'int':
                    return 0;
                case 'array':
                    return [];
                case 'bool':
                    return false;
                default:
                    return null;
            }
        }

        return null;
    }

    /**
     * Get all props
     *
     * @return array
     */
    public function getProps(): array
    {
        return array_merge($this->getDefaults(), $this->props);
    }

    /**
     * Validate prop
     *
     * @throws AppException
     */
    protected function validateProp($name, $value)
    {
        $defs = $this->getPropDefs();

        $validators = [
            'string' => 'is_string',
            'int' => 'is_int',
            'array' => 'is_array',
            'callable' => 'is_callable',
            'bool' => 'is_bool',
        ];

        // Skip props without defs
        if (empty($defs[$name])) {
            return;
        }

        $type = $defs[$name]['type'];

        if (!$validators[$type]($value)) {
            throw new AppException(sprintf('%s: "%s" must be %s, %s given', get_called_class(), $name, $type, gettype($value)));
        }
    }

    /**
     * Get all default values
     *
     * @return array
     */
    protected function getDefaults(): array
    {
        $defaults = [];

        foreach ($this->getPropDefs() as $name => $def) {
            if (isset($def['default'])) {
                if ($def['default'] instanceof \Closure) {
                    $defaults[$name] = $def['default']();
                } else {
                    $defaults[$name] = $def['default'];
                }
            }
        }

        return $defaults;
    }

    /**
     * Check if all required props provided
     *
     * @throws AppException
     */
    protected function checkRequiredProps()
    {
        foreach ($this->getPropDefs() as $name => $def) {
            if (!empty($def['required']) && !isset($this->props[$name])) {
                throw new AppException(sprintf('%s: "%s" is required', get_called_class(), $name));
            }
        }
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
