<?php

namespace AlexDashkin\Adwpfw\Fields;

/**
 * Form Field
 */
class Password extends Text
{
    /**
     * Constructor
     *
     * @param array $data {
     * @type string $id Required.
     * @type string $label Field Label. Required.
     * @type string $desc Field Description
     * }
     */
    public function __construct(array $data, array $props = [])
    {
        $defaults = [
            'tpl' => [
                'default' => 'password',
            ],
        ];

        parent::__construct($data, array_merge($props, $defaults));
    }
}
