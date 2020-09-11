<?php

namespace AlexDashkin\Adwpfw\Modules;

use AlexDashkin\Adwpfw\Fields\Field;

class AdminPageTab extends Module
{
    /**
     * @var AdminPage
     */
    protected $parent;

    /**
     * @var Field[]
     */
    protected $fields = [];

    /**
     * Init Module
     */
    public function init()
    {
        $this->addHook('admin_menu', [$this, 'register']);

        if ($this->getProp('form')) {
            $this->m(
                'admin_ajax',
                [
                    'prefix' => $this->config('prefix'),
                    'action' => sprintf('save_%s_%s', $this->parent->getProp('slug'), $this->getProp('slug')),
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
     * Set Parent Page
     *
     * @param AdminPage $parent
     */
    public function setParent(AdminPage $parent)
    {
        $this->parent = $parent;
    }

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
     * @param array $request
     * @return array
     */
    public function save(array $request): array
    {
        $main = $this->m('main');
        $form = $request['form'];
        $prefix = $this->config('prefix');
        $slug = $this->getProp('slug');

        if (empty($form[$prefix][$slug])) {
            return $main->returnError('Form is empty');
        }

        $data = $form[$prefix][$slug];

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

        return $main->returnSuccess('Saved');
    }

    /**
     * Get Default Prop value
     *
     * @param string $key
     * @return mixed
     */
    protected function getDefault(string $key)
    {
        switch ($key) {
            case 'slug':
                return sanitize_key(str_replace(' ', '-', $this->getProp('title')));
            case 'form':
                return false;
            case 'option':
                return 'settings';
        }

        return null;
    }
}
