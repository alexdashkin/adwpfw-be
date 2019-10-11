<?php

namespace AlexDashkin\Adwpfw\Fields;

/**
 * Radio Selector.
 */
class Radio extends Field
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
                'default' => 'radio',
            ],
            'label' => [
                'required' => true,
            ],
            'options' => [
                'type' => 'array',
                'required' => true,
                'def' => [
                    'value' => '',
                    'label' => 'Option',
                ],
            ],
        ];

        parent::__construct($data, array_merge($props, $defaults));
    }

    public function sanitize($value)
    {
        return sanitize_text_field($value);
    }
}
