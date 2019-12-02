<?php

namespace AlexDashkin\Adwpfw\Fields;

use AlexDashkin\Adwpfw\App;
use AlexDashkin\Adwpfw\Exceptions\AdwpfwException;

/**
 * Checkbox.
 */
class Checkbox extends Field
{
    /**
     * Constructor
     *
     * @param App $app
     * @param array $data {
     * @type string $id Required.
     * @type string $layout Parent template to extend. Required.
     * @type string $form Form ID (slug). Used to distinguish multiple forms on one page. Required.
     * @type string $tpl Template name. Default 'checkbox'.
     * @type string $class CSS Class(es) for the control. Default empty.
     * @type string $label Label. Required.
     * @type string $desc Description. Default empty.
     * }
     * @param array $props
     *
     * @throws AdwpfwException
     */
    public function __construct(App $app, array $data, array $props = [])
    {
        $defaults = [
            'label' => [
                'required' => true,
            ],
            'tpl' => [
                'default' => 'checkbox',
            ],
        ];

        parent::__construct($app, $data, array_merge($defaults, $props));
    }

    /**
     * Sanitize value.
     *
     * @param mixed $value
     * @return string
     */
    public function sanitize($value)
    {
        return sanitize_text_field($value);
    }
}
