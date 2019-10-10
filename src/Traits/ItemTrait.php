<?php

namespace AlexDashkin\Adwpfw\Traits;

use AlexDashkin\Adwpfw\Exceptions\AdwpfwException;

trait ItemTrait
{
    /**
     * @var array Item Data
     */
    public $data = [];

    /**
     * @var array Item Props
     */
    protected $props = [];

    /**
     * Validate data
     *
     * @param array $data Passed data
     * @return array Validated and Sanitized data
     *
     * @throws AdwpfwException
     */
    protected function validate($data)
    {
        foreach ($this->props as $name => $def) {
            $field = array_merge([
                'type' => 'string',
                'required' => false,
                'default' => null,
            ], $def);

            if (!isset($data[$name])) {
                if ($field['required']) {
                    throw new AdwpfwException("Field $name is required"); // todo
                } else {
                    $data[$name] = $field['default'];
                }
            }

            $item =& $data[$name];

            if ('callable' === $field['type'] && !is_callable($item)) {
                throw new AdwpfwException("Field $name is not callable"); // todo
            }

            switch ($field['type']) {
                case 'string':
                    $item = trim($item);
                    break;

                case 'int':
                    $item = (int)$item;
                    break;

                case 'bool':
                    $item = (bool)$item;
                    break;

                case 'array':
                    $item = (array)$item;

                    if (!empty($field['def'])) {
                        foreach ($item as &$subItem) {
                            $subItem = array_merge($field['def'], $subItem);
                        }
                    }

                    break;
            }
        }

        return $data;
    }
}