<?php

namespace AlexDashkin\Adwpfw\Fields;

/**
 * Textarea.
 */
class Textarea extends Text
{
    /**
     * Constructor.
     *
     * @param array $data {
     * @type string $id Required.
     * @type string $label Field Label. Required.
     * @type string $desc Field Description
     * }
     * @throws \AlexDashkin\Adwpfw\Exceptions\AdwpfwException
     */
    public function __construct(array $data, array $props = [])
    {
        $defaults = [
            'tpl' => [
                'default' => 'textarea',
            ],
        ];

        parent::__construct($data, array_merge($props, $defaults));
    }
}
