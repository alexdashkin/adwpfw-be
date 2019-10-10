<?php

namespace AlexDashkin\Adwpfw\Items;

use AlexDashkin\Adwpfw\App;
use AlexDashkin\Adwpfw\Exceptions\AdwpfwException;

/**
 * Form Field
 */
abstract class FormField extends Item
{
    /**
     * @param array $data Field Data
     * @param App $app
     * @return FormField
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
    public function __construct(array $data, App $app, array $props = [])
    {
        $own = [
            'layout' => [
                'required' => true,
            ],
        ];

        parent::__construct($data, $app, array_merge($props, $own));
    }

    abstract public function getArgs(array $values);
}
