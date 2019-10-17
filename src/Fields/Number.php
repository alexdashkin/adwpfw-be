<?php

namespace AlexDashkin\Adwpfw\Fields;

/**
 * Number (integer).
 */
class Number extends Field
{
    /**
     * Constructor.
     *
     * @param array $data {
     * @type string $id Required.
     * @type string $label Field Label. Required.
     * @type string $desc Field Description.
     * @type string $class CSS Class(es) for the control.
     * @type int $min Min attr
     * @type int $max Max attr
     * @type int $step Step attr
     * }
     * @throws \AlexDashkin\Adwpfw\Exceptions\AdwpfwException
     */
    public function __construct(array $data, array $props = [])
    {
        $defaults = [
            'min' => [
                'type' => 'int',
                'default' => 0,
            ],
            'max' => [
                'type' => 'int',
                'default' => 0,
            ],
            'step' => [
                'type' => 'int',
                'default' => 0,
            ],
            'tpl' => [
                'default' => 'number',
            ],
        ];

        parent::__construct($data, array_merge($defaults, $props));
    }

    public function sanitize($value)
    {
        return (int)$value;
    }
}
