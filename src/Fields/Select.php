<?php

namespace AlexDashkin\Adwpfw\Fields;

use AlexDashkin\Adwpfw\App;
use AlexDashkin\Adwpfw\Exceptions\AdwpfwException;

/**
 * Select Field.
 */
class Select extends Field
{
    /**
     * Constructor.
     *
     * @param App $app
     * @param array $data {
     * @type string $id Required.
     * @type string $layout Parent template to extend. Required.
     * @type string $form Form ID (slug). Used to distinguish multiple forms on one page. Required.
     * @type string $tpl Template name. Default 'select'.
     * @type string $class CSS Class(es) for the control. Default 'adwpfw-form-control'.
     * @type string $label Label. Default empty.
     * @type string $desc Description. Default empty.
     * @type mixed $default Default value. Default empty.
     * @type callable $callback Filters Twig args, gets $value, returns fields[] as key/value pairs. Default empty.
     * @type string $placeholder Placeholder. Default '--- Select ---'.
     * @type array $options Options. Required.
     * @type bool $multiple Default false.
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
                'default' => 'select',
            ],
            'class' => [
                'type' => 'string',
                'default' => 'adwpfw-form-control',
            ],
            'placeholder' => [
                'type' => 'string',
                'default' => '--- Select ---',
            ],
            'options' => [
                'type' => 'array',
                'required' => true,
                'def' => [
                    'value' => '',
                    'label' => 'Option',
                ],
            ],
            'multiple' => [
                'type' => 'bool',
                'default' => false,
            ],
        ];

        parent::__construct($app, $data, array_merge($defaults, $props));
    }

    public function getArgs(array $values)
    {
        $data = $this->data;

        $value = isset($values[$data['id']]) ? $values[$data['id']] : null;

        $options = [
            [
                'label' => $data['placeholder'],
                'value' => '',
                'selected' => '',
            ],
        ];

        foreach ($data['options'] as $option) {
            $val = $option['value'];
            $label = $option['label'];

            $selected = $data['multiple'] ? in_array($val, (array)$value) : $val == $value;

            $options[] = [
                'label' => $label,
                'value' => $val,
                'selected' => $selected ? 'selected' : '',
            ];
        }

        $data['options'] = $options;

        return $data;
    }

    /**
     * Sanitize value.
     *
     * @param string|array $value
     * @return string|array
     */
    public function sanitize($value)
    {
        if (is_array($value)) {
            foreach ($value as &$item) {
                $item = sanitize_text_field($item);
            }

            return $value;
        }

        return sanitize_text_field($value);
    }
}
