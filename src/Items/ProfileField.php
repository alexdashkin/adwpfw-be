<?php

namespace AlexDashkin\Adwpfw\Items;

use AlexDashkin\Adwpfw\App;

/**
 * User Profile Custom Field
 */
class ProfileField extends Item // todo use Fields
{
    private $metaKey;

    /**
     * Constructor
     *
     * @param array $data {
     * @type string $slug Field Slug. Used to get the saved value. Required.
     * @type string $name Field Label. Required.
     * @type string $type Field Type. Default 'text'.
     * @type string $desc Field Description to be shown below the Field
     * }
     */
    public function __construct(array $data, App $app)
    {
        $this->props = [
            'slug' => [
                'required' => true,
            ],
            'name' => [
                'required' => true,
            ],
            'type' => [
                'default' => 'text',
            ],
            'desc' => [
                'default' => null,
            ],
        ];

        parent::__construct($data, $app);

        $this->metaKey = $this->config['prefix'] . '_' . $this->data['name'];
    }

    /**
     * Get a field value
     *
     * @param int|null $userId User ID (defaults to the current user)
     * @return mixed
     */
    public function get($userId = null)
    {
        $userId = $userId ?: get_current_user_id();

        if (!$user = get_user_by('ID', $userId)) {
            return '';
        }

        return get_user_meta($userId, $this->metaKey, true);
    }

    public function getTwigArgs($userId)
    {
        $args = $this->data;

        $args['value'] = $this->get($userId);

        return $args;
    }

    public function save($userId, $post)
    {
        update_user_meta($userId, $this->metaKey, $post);
    }
}
