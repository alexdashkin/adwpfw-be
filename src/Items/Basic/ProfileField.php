<?php

namespace AlexDashkin\Adwpfw\Items\Basic;

use AlexDashkin\Adwpfw\App;
use AlexDashkin\Adwpfw\Fields\Field;

/**
 * User Profile Custom Field
 */
class ProfileField extends Item
{
    /**
     * @var Field
     */
    private $field;

    private $metaKey;

    /**
     * Constructor.
     *
     * @param array $data {
     * @type string $id Field Name used as a key in $prefix[] array on the Form. Required.
     * @type string $label Field Label. Required.
     * @type string $type Field Type. Default 'text'.
     * @type string $desc Field Description to be shown below the Field.
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
            'class' => [
                'default' => 'regular-text',
            ],
            'desc' => [
                'default' => null,
            ],
        ];

        parent::__construct($data, $app, $props);

        $this->data['layout'] = 'profile-field';
        $this->data['form'] = 'profile';

        $this->field = Field::getField($this->data);

        $this->metaKey = $this->prefix . '_' . $this->data['id'];
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

    /**
     * Get Twig Args for rendering the Field.
     *
     * @param int $userId User ID.
     * @return array Twig Args.
     */
    public function getArgs($userId)
    {
        return $this->field->getArgs([$this->data['id'] => $this->get($userId)]);
    }

    /**
     * Save the Field.
     *
     * @param int $userId User ID.
     * @param array $data Posted data.
     * @return bool|int
     */
    public function save($userId, $data)
    {
        if (!array_key_exists($this->data['id'], $data)) {
            return false;
        }

        $value = $this->field->sanitize($data[$this->data['id']]);

        return update_user_meta($userId, $this->metaKey, $value);
    }
}
