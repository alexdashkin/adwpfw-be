<?php

namespace AlexDashkin\Adwpfw\Modules;

use AlexDashkin\Adwpfw\App;
use AlexDashkin\Adwpfw\Exceptions\AdwpfwException;
use AlexDashkin\Adwpfw\Items\ProfileField;

/**
 * User Profile Custom fields.
 */
class Profile extends ModuleWithItems
{
    private $heading = 'Custom fields';

    /**
     * Constructor.
     *
     * @param App $app
     */
    public function __construct(App $app)
    {
        parent::__construct($app);

        add_action('show_user_profile', [$this, 'render']);
        add_action('edit_user_profile', [$this, 'render']);
        add_action('personal_options_update', [$this, 'save']);
        add_action('edit_user_profile_update', [$this, 'save']);
    }

    /**
     * Add Profile Field.
     *
     * @param array $data
     *
     * @see ProfileField::__construct();
     *
     * @throws AdwpfwException
     */
    public function add(array $data)
    {
        $this->items[] = new ProfileField($this->app, $data);
    }

    /**
     * Set Fields Group Heading.
     *
     * @param string $heading Heading Text
     */
    public function setHeading($heading)
    {
        $this->heading = $heading;
    }

    public function get($id, $userId = null)
    {
        if ($item = $this->searchItems(['id' => $id], true)) {
            return $item->get($userId);
        }

        return null;
    }

    public function set($id, $value, $userId = null)
    {
        if ($item = $this->searchItems(['id' => $id], true)) {
            return $item->set($value, $userId);
        }

        return null;
    }

    /**
     * Save posted data.
     *
     * @param int $userId User ID.
     */
    public function save($userId)
    {
        if (!current_user_can('edit_user')) {
            return;
        }

        $prefix = $this->config['prefix'];

        if (empty($_POST[$prefix]['profile'])) {
            return;
        }

        foreach ($this->items as $item) {
            $item->save($userId, $_POST[$prefix]['profile']);
        }
    }

    /**
     * Render the Fields.
     *
     * @param \WP_User $user
     */
    public function render($user)
    {
        $fields = [];

        foreach ($this->items as $item) {
            $fields[] = $item->getArgs($user->ID);
        }

        $args = [
            'prefix' => $this->config['prefix'],
            'heading' => $this->heading,
            'fields' => $fields,
        ];

        echo $this->m('Twig')->renderFile('templates/profile', $args);
    }
}
