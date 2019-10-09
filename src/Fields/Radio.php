<?php

namespace AlexDashkin\Adwpfw\Fields;

use AlexDashkin\Adwpfw\App;
use AlexDashkin\Adwpfw\Items\FormField;

/**
 * Form Field
 */
class Radio extends FormField
{
    /**
     * Constructor
     *
     * @param array $data {
     * @type string $id Required.
     * @type string $label Field Label. Required.
     * @type string $desc Field Description
     * }
     */
    public function __construct(array $data, App $app)
    {
        $this->tpl = 'radio';

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
        ];

        parent::__construct($data, $app, $props);
    }

    public function getArgs(array $values)
    {
        return [
            'tpl' => $this->tpl,
            'id' => $this->data['id'],
            'label' => $this->data['label'],
            'desc' => $this->data['desc'],
            'options' => $this->data['options'],
            'value' => isset($values[$this->data['id']]) ? $values[$this->data['id']] : null,
        ];
    }
}
