<?php

namespace AlexDashkin\Adwpfw\Fields;

/**
 * Form Field
 */
class Heading extends Field
{
    /**
     * Constructor
     *
     * @param array $data {
     * @type string $text Heading. Required.
     * }
     */
    public function __construct(array $data, array $props = [])
    {
        $defaults = [
            'tpl' => [
                'default' => 'heading',
            ],
            'text' => [
                'required' => true,
            ],
        ];

        parent::__construct($data, array_merge($props, $defaults));
    }
}
