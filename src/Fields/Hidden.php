<?php

namespace AlexDashkin\Adwpfw\Fields;

use AlexDashkin\Adwpfw\App;
use AlexDashkin\Adwpfw\Items\FormField;

/**
 * Form Field
 */
class Hidden extends FormField
{
    /**
     * Constructor
     *
     * @param array $data {
     * @type string $id Required.
     * }
     */
    public function __construct(array $data, App $app)
    {
        $this->tpl = 'hidden';

        $this->props = [
            'id' => [
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
            'value' => isset($values[$this->data['id']]) ? $values[$this->data['id']] : null,
        ];
    }
}
