<?php

namespace AlexDashkin\Adwpfw\Fields;

use AlexDashkin\Adwpfw\App;
use AlexDashkin\Adwpfw\Items\FormField;

/**
 * Form Field
 */
class Heading extends FormField
{
    /**
     * Constructor
     *
     * @param array $data {
     * @type string $text Heading. Required.
     * }
     */
    public function __construct(array $data, App $app)
    {
        $this->tpl = 'heading';

        $props = [
            'text' => [
                'required' => true,
            ],
        ];

        parent::__construct($data, $app, $props);
    }

    public function getArgs(array $values)
    {
        return [
            'tpl' => $this->tpl,
            'text' => $this->data['text'],
        ];
    }
}
