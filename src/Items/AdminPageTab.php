<?php

namespace AlexDashkin\Adwpfw\Items;

use AlexDashkin\Adwpfw\App;
use AlexDashkin\Adwpfw\Fields\Text;

/**
 * Menu Page Tab
 */
class AdminPageTab extends Item
{
    /**
     * @var FormField[]
     */
    private $fields;

    /**
     * @var FormField[]
     */
    private $buttons;

    /**
     * Constructor
     *
     * @param array $data {
     * @type string $title
     * @type bool $form Whether to wrap content with the <form> tag
     * @type array $options Tab fields
     * @type array $buttons Buttons at the bottom of the Tab
     * }
     */
    public function __construct(array $data, App $app)
    {
        $this->props = [
            'title' => [
                'default' => 'Tab',
            ],
            'form' => [
                'type' => 'bool',
                'default' => false,
            ],
            'fields' => [
                'type' => 'array',
                'def' => [
                    'id' => 'field',
                    'type' => 'text',
                    'name' => 'Field',
                ],
            ],
        ];

        parent::__construct($data, $app);

        foreach ($this->data['fields'] as $field) {

            switch ($field['type']) {
                case 'text':
                    $this->fields[] = new Text($field, $app);
                    break;
            }

        }
    }

    public function getArgs(array $values)
    {
        $fields = $buttons = [];

        foreach ($this->fields as $field) {
            $fields[] = $field->getArgs($values);
        }

        foreach ($this->buttons as $button) {
            $buttons[] = $button->getArgs($values);
        }

        $args = [
            'form' => $this->data['form'],
            'fields' => $fields,
            'buttons' => $buttons,
        ];

        return $args;
    }
}
