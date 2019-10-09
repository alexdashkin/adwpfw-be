<?php

namespace AlexDashkin\Adwpfw\Fields;

use AlexDashkin\Adwpfw\App;
use AlexDashkin\Adwpfw\Items\FormField;

/**
 * Admin Page Field
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

        $this->props = [
            'text' => [
                'required' => true,
            ],
        ];

        parent::__construct($data, $app);
    }

    public function getArgs(array $values)
    {
        return [
            'tpl' => $this->tpl,
            'text' => $this->data['text'],
        ];
    }
}
