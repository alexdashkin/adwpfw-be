<?php

namespace AlexDashkin\Adwpfw\Modules;

use AlexDashkin\Adwpfw\Abstracts\Module;
use AlexDashkin\Adwpfw\Fields\Field;

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
        $this->hook('show_user_profile', [$this, 'render']);
        $this->hook('edit_user_profile', [$this, 'render']);
        $this->hook('personal_options_update', [$this, 'save']);
        $this->hook('edit_user_profile_update', [$this, 'save']);
    }

    /**
     * Render Section
     *
     * @param \WP_User $user
     */
    public function render(\WP_User $user)
    {
        $values = get_user_meta($user->ID, $this->gp('prefix') . '_' . $this->gp('id'), true) ?: [];

        $fields = [];

        foreach ($this->fields as $field) {
            $fieldName = $field->gp('name');

            $twigArgs = $field->getTwigArgs($values[$fieldName] ?? null);

            $fields[] = $twigArgs;
        }

        $args = [
            'heading' => $this->gp('heading'),
            'fields' => $fields,
        ];

        echo $this->twig('templates/profile-section', $args);
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

        $id = $this->gp('id');
        $prefix = $this->gp('prefix');
        $metaKey = $prefix . '_' . $id;

        if (empty($_POST[$prefix][$id])) {
            return;
        }

        $form = $_POST[$prefix][$id];

        $values = [];

        foreach ($this->fields as $field) {
            $fieldName = $field->gp('name');

            if (empty($fieldName) || !array_key_exists($fieldName, $form)) {
                continue;
            }

            $values[$fieldName] = $field->sanitize($form[$fieldName]);
        }

        update_user_meta($userId, $metaKey, $values);

        do_action('adwpfw_profile_saved', $this, $values);
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
            'heading' => [
                'default' => '',
            ],
            'id' => [
                'default' => function ($data) {
                    return sanitize_key(str_replace(' ', '-', $data['prefix']));
                },
            ],
        ];
    }
}
