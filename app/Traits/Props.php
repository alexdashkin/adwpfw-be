<?php

namespace AlexDashkin\Adwpfw\Traits;

use AlexDashkin\Adwpfw\Exceptions\AppException;

trait Props
{
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
}
