<?php

namespace AlexDashkin\Adwpfw\Fields;

use AlexDashkin\Adwpfw\App;
use AlexDashkin\Adwpfw\Items\FormField;

/**
 * Form Field
 */
class Text extends FormField
{
    /**
     * Constructor
     *
     * @param array $data {
     * @type string $layout Parent template to extend. Required.
     * @type string $id Required.
     * @type string $label Field Label. Required.
     * @type string $desc Field Description
     * }
     */
    public function __construct(array $data, App $app)
    {
        $props = [
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

        parent::__construct($data, $app, $props);
    }

    public function getArgs(array $values)
    {
        return [
            'tpl' => 'text',
            'id' => $this->data['id'],
            'layout' => $this->data['layout'],
            'label' => $this->data['label'],
            'desc' => $this->data['desc'],
            'value' => isset($values[$this->data['id']]) ? $values[$this->data['id']] : null,
        ];
    }
}
