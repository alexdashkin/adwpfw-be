<?php

namespace AlexDashkin\Adwpfw\Fields;

use AlexDashkin\Adwpfw\App;
use AlexDashkin\Adwpfw\Exceptions\AdwpfwException;
use AlexDashkin\Adwpfw\Traits\ItemTrait;

/**
 * Form Field
 */
abstract class Field
{
    use ItemTrait;

    /**
     * @param array $data Field Data
     * @param App $app
     * @return Field
     * @throws AdwpfwException
     */
    public static function getField($data, App $app)
    {
        $class = 'AlexDashkin\\Adwpfw\\Fields\\' . ucfirst($data['type']);

        if (!class_exists($class)) {
            throw new AdwpfwException(sprintf('Field "%s" not found', $data['type']));
        }

        return new $class($data, $app);
    }

    /**
     * Constructor
     */
    public function __construct(array $data, array $props = [])
    {
        $defaults = [
            'id' => [
                'required' => true,
            ],
            'tpl' => [
                'required' => true,
            ],
            'layout' => [
                'required' => true,
            ],
            'desc' => [
                'default' => null,
            ],
        ];

        $this->props = array_merge($props, $defaults);

        $this->data = $this->validate($data);
    }

    public function getArgs(array $values)
    {
        $this->data['value'] = isset($values[$this->data['id']]) ? $values[$this->data['id']] : null;

        return $this->data;
    }
}
