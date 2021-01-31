<?php

namespace AlexDashkin\Adwpfw\Modules;

use AlexDashkin\Adwpfw\Modules\Fields\Contexts\Option;
use AlexDashkin\Adwpfw\Modules\Fields\Field;

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
                    'action' => sprintf('save_%s', $this->getProp('slug')),
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
        $field->setProp('context', new Option($field, $this->main));

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

        $fields = Field::renderMany($this->fields);

        $args['fields'] = $this->main->render('templates/admin-page-tab-fields', ['fields' => $fields]);

        return $this->main->render('templates/admin-page-tab', $args);
    }

    /**
     * Save the posted data
     *
     * @param array $request
     * @return array
     */
    public function save(array $request): array
    {
        $form = $request['form'];

        if (empty($form[$this->prefix])) {
            return $this->main->returnError('Form is empty');
        }

        $values = $form[$this->prefix];

        Field::setMany($this->fields, $values);

        do_action('adwpfw_settings_saved', $this, $values);

        return $this->main->returnSuccess('Saved');
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
            'form' => false,
            'option' => 'settings',
        ];
    }
}
