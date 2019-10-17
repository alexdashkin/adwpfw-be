<?php

namespace AlexDashkin\Adwpfw\Fields;

/**
 * Text Field.
 */
class Text extends Field
{
    /**
     * Constructor.
     *
     * @param array $data {
     * @type string $layout Parent template to extend. Required.
     * @type string $id Required.
     * @type string $label Field Label. Required.
     * @type string $placeholder Placeholder.
     * @type string $desc Field Description.
     * @type string $class CSS Class(es) for the control.
     * }
     * @throws \AlexDashkin\Adwpfw\Exceptions\AdwpfwException
     */
    public function __construct(array $data, array $props = [])
    {
        $defaults = [
            'placeholder' => [
                'default' => null,
            ],
            'tpl' => [
                'default' => 'text',
            ],
        ];

        parent::__construct($data, array_merge($defaults, $props));
    }

    public function sanitize($value)
    {
        return sanitize_text_field($value);
    }
}
