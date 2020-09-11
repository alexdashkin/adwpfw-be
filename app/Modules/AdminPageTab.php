<?php

namespace AlexDashkin\Adwpfw\Modules;

use AlexDashkin\Adwpfw\Fields\Field;

class AdminPageTab extends Module
{
    /**
     * @var Field[]
     */
    protected $fields = [];

    /**
     * Add Field
     *
     * @param Field $field
     */
    public function addField(Field $field)
    {
        $field->setProps(['form' => $this->getProp('slug')]);

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

        $values = get_option($this->config('prefix') . '_' . $this->getProp('option')) ?: [];

        $fields = [];

        foreach ($this->fields as $field) {
            $fieldName = $field->gp('name');

            $twigArgs = $field->getTwigArgs($values[$fieldName] ?? null);

            $fields[] = $twigArgs;
        }

        return [
            'form' => $this->getProp('form'),
            'title' => $this->getProp('title'),
            'fields' => $fields,
            'buttons' => [],
        ];
    }

    /**
     * Save the posted data
     *
     * @param array $postedData
     * @return bool
     */
    public function save(array $postedData): bool
    {
        if (empty($postedData[$this->getProp('slug')])) {
            return false;
        }

        $data = $postedData[$this->getProp('slug')];

        $optionName = $this->config('prefix') . '_' . $this->getProp('option');

        $values = [];

        foreach ($this->fields as $field) {
            $fieldName = $field->gp('name');

            if (empty($fieldName) || !array_key_exists($fieldName, $data)) {
                continue;
            }

            $values[$fieldName] = $field->sanitize($data[$fieldName]);
        }

        update_option($optionName, $values);

        do_action('adwpfw_settings_saved', $this, $values);

        return true;
    }

    /**
     * Get Class props
     *
     * @return array
     */
    protected function getInitialPropDefs(): array
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
