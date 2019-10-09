<?php

namespace AlexDashkin\Adwpfw\Fields;

use AlexDashkin\Adwpfw\App;
use AlexDashkin\Adwpfw\Items\FormField;
use AlexDashkin\Adwpfw\Modules\Helpers;

/**
 * Form Field
 */
class Select2 extends FormField
{
    /**
     * Constructor
     *
     * @param array $data {
     * @type string $id Required.
     * @type string $label Field Label. Required.
     * @type string $desc Field Description
     * @type array $options Options. Required.
     * @type bool $multiple Default false
     * }
     */
    public function __construct(array $data, App $app)
    {
        $this->tpl = 'select2';

        $props = [
            'id' => [
                'required' => true,
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
            'desc' => [
                'default' => null,
            ],
            'multiple' => [
                'type' => 'bool',
                'default' => false,
            ],
            'ajax_action' => [
                'required' => true,
            ],
            'label_cd' => [
                'type' => 'callable',
                'required' => true,
            ],
        ];

        parent::__construct($data, $app, $props);
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

        $valueArr = $data['multiple'] ? (array)$value : [$value];

        foreach ($valueArr as $item) {
            if (!Helpers::arraySearch($options, ['value' => $item])) {
                $options[] = [
                    'label' => !empty($option['label_cb']) ? $option['label_cb']($item) : $item,
                    'value' => $item,
                    'selected' => 'selected',
                ];
            }
        }

        return [
            'tpl' => $this->tpl,
            'id' => $data['id'],
            'label' => $data['label'],
            'desc' => $data['desc'],
            'multiple' => $data['multiple'],
            'options' => $options,
        ];
    }
}
