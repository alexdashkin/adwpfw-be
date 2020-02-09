<?php

namespace AlexDashkin\Adwpfw\Fields;

use AlexDashkin\Adwpfw\App;
use AlexDashkin\Adwpfw\Exceptions\AdwpfwException;

/**
 * Text Field.
 */
class Text extends Field
{
    /**
     * Constructor.
     *
     * @param App $app
     * @param array $data {
     * @type string $id Required.
     * @type string $layout Parent template to extend. Required.
     * @type string $form Form ID (slug). Used to distinguish multiple forms on one page. Required.
     * @type string $tpl Template name. Default 'text'.
     * @type string $class CSS Class(es) for the control. Default 'adwpfw-form-control'.
     * @type string $label Label. Default empty.
     * @type string $desc Description. Default empty.
     * @type mixed $default Default value. Default empty.
     * @type callable $callback Filters Twig args, gets $value, returns fields[] as key/value pairs. Default empty.
     * @type string $placeholder Placeholder. Default empty.
     * }
     * @param array $props
     *
     * @throws AdwpfwException
     */
    public function __construct(App $app, array $data, array $props = [])
    {
        $defaults = [
            'tpl' => [
                'type' => 'string',
                'default' => 'text',
            ],
            'placeholder' => [
                'type' => 'string',
                'default' => null,
            ],
            'class' => [
                'type' => 'string',
                'default' => 'adwpfw-form-control',
            ],
        ];

        parent::__construct($app, $data, array_merge($defaults, $props));
    }

    /**
     * Sanitize value.
     *
     * @param string $value
     * @return string
     */
    public function sanitize($value)
    {
        return sanitize_text_field($value);
    }
}
