<?php

namespace AlexDashkin\Adwpfw\Modules;

/**
 * User Profile page
 */
class Profile extends \AlexDashkin\Adwpfw\Common\Base
{
    private $heading = 'Custom fields';
    private $fields = [];
    private $prefix;

    public function __construct($app)
    {
        parent::__construct($app);
    }

    public function run()
    {
        $this->prefix = $this->config['prefix'];

        add_action('show_user_profile', [$this, 'render']);
        add_action('edit_user_profile', [$this, 'render']);

        add_action('personal_options_update', [$this, 'save']);
        add_action('edit_user_profile_update', [$this, 'save']);
    }

    /**
     * Set Fields Group Heading
     *
     * @param string $heading Heading Text
     */
    public function setHeading($heading)
    {
        $this->heading = $heading;
    }

    /**
     * Add a Profile Field
     *
     * @param array $field {
     * @type string $id
     * @type string $type
     * @type string $name
     * @type string $desc
     * }
     */
    public function addField(array $field)
    {
        $field = array_merge([
            'id' => '',
            'type' => 'text',
            'name' => 'Custom field',
            'desc' => '',
        ], $field);

        $field['id'] = $field['id'] ?: sanitize_title($field['name']);

        $this->fields[] = $field;
    }

    /**
     * Add multiple Profile Fields
     *
     * @param array $fields
     *
     * @see Profile::addField()
     */
    public function addFields(array $fields)
    {
        foreach ($fields as $field) {
            $this->addField($field);
        }
    }

    /**
     * Get a field value
     *
     * @param string $id Field ID
     * @param int|null $userId User ID (defaults to the current user)
     * @return mixed
     */
    public function get($id, $userId = null)
    {
        $userId = $userId ?: get_current_user_id();

        if (!$user = get_user_by('ID', $userId)) {
            return '';
        }

        return get_user_meta($userId, '_' . $this->prefix . '_' . $id, true);
    }

    public function save($userId)
    {
        if (!current_user_can('edit_user')) {
            return;
        }

        $prefix = $this->prefix;
        if (empty($_POST[$prefix])) {
            return;
        }

        foreach ($this->fields as $field) {
            $id = $field['id'];
            update_user_meta($userId, '_' . $this->prefix . '_' . $id, $_POST[$prefix][$id]);
        }
    }

    public function render($user)
    {
        $args = [
            'prefix' => $this->prefix,
            'heading' => $this->heading,
        ];

        foreach ($this->fields as $field) {
            $id = $field['id'];
            $field['value'] = get_user_meta($user->ID, '_' . $this->prefix . '_' . $id, true);
            $args['fields'][] = $field;
        }

        echo $this->twig('profile', $args);
    }

    private function twig($name, $args = [])
    {
        return $this->m('Utils')->renderTwig($name, $args);
    }
}
