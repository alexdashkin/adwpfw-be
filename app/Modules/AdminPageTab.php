<?php

namespace AlexDashkin\Adwpfw\Modules;

/**
 * title*, slug, form, option
 */
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
        if ($this->getProp('form')) {
            $this->m(
                'api.ajax',
                [
                    'prefix' => $this->prefix,
                    'action' => sprintf('save_%s_%s', $this->getProp('parent_slug'), $this->getProp('slug')),
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
        $field->setProp('form', $this->getProp('slug'));

        $this->fields[] = $field;
    }

    /**
     * Render Tab
     *
     * @return string
     */
    public function render(): string
    {
        $args = $this->getProps();
        $values = get_option($this->prefix . '_' . $this->getProp('option')) ?: [];
        $args['fields'] = Field::getArgsForMany($this->fields, $values);

        return $this->app->main->render('templates/admin-page-tab', $args);
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
        $prefix = $this->prefix;
        $slug = $this->getProp('slug');

        if (empty($form[$prefix][$slug])) {
            return $main->returnError('Form is empty');
        }

        $data = $form[$prefix][$slug];

        $optionName = $this->prefix . '_' . $this->getProp('option');

        $values = [];

        foreach ($this->fields as $field) {
            $fieldName = $field->getProp('name');

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
     * Get Default prop values
     *
     * @return array
     */
    protected function defaults(): array
    {
        return [
            'title' => 'Tab',
            'slug' => function () {
                return sanitize_key(str_replace(' ', '-', $this->getProp('title')));
            },
            'option' => 'settings',
        ];
    }
}
