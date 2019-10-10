<?php

namespace AlexDashkin\Adwpfw\Fields;

/**
 * Form Field
 */
class Button extends Field
{
    /**
     * Constructor
     *
     * @param array $data {
     * @type string $id Required.
     * @type string $caption Button Caption. Required.
     * @type string $desc Button Description
     * }
     */
    public function __construct(array $data, array $props = [])
    {
        $defaults = [
            'tpl' => [
                'default' => 'button',
            ],
            'caption' => [
                'required' => true,
            ],
        ];

        parent::__construct($data, array_merge($props, $defaults));
    }
}
