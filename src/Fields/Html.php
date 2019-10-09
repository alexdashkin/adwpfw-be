<?php

namespace AlexDashkin\Adwpfw\Fields;

use AlexDashkin\Adwpfw\App;
use AlexDashkin\Adwpfw\Items\FormField;

/**
 * Form Field
 */
class Html extends FormField
{
    /**
     * Constructor
     *
     * @param array $data {
     * @type string $content Required.
     * }
     */
    public function __construct(array $data, App $app)
    {
        $this->tpl = 'html';

        $props = [
            'content' => [
                'required' => true,
            ],
        ];

        parent::__construct($data, $app, $props);
    }

    public function getArgs(array $values)
    {
        return [
            'tpl' => $this->tpl,
            'content' => $this->data['content'],
        ];
    }
}
