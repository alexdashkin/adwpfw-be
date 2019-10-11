<?php

namespace AlexDashkin\Adwpfw\Items\WithItems;

use AlexDashkin\Adwpfw\App;
use AlexDashkin\Adwpfw\Fields\Field;

/**
 * Menu Page Tab
 */
class AdminPageTab extends ItemWithItems
{
    /**
     * Constructor
     *
     * @param array $data {
     * @type string $slug Defaults to sanitized $title.
     * @type string $title Tab title. Required.
     * @type bool $form Whether to wrap content with the <form> tag and add 'Save changes' button. Default false.
     * @type string $option WP Option name where the values are stored. Required if $form is true.
     * @type array $fields Tab fields
     * }
     *
     * @throws \AlexDashkin\Adwpfw\Exceptions\AdwpfwException
     */
    public function __construct(array $data, App $app)
    {
        $props = [
            'id' => [
                'default' => $this->getDefaultId($data['title']),
            ],
            'title' => [
                'required' => true,
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

        parent::__construct($data, $app, $props);

        foreach ($this->data['fields'] as $field) {
            $field['layout'] = 'admin-page-field';
            $field['form'] = $this->data['id'];
            $this->add($field, $app);
        }
    }

    /**
     * Add Field.
     *
     * @param array $data Data passed to the Field Constructor.
     * @param App $app
     *
     * @throws \AlexDashkin\Adwpfw\Exceptions\AdwpfwException
     */
    public function add(array $data)
    {
        $this->items[] = Field::getField($data, $this->app);
    }

    /**
     * Get Twig args.
     *
     * @return array
     */
    public function getArgs()
    {
        $values = get_option($this->prefix . '_' . $this->data['option']) ?: [];

        $fields = $buttons = [];

        foreach ($this->items as $field) {
            $fields[] = $field->getArgs($values);
        }

        $args = [
            'form' => $this->data['form'],
            'title' => $this->data['title'],
            'fields' => $fields,
            'buttons' => $buttons,
        ];

        return $args;
    }

    /**
     * Save the posted data.
     *
     * @param array $data Posted data
     */
    public function save($data)
    {
        if (empty($data[$this->data['id']])) {
            return;
        }

        $form = $data[$this->data['id']];

        $optionName = $this->prefix . '_' . $this->data['option'];

        $values = get_option($this->prefix . '_' . $this->data['option']) ?: [];

        foreach ($this->items as $field) {

            if (empty($field->data['id']) || !array_key_exists($field->data['id'], $form)) {
                continue;
            }

            $fieldId = $field->data['id'];

            $values[$fieldId] = $field->sanitize($form[$fieldId]);
        }

        update_option($optionName, $values);

        do_action('adwpfw_settings_saved', $this, $values);
    }
}
