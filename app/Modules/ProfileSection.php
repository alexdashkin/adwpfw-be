<?php

namespace AlexDashkin\Adwpfw\Modules;

/**
 * slug*, title
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
    }

    /**
     * Render Section
     *
     * @param \WP_User $user
     */
    public function render(\WP_User $user)
    {
        $args = $this->getProps();
        $values = get_user_meta($user->ID, '_' . $this->prefix . '_' . $this->getProp('slug'), true) ?: [];
        $args['fields'] = Field::getArgsForMany($this->fields, $values);

        return $this->app->main->render('templates/profile-section', $args);
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

        $id = $this->getProp('slug');
        $prefix = $this->prefix;
        $metaKey = '_' . $prefix . '_' . $id;

        if (empty($_POST[$prefix][$id])) {
            return;
        }

        $form = $_POST[$prefix][$id];

        $values = [];

        foreach ($this->fields as $field) {
            $fieldName = $field->getProp('name');

            if (empty($fieldName) || !array_key_exists($fieldName, $form)) {
                continue;
            }

            $values[$fieldName] = $field->sanitize($form[$fieldName]);
        }

        update_user_meta($userId, $metaKey, $values);

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
            'slug' => function () {
                return sanitize_key(str_replace(' ', '-', $this->getProp('title')));
            },
        ];
    }
}
