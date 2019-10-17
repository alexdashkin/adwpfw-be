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
     * @type string $layout Parent template to extend. Required.
     * @type string $form Form ID (slug). Used to distinguish multiple forms on one page. Required.
     * @type string $tpl Template name. Default 'select'.
     * @type string $class CSS Class(es) for the control. Default 'adwpfw-form-control'.
     * @type string $label Label.
     * @type string $desc Description.
     * @type string $placeholder Placeholder. Default '--- Select ---'.
     * @type array $options Options. Required.
     * @type bool $multiple Default false.
     * }
     * @throws \AlexDashkin\Adwpfw\Exceptions\AdwpfwException
     */
    public function __construct(array $data, array $props = [])
    {
        $defaults = [
            'tpl' => [
                'default' => 'select',
            ],
            'placeholder' => [
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
