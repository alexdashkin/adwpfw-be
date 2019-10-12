<?php

namespace AlexDashkin\Adwpfw\Fields;

use AlexDashkin\Adwpfw\Modules\Basic\Helpers;

/**
 * Select2 Field.
 */
class Select2 extends Select
{
    /**
     * Constructor
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
            'options' => [
                'type' => 'array',
                'default' => [],
            ],
            'ajax_action' => [
                'required' => true,
            ],
            'label_cb' => [
                'type' => 'callable',
                'default' => null,
            ],
            'tpl' => [
                'default' => 'select2',
            ],
        ];

        parent::__construct($data, array_merge($defaults, $props));
    }

    public function getArgs(array $values)
    {
        $data = $this->data;

        $args = parent::getArgs($values);

        $value = isset($values[$data['id']]) ? $values[$data['id']] : null;

        $valueArr = $data['multiple'] ? (array)$value : [$value];

        foreach ($valueArr as $item) {
            if (!Helpers::arraySearch($args['options'], ['value' => $item])) {
                $args['options'][] = [
                    'label' => !empty($data['label_cb']) ? $data['label_cb']($item) : $item,
                    'value' => $item,
                    'selected' => 'selected',
                ];
            }
        }

        return $args;
    }
}
