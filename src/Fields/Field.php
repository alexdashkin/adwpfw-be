<?php

namespace AlexDashkin\Adwpfw\Fields;

use AlexDashkin\Adwpfw\Abstracts\BasicItem;
use AlexDashkin\Adwpfw\App;
use AlexDashkin\Adwpfw\Exceptions\AdwpfwException;

/**
 * Form Field to be extended.
 */
abstract class Field extends BasicItem
{
    /**
     * @param App $app
     * @param array $data Field Data
     * @return Field
     * @throws AdwpfwException
     */
    public static function getField(App $app, $data)
    {
        $class = __NAMESPACE__ . '\\' . ucfirst($data['type']);

        if (!class_exists($class)) {
            throw new AdwpfwException(sprintf('Field "%s" not found', $data['type']));
        }

        return new $class($app, $data);
    }

    /**
     * Constructor
     *
     * @param App $app
     * @param array $data
     * @param array $props
     *
     * @throws AdwpfwException
     */
    protected function __construct(App $app, array $data, array $props = [])
    {
        $defaults = [
            'layout' => [
                'required' => true,
            ],
            'form' => [
                'required' => true,
            ],
            'tpl' => [
                'required' => true,
            ],
            'class' => [
                'default' => null,
            ],
            'required' => [
                'default' => false,
            ],
            'label' => [
                'default' => null,
            ],
            'desc' => [
                'default' => null,
            ],
            'default' => [
                'default' => null,
            ],
        ];

        parent::__construct($app, $data, array_merge($defaults, $props));
    }

    /**
     * Get Twig args to render the Field.
     *
     * @param array $values
     * @return array
     */
    public function getArgs(array $values)
    {
        $this->data['value'] = isset($values[$this->data['id']]) ? $values[$this->data['id']] : $this->data['default'];

        return $this->data;
    }

    /**
     * Sanitize field value.
     *
     * @param mixed $value
     * @return mixed
     */
    public function sanitize($value)
    {
        return $value;
    }
}
