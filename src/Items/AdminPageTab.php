<?php

namespace AlexDashkin\Adwpfw\Items;

use AlexDashkin\Adwpfw\App;

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
     * @type string $slug Defaults to prefixed sanitized Title. Used if $form is true.
     * @type string $option WP Option name where the values are stored. Required if $form is true.
     * @type array $fields Tab fields
     * @type array $buttons Buttons at the bottom of the Tab
     * }
     *
     * @throws \AlexDashkin\Adwpfw\Exceptions\AdwpfwException
     */
    public function __construct(array $data, App $app)
    {
        $this->props = [
            'title' => [
                'default' => 'Tab',
            ],
            'slug' => [
                'default' => $this->getDefaultSlug($data['title']),
            ],
            'form' => [
                'type' => 'bool',
                'default' => false,
            ],
            'option' => [
                'default' => null,
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
            $this->fields[] = FormField::getField($field, $app);
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
            'slug' => $this->data['slug'],
            'fields' => $fields,
            'buttons' => $buttons,
        ];

        return $args;
    }
}
