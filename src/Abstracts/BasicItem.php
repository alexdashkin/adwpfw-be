<?php

namespace AlexDashkin\Adwpfw\Abstracts;

use AlexDashkin\Adwpfw\App;
use AlexDashkin\Adwpfw\Exceptions\AdwpfwException;

/**
 * Basic Class
 */
abstract class BasicItem extends Basic
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
     * Constructor
     *
     * @param App $app
     * @param array $data
     * @param array $props
     *
     * @throws AdwpfwException
     */
    protected function __construct(App $app, array $data = [], array $props = [])
    {
        parent::__construct($app);

        $defaults = [
            'id' => [
                'required' => true,
            ],
        ];

        $this->props = array_merge($defaults, $props);

        $this->data = $this->validate($data);
    }

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
                    $exploded = explode('\\', get_class($this));
                    throw new AdwpfwException(sprintf('Prop "%s" is required for item "%s"', $name, array_pop($exploded)));
                } else {
                    $data[$name] = $field['default'];
                }
            }

            $item =& $data[$name];

            if ($item && 'callable' === $field['type'] && !is_callable($item)) {
                throw new AdwpfwException("Field $name is not callable");
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

    /**
     * Generate default ID for item based on the $base
     *
     * @param string $base Basic string to generate ID
     * @return string
     */
    protected function getDefaultId($base)
    {
        return uniqid(esc_attr(sanitize_key(str_replace(' ', '-', $base))) . '-');
    }
}