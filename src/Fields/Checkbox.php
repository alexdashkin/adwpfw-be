<?php

namespace AlexDashkin\Adwpfw\Fields;

/**
 * Form Field
 */
class Checkbox extends Field
{
    /**
     * Constructor
     *
     * @param array $data {
     * @type string $id Required.
     * @type string $label Required.
     * @type string $desc Description
     * }
     */
    public function __construct(array $data, array $props = [])
    {
        $defaults = [
            'tpl' => [
                'default' => 'checkbox',
            ],
            'label' => [
                'required' => true,
            ],
        ];

        parent::__construct($data, array_merge($props, $defaults));
    }
}
