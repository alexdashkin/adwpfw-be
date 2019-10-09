<?php

namespace AlexDashkin\Adwpfw\Fields;

use AlexDashkin\Adwpfw\App;
use AlexDashkin\Adwpfw\Items\FormField;

/**
 * Admin Page Field
 */
class Text extends FormField
{
    /**
     * Constructor
     *
     * @param array $data {
     * @type string $id Required.
     * @type string $name Field Label. Required.
     * @type string $desc Field Description
     * }
     */
    public function __construct(array $data, App $app)
    {
        $this->tpl = 'text';

        $this->props = [
            'id' => [
                'required' => true,
            ],
            'name' => [
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
            'name' => $this->data['name'],
            'desc' => $this->data['desc'],
            'value' => isset($values[$this->data['id']]) ? $values[$this->data['id']] : null,
        ];
    }
}
