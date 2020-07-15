<?php

namespace AlexDashkin\Adwpfw\Abstracts;

use AlexDashkin\Adwpfw\App;
use AlexDashkin\Adwpfw\Exceptions\AppException;

abstract class Module
{
    /**
     * @var array Item Props
     */
    private $props = [];

    /**
     * Get Prop
     *
     * @param string $key If omitted, all props will be returned
     * @return mixed
     */
    public function gp(string $key = '')
    {
        if ($key) {
            return $this->getProp($key);
        }

        $return = [];

        $allProps = array_merge(array_keys($this->getPropDefs()), array_keys($this->props));

        foreach ($allProps as $propName) {
            $return[$propName] = $this->getProp($propName);
        }

        return $return;
    }

    /**
     * Set Prop
     *
     * @param string $key
     * @param mixed $value
     */
    public function sp(string $key, $value)
    {
        // Get prop definition
        $prop = $this->getPropDef($key);

        // If prop type is defined - parse by type, else - assign as is
        $this->props[$key] = $prop ? $this->parsePropByType($value, $prop['type']) : $value;
    }

    /**
     * Set Many Props
     *
     * @param array $data
     */
    public function spm(array $data)
    {
        foreach ($data as $key => $value) {
            $this->sp($key, $value);
        }
    }

    /**
     * Get Single Prop
     *
     * @param string $key
     * @return mixed
     */
    private function getProp($key)
    {
        // Get Prop Definition if exists
        $def = $this->getPropDef($key);

        // If no value yet, fill with the default
        if (!array_key_exists($key, $this->props)) {
            return $def ? ($def['default'] instanceof \Closure ? $def['default']($this->props) : $def['default']) : null;
        }

        // Parse value by type before returning
        return $def ? $this->parsePropByType($this->props[$key], $def['type']) : $this->props[$key];
    }

    /**
     * Convert value as per the type
     *
     * @param string $type
     * @param mixed $value
     * @return mixed
     */
    private function parsePropByType($value, $type)
    {
        switch ($type) {
            case 'string':
                return trim($value);

            case 'int':
                return (int)$value;

            case 'bool':
                return (bool)$value;

            case 'array':
                return (array)$value;
        }

        return $value;
    }

    /**
     * Get Prop
     *
     * @return array
     */
    private function getPropDef(string $key): array
    {
        $props = $this->getPropDefs();

        return array_key_exists($key, $props) ? $props[$key] : [];
    }

    /**
     * Get props merged with defaults
     *
     * @return array
     */
    private function getPropDefs(): array
    {
        $props = $this->getInitialPropDefs();

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
        $data = $this->props;

        $props = $this->getPropDefs();

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
            $item = $this->parsePropByType($item, $fieldData['type']);
        }

        // Assign data
        $this->props = $data;

        return $data;
    }

    /**
     * Add Hook with props validation in the middle
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

    abstract protected function getInitialPropDefs(): array;
}
