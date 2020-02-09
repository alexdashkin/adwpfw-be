<?php

namespace AlexDashkin\Adwpfw\Fields;

use AlexDashkin\Adwpfw\App;
use AlexDashkin\Adwpfw\Exceptions\AdwpfwException;

/**
 * Number (integer).
 */
class Number extends Field
{
    /**
     * Constructor.
     *
     * @param App $app
     * @param array $data {
     * @type string $id Required.
     * @type string $layout Parent template to extend. Required.
     * @type string $form Form ID (slug). Used to distinguish multiple forms on one page. Required.
     * @type string $tpl Template name. Default 'number'.
     * @type string $class CSS Class(es) for the control. Default 'adwpfw-form-control'.
     * @type string $label Label. Default empty.
     * @type string $desc Description. Default empty.
     * @type mixed $default Default value. Default empty.
     * @type callable $callback Filters Twig args, gets $value, returns fields[] as key/value pairs. Default empty.
     * @type int $min Min attr. Default 0.
     * @type int $max Max attr. Default 1000000000.
     * @type int $step Step attr. Default 1.
     * }
     * @param array $props
     *
     * @throws AdwpfwException
     */
    public function __construct(App $app, array $data, array $props = [])
    {
        $defaults = [
            'tpl' => [
                'type' => 'string',
                'default' => 'number',
            ],
            'class' => [
                'type' => 'string',
                'default' => 'adwpfw-form-control',
            ],
            'min' => [
                'type' => 'int',
                'default' => 0,
            ],
            'max' => [
                'type' => 'int',
                'default' => 1000000000,
            ],
            'step' => [
                'type' => 'int',
                'default' => 1,
            ],
        ];

        parent::__construct($app, $data, array_merge($defaults, $props));
    }

    /**
     * Sanitize value.
     *
     * @param mixed $value
     * @return int
     */
    public function sanitize($value)
    {
        return (int)$value;
    }
}
