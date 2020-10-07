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
        $values = get_user_meta($user->ID, '_' . $this->prefix . '_' . $this->getProp('id'), true) ?: [];
        $args['fields'] = Field::getArgsForMany($this->fields, $values);

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

        $id = $this->getProp('id');
        $prefix = $this->prefix;

        if (empty($_POST[$prefix][$id])) {
            return;
        }

        $values = Field::getFieldValues($this->fields, $_POST[$prefix][$id]);

        update_user_meta($userId, '_' . $prefix . '_' . $id, $values);

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
        ];
    }
}
