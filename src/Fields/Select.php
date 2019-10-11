<?php

namespace AlexDashkin\Adwpfw\Fields;

/**
 * Select Field.
 */
class Select extends Field
{
    /**
     * Constructor.
     *
     * @param array $data {
     * @type string $id Required.
     * @type string $label Field Label. Required.
     * @type string $desc Field Description
     * @type string $class CSS Class(es) for the control.
     * @type array $options Options. Required.
     * @type bool $multiple Default false
     * }
     * @throws \AlexDashkin\Adwpfw\Exceptions\AdwpfwException
     */
    public function __construct(array $data, array $props = [])
    {
        $defaults = [
            'tpl' => [
                'default' => 'select',
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
            'placeholder' => [
                'default' => '--- Select ---',
            ],
            'multiple' => [
                'type' => 'bool',
                'default' => false,
            ],
        ];

        parent::__construct($data, array_merge($defaults, $props));
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

        foreach ($data['options'] as $val => $label) {
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

    public function sanitize($value)
    {
        return sanitize_text_field($value);
    }
}
