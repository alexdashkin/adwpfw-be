<?php

namespace AlexDashkin\Adwpfw\Items;

use AlexDashkin\Adwpfw\Abstracts\Module;
use AlexDashkin\Adwpfw\App;
use AlexDashkin\Adwpfw\Fields\Field;

class AdminPageTab extends Module
{
    /**
     * @var Field[]
     */
    protected $fields = [];

    /**
     * Init Module
     */
    public function init()
    {
        if ($this->get('form')) {
            App::get(
                'admin_ajax',
                [
                    'prefix' => $this->get('prefix'),
                    'action' => 'save',
                    'fields' => [
                        'form' => [
                            'type' => 'form',
                            'required' => true,
                        ],
                    ],
                    'callback' => [$this, 'save'],
                ]
            );
        }
    }

    /**
     * Add Field
     *
     * @param Field $field
     */
    public function addField(Field $field)
    {
        $field->setMany(['form' => $this->get('slug')]);

        $this->fields[] = $field;
    }

    /**
     * Get Twig args
     *
     * @return array
     */
    public function getTwigArgs(): array
    {
        $this->validateData();

        $values = get_option($this->get('prefix') . '_' . $this->get('option')) ?: [];

        $fields = [];

        foreach ($this->fields as $field) {
            $fieldName = $field->get('name');

            $twigArgs = $field->getTwigArgs($values[$fieldName] ?? null);

            $fields[] = $twigArgs;
        }

        return [
            'form' => $this->get('form'),
            'title' => $this->get('title'),
            'fields' => $fields,
            'buttons' => [],
        ];
    }

    /**
     * Save the posted data
     *
     * @param array $request
     */
    public function save(array $request)
    {
        $helpers = App::get('helpers');
        $postedData = $request['form'];

        if (empty($postedData[$this->get('prefix')][$this->get('slug')])) {
            return $helpers->returnError('Form is empty');
        }

        $form = $postedData[$this->get('prefix')][$this->get('slug')];

        $optionName = $this->get('prefix') . '_' . $this->get('option');

        $values = [];

        foreach ($this->fields as $field) {
            $fieldName = $field->get('name');

            if (empty($fieldName) || !array_key_exists($fieldName, $form)) {
                continue;
            }

            $values[$fieldName] = $field->sanitize($form[$fieldName]);
        }

        update_option($optionName, $values);

        do_action('adwpfw_settings_saved', $this, $values);

        return $helpers->returnSuccess('Saved');
    }

    /**
     * Get Class props
     *
     * @return array
     */
    protected function props(): array
    {
        return [
            'prefix' => [
                'required' => true,
            ],
            'title' => [
                'required' => true,
            ],
            'slug' => [
                'default' => function ($data) {
                    return sanitize_key(str_replace(' ', '-', $data['title']));
                },
            ],
            'form' => [
                'type' => 'bool',
                'default' => false,
            ],
            'option' => [
                'default' => 'settings',
            ],
        ];
    }

}
