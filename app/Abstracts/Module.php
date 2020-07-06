<?php

namespace AlexDashkin\Adwpfw\Abstracts;

use AlexDashkin\Adwpfw\App;
use AlexDashkin\Adwpfw\Exceptions\AppException;

abstract class Module
{
    /**
     * @var array Item Data
     */
    protected $data = [];

    /**
     * Get Data value
     *
     * @param string $key
     * @return mixed
     */
    public function get(string $key)
    {
        if (array_key_exists($key, $this->data)) {
            return $this->data[$key];
        }

        if (!$prop = $this->getProp($key)) {
            return null;
        }

        return $prop['default'] instanceof \Closure ? $prop['default']($this->data) : $prop['default'];
    }

    /**
     * Set Data value
     *
     * @param array $data
     */
    public function set(string $key, $value)
    {
        $props = $this->getProps();

        if (array_key_exists($key, $props)) {
            // If prop type is defined - parse by type
            $this->data[$key] = $this->parseField($props[$key]['type'], $value);
        } else {
            // Else - assign as is
            $this->data[$key] = $value;
        }
    }

    /**
     * Set Many Data values
     *
     * @param array $data
     */
    public function setMany(array $data)
    {
        foreach ($data as $key => $value) {
            $this->set($key, $value);
        }
    }

    /**
     * Convert value as per the type
     *
     * @param string $type
     * @param mixed $value
     * @return mixed
     */
    protected function parseField($type, $value)
    {
        switch ($type) {
            case 'string':
                $value = trim($value);
                break;

            case 'int':
                $value = (int)$value;
                break;

            case 'bool':
                $value = (bool)$value;
                break;

            case 'array':
                $value = (array)$value;
                break;
        }

        return $value;
    }

    /**
     * Get Prop
     *
     * @return array
     */
    protected function getProp(string $key): array
    {
        $props = $this->getProps();

        return array_key_exists($key, $props) ? $props[$key] : [];
    }

    /**
     * Get props merged with defaults
     *
     * @return array
     */
    protected function getProps(): array
    {
        $props = $this->props();

        foreach ($props as $name => &$def) {
            $def = array_merge(
                [
                    'type' => 'string',
                    'required' => false,
                    'default' => null,
                ],
                $def
            );
        }

        return $props;
    }

    /**
     * Validate data before firing hooks
     *
     * @param array $data
     * @throws AppException
     */
    protected function validateData()
    {
        $data = $this->data;

        $props = $this->getProps();

        foreach ($props as $name => $fieldData) {
            // If a var is not set - assign default or stop
            if (!isset($data[$name])) {
                if ($fieldData['required']) {
                    // If required and not set - error
                    throw new AppException(sprintf('Missing required field "%s"', $name));
                } else {
                    // Else - assign default
                    $data[$name] = $fieldData['default'] instanceof \Closure ? $fieldData['default']($data) : $fieldData['default'];
                }
            }

            // Shorthand by reference
            $item =& $data[$name];

            // Check callable var
            if ($item && 'callable' === $fieldData['type'] && !is_callable($item)) {
                throw new AppException(sprintf('Field "%s" is not callable', $name));
            }

            // Sanitize by type
            $item = $this->parseField($fieldData['type'], $item);
        }

        // Assign data
        $this->data = $data;

        return $data;
    }

    /**
     * Add Hook
     *
     * @param string $tag
     * @param callable $callback
     * @param int $priority
     * @return Module
     * @throws AppException
     */
    protected function hook(string $tag, callable $callback, int $priority = 10)
    {
        return App::get(
            'hook',
            [
                'tag' => $tag,
                'callback' => function () use ($callback) {
                    // Validate Data before calling
                    $this->validateData();

                    // Call the callback
                    return $callback(...func_get_args());
                },
                'priority' => $priority,
            ]
        );
    }

    /**
     * Render Twig Template
     *
     * @param string $name Template file name without .twig.
     * @param array $args Args to be passed to the Template. Default [].
     * @return string Rendered Template
     */
    protected function twig($name, $args = []): string
    {
        return App::get('twig')->renderFile($name, $args);
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
        App::get('logger')->log($message, $values, $level);
    }

    abstract protected function props(): array;
}
