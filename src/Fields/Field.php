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
                'type' => 'string',
                'required' => true,
            ],
            'form' => [
                'type' => 'string',
                'required' => true,
            ],
            'tpl' => [
                'type' => 'string',
                'required' => true,
            ],
            'class' => [
                'type' => 'string',
                'default' => null,
            ],
            'required' => [
                'type' => 'bool',
                'default' => false,
            ],
            'label' => [
                'type' => 'string',
                'default' => null,
            ],
            'desc' => [
                'type' => 'string',
                'default' => null,
            ],
            'default' => [
                'default' => null,
            ],
            'callback' => [
                'type' => 'callable',
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

        if ($this->data['callback'] && is_callable($this->data['callback'])) {
            $this->data = array_merge($this->data, $this->data['callback']($values));
        }

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
