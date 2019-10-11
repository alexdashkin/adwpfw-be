<?php

namespace AlexDashkin\Adwpfw\Fields;

/**
 * Checkbox.
 */
class Checkbox extends Field
{
    /**
     * Constructor
     *
     * @param array $data {
     * @type string $id Required.
     * @type string $label Required.
     * @type string $desc Description.
     * @type string $class CSS Class(es) for the control.
     * }
     * @throws \AlexDashkin\Adwpfw\Exceptions\AdwpfwException
     */
    public function __construct(array $data, array $props = [])
    {
        $defaults = [
            'label' => [
                'required' => true,
            ],
            'tpl' => [
                'default' => 'checkbox',
            ],
        ];

        parent::__construct($data, array_merge($defaults, $props));
    }

    public function sanitize($value)
    {
        return sanitize_text_field($value);
    }
}
