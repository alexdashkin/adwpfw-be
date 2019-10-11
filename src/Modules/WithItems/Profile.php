<?php

namespace AlexDashkin\Adwpfw\Modules\WithItems;

use AlexDashkin\Adwpfw\App;
use AlexDashkin\Adwpfw\Items\Basic\ProfileField;

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
    }

    /**
     * Add Profile Field.
     *
     * @param array $data
     *
     * @see ProfileField::__construct();
     *
     * @throws \AlexDashkin\Adwpfw\Exceptions\AdwpfwException
     */
    public function add(array $data)
    {
        $this->items[] = new ProfileField($data, $this->app);
    }

    /**
     * Add hooks
     */
    protected function init()
    {
        add_action('show_user_profile', [$this, 'render']);
        add_action('edit_user_profile', [$this, 'render']);

        add_action('personal_options_update', [$this, 'save']);
        add_action('edit_user_profile_update', [$this, 'save']);
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
        // todo implement
    }

    public function set($id, $value, $userId = null)
    {
        // todo implement
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

        if (empty($_POST[$prefix])) {
            return;
        }

        foreach ($this->items as $item) {
            $item->save($userId, $_POST[$prefix]);
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

        echo $this->m('Twig')->renderFile('profile', $args);
    }
}
