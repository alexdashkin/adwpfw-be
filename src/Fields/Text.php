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
     * @type string $id Required.
     * @type string $layout Parent template to extend. Required.
     * @type string $form Form ID (slug). Used to distinguish multiple forms on one page. Required.
     * @type string $tpl Template name. Default 'text'.
     * @type string $class CSS Class(es) for the control. Default 'adwpfw-form-control'.
     * @type string $label Label.
     * @type string $desc Description.
     * @type string $placeholder Placeholder. Default empty.
     * }
     * @throws \AlexDashkin\Adwpfw\Exceptions\AdwpfwException
     */
    public function __construct(array $data, array $props = [])
    {
        $defaults = [
            'tpl' => [
                'default' => 'text',
            ],
            'placeholder' => [
                'default' => null,
            ],
            'class' => [
                'default' => 'adwpfw-form-control',
            ],
        ];

        parent::__construct($data, array_merge($defaults, $props));
    }

    public function sanitize($value)
    {
        return sanitize_text_field($value);
    }
}
