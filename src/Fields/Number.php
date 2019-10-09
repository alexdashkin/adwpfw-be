<?php

namespace AlexDashkin\Adwpfw\Fields;

use AlexDashkin\Adwpfw\App;
use AlexDashkin\Adwpfw\Items\FormField;

/**
 * Form Field
 */
class Number extends FormField
{
    /**
     * Constructor
     *
     * @param array $data {
     * @type string $id Required.
     * @type string $label Field Label. Required.
     * @type string $desc Field Description
     * @type int $min Min attr
     * @type int $max Max attr
     * @type int $step Step attr
     * }
     */
    public function __construct(array $data, App $app)
    {
        $this->tpl = 'number';

        $this->props = [
            'id' => [
                'required' => true,
            ],
            'label' => [
                'required' => true,
            ],
            'desc' => [
                'default' => null,
            ],
            'min' => [
                'type' => 'int',
                'default' => 0,
            ],
            'max' => [
                'type' => 'int',
                'default' => 0,
            ],
            'step' => [
                'type' => 'int',
                'default' => 0,
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
            'min' => $this->data['min'],
            'max' => $this->data['max'],
            'step' => $this->data['step'],
            'value' => isset($values[$this->data['id']]) ? $values[$this->data['id']] : null,
        ];
    }
}
