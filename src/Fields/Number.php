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
     * @type string $label Label.
     * @type string $desc Description.
     * @type int $min Min attr
     * @type int $max Max attr
     * @type int $step Step attr
     * }
     * @param array $props
     *
     * @throws AdwpfwException
     */
    public function __construct(App $app, array $data, array $props = [])
    {
        $defaults = [
            'tpl' => [
                'default' => 'number',
            ],
            'class' => [
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

    public function sanitize($value)
    {
        return (int)$value;
    }
}
