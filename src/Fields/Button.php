<?php

namespace AlexDashkin\Adwpfw\Fields;

use AlexDashkin\Adwpfw\App;
use AlexDashkin\Adwpfw\Items\FormField;

/**
 * Form Field
 */
class Button extends FormField
{
    /**
     * Constructor
     *
     * @param array $data {
     * @type string $id Required.
     * @type string $caption Button Caption. Required.
     * @type string $desc Button Description
     * }
     */
    public function __construct(array $data, App $app)
    {
        $this->tpl = 'button';

        $this->props = [
            'id' => [
                'required' => true,
            ],
            'caption' => [
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
            'caption' => $this->data['cation'],
            'desc' => $this->data['desc'],
            'value' => isset($values[$this->data['id']]) ? $values[$this->data['id']] : null,
        ];
    }
}
