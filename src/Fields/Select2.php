<?php

namespace AlexDashkin\Adwpfw\Fields;

use AlexDashkin\Adwpfw\App;
use AlexDashkin\Adwpfw\Items\FormField;

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

        $this->props = [
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

        parent::__construct($data, $app);
    }

    public function getArgs(array $values)
    {
        return [
            'tpl' => $this->tpl,
            'id' => $this->data['id'],
            'label' => $this->data['label'],
            'desc' => $this->data['desc'],
            'multiple' => $this->data['multiple'],
            'ajax_action' => $this->data['ajax_action'],
            'options' => $this->data['options'],
            'value' => isset($values[$this->data['id']]) ? $values[$this->data['id']] : null,
        ];
    }
}
