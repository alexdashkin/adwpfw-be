<?php

namespace AlexDashkin\Adwpfw\Fields;

/**
 * Form Field
 */
class Hidden extends Text
{
    /**
     * Constructor
     *
     * @param array $data {
     * @type string $id Required.
     * }
     */
    public function __construct(array $data, array $props = [])
    {
        $defaults = [
            'tpl' => [
                'default' => 'hidden',
            ],
        ];

        parent::__construct($data, array_merge($props, $defaults));
    }
}
