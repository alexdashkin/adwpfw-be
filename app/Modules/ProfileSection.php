<?php

namespace AlexDashkin\Adwpfw\Modules;

use AlexDashkin\Adwpfw\{Fields\Field, Modules\Assets\Asset};

/**
 * User Profile Custom Fields
 */
class ProfileSection extends FieldHolder
{
    /**
     * @var Asset[]
     */
    protected $assets = [];
    
    /**
     * Add Asset
     *
     * @param Asset $asset
     */
    public function addAsset(Asset $asset)
    {
        $this->assets[] = $asset;
    }
    
    /**
     * Constructor
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
        // Enqueue assets
        foreach ($this->assets as $asset) {
            $asset->enqueue();
        }
        
        // Prepare args
        $args = [
            'title' => $this->getProp('title'),
            'description' => $this->getProp('description'),
            'fields' => $this->getFieldsArgs($user->ID),
        ];
        
        // Output template
        echo $this->app->render('layouts/profile-section', $args);
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
        
        Field::setMany($this->fields, $_POST, $userId);
    }
    
    /**
     * Get field value
     *
     * @param Field $field
     * @param int $objectId
     * @return mixed
     */
    public function getFieldValue(Field $field, int $objectId = 0)
    {
        return $this->getUserMeta($objectId, $field->getProp('name'));
    }
    
    /**
     * Set field value
     *
     * @param Field $field
     * @param $value
     * @param int $objectId
     * @return bool
     */
    public function setFieldValue(Field $field, $value, int $objectId = 0): bool
    {
        return $this->updateUserMeta($objectId, $field->getProp('name'), $value);
    }
    
    /**
     * Get prop definitions
     *
     * @return array
     */
    protected function getPropDefs(): array
    {
        return [
            'id' => [
                'type' => 'string',
                'default' => function () {
                    return sanitize_key(str_replace(' ', '_', $this->getProp('title')));
                },
            ],
            'assets' => [
                'type' => 'array',
                'default' => [],
            ],
        ];
    }
}
