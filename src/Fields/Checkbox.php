<?php

namespace AlexDashkin\Adwpfw\Fields;

use AlexDashkin\Adwpfw\App;
use AlexDashkin\Adwpfw\Items\FormField;

/**
 * Form Field
 */
class Checkbox extends FormField
{
    /**
     * Constructor
     *
     * @param array $data {
     * @type string $id Required.
     * @type string $label Required.
     * @type string $desc Description
     * }
     */
    public function __construct(array $data, App $app)
    {
        $this->tpl = 'checkbox';

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
            'value' => isset($values[$this->data['id']]) ? $values[$this->data['id']] : null,
        ];
    }
}