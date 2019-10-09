<?php

namespace AlexDashkin\Adwpfw\Items;

use AlexDashkin\Adwpfw\App;

/**
 * User Profile Custom Field
 */
class ProfileField extends Item // todo use Fields
{
    /**
     * @var FormField
     */
    private $field;

    private $metaKey;

    /**
     * Constructor
     *
     * @param array $data {
     * @type string $id Required.
     * @type string $label Field Label. Required.
     * @type string $type Field Type. Default 'text'.
     * @type string $desc Field Description to be shown below the Field
     * }
     *
     * @throws \AlexDashkin\Adwpfw\Exceptions\AdwpfwException
     */
    public function __construct(array $data, App $app)
    {
        $props = [
            'id' => [
                'required' => true,
            ],
            'label' => [
                'required' => true,
            ],
            'type' => [
                'default' => 'text',
            ],
            'desc' => [
                'default' => null,
            ],
        ];

        parent::__construct($data, $app, $props);

        $this->field = FormField::getField($this->data, $app);

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

    public function getArgs($userId)
    {
        return $this->field->getArgs([$this->data['id'] => $this->get($userId)]);
    }

    public function save($userId, $post)
    {
        update_user_meta($userId, $this->metaKey, $post);
    }
}
