<?php

namespace AlexDashkin\Adwpfw\Items;

use AlexDashkin\Adwpfw\App;
use AlexDashkin\Adwpfw\Exceptions\AdwpfwException;
use AlexDashkin\Adwpfw\Fields\Field;

/**
 * Admin Page Tab.
 */
class AdminPageTab extends ItemWithItems
{
    /**
     * @var Field[]
     */
    protected $items = [];

    /**
     * Constructor.
     *
     * @param App $app
     * @param array $data {
     * @type string $id Defaults to sanitized $title.
     * @type string $title Tab title. Required.
     * @type bool $form Whether to wrap content with the <form> tag and add 'Save changes' button. Default false.
     * @type string $option WP Option name where the values are stored. Default 'settings'.
     * @type array $fields Tab fields
     * }
     *
     * @throws AdwpfwException
     */
    public function __construct(App $app, array $data)
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
                'default' => 'settings',
            ],
            'fields' => [
                'type' => 'array',
                'def' => [
                    'id' => 'field',
                    'type' => 'text',
                    'name' => 'Field',
                    'default' => null,
                ],
            ],
        ];

        parent::__construct($app, $data, $props);

        foreach ($this->data['fields'] as $field) {
            $field['layout'] = 'admin-page-field';
            $field['form'] = $this->data['id'];
            $this->add($field);
        }
    }

    /**
     * Add Field.
     *
     * @param App $app
     * @param array $data Data passed to the Field Constructor.
     *
     * @throws AdwpfwException
     */
    public function add(array $data)
    {
        $this->items[] = Field::getField($this->app, $data);
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
