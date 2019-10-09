<?php

namespace AlexDashkin\Adwpfw\Modules;

use AlexDashkin\Adwpfw\App;
use AlexDashkin\Adwpfw\Items\ProfileField;

/**
 * User Profile Custom fields
 */
class Profile extends ItemsModule
{
    private $heading = 'Custom fields';

    /**
     * Constructor
     *
     * @param App $app
     */
    public function __construct(App $app)
    {
        parent::__construct($app);
    }

    /**
     * Add an Item
     *
     * @param array $data
     * @param App $app
     * @throws \AlexDashkin\Adwpfw\Exceptions\AdwpfwException
     */
    public function add(array $data, App $app)
    {
        $this->items[] = new ProfileField($data, $app);
    }

    /**
     * Hooks to register Items in WP
     */
    protected function hooks()
    {
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

    public function save($userId)
    {
        if (!current_user_can('edit_user')) {
            return;
        }

        $prefix = $this->config['prefix'];

        if (empty($_POST[$prefix])) {
            return;
        }

        foreach ($this->items as $item) {
            $item->save($userId, $_POST['prefix']);
        }
    }

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

        echo $this->m('Utils')->renderTwig('profile', $args); // todo use fields tpls
    }
}
