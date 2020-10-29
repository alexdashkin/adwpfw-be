<?php

namespace AlexDashkin\Adwpfw\Modules;

use AlexDashkin\Adwpfw\Modules\Fields\Field;

/**
 * id*, title
 */
class ProfileSection extends Module
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
        $field->setProp('context', 'user');

        $this->fields[] = $field;
    }

    /**
     * Init Module
     */
    public function init()
    {
        $this->addHook('show_user_profile', [$this, 'render']);
        $this->addHook('edit_user_profile', [$this, 'render']);
        $this->addHook('personal_options_update', [$this, 'save']);
        $this->addHook('edit_user_profile_update', [$this, 'save']);

        // Enqueue assets
        foreach ($this->getProp('assets') as $index => $asset) {

            // Type here is CSS/JS
            $type = $asset['type'] ?? 'css';

            // Type for particular asset is admin/front
            $asset['type'] = 'admin';

            $args = [
                'id' => sprintf('%s-%d', $this->getProp('id'), $index),
                'callback' => function () {
                    return get_current_screen()->id === 'user';
                },
            ];

            $this->m('asset.' . $type, array_merge($args, $asset));
        }
    }

    /**
     * Render Section
     *
     * @param \WP_User $user
     */
    public function render(\WP_User $user)
    {
        $args = $this->getProps();

        $args['fields'] = Field::renderMany($this->fields, $user->ID);

        echo $this->main->render('templates/profile-section', $args);
    }

    /**
     * Save Section fields
     *
     * @param int $userId User ID.
     */
    public function save(int $userId)
    {
        if (!current_user_can('edit_user')) {
            $this->log('Current user has no permissions to edit users');
            return;
        }

        if (empty($_POST[$this->prefix])) {
            return;
        }

        $values = $_POST[$this->prefix];

        Field::setMany($this->fields, $values, $userId);

        do_action('adwpfw_profile_saved', $this, $values);
    }

    /**
     * Get Default prop values
     *
     * @return array
     */
    protected function defaults(): array
    {
        return [
            'title' => 'Custom',
            'id' => function () {
                return sanitize_key(str_replace(' ', '_', $this->getProp('title')));
            },
            'assets' => [],
        ];
    }
}
