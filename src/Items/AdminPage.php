<?php

namespace AlexDashkin\Adwpfw\Items;

use AlexDashkin\Adwpfw\App;

/**
 * Admin Dashboard sub-menu page
 */
class AdminPage extends Item
{
    /**
     * Constructor
     *
     * @param array $data {
     * @type string $id
     * @type string $title
     * @type string $name
     * @type callable $callback Renders the page
     * }
     */
    public function __construct(array $data, App $app)
    {
        $this->defaults = [
            'id' => '',
            'title' => 'Page',
            'name' => 'Page',
            'callback' => '',
        ];

        parent::__construct($data, $app);
    }

    /**
     * Hooks to register Item in WP
     */
    protected function hooks()
    {
        add_action('admin_menu', [$this, 'register']);
    }

    /**
     * Register Page in WP
     * Hooked to "admin_menu" action
     */
    public function register()
    {
        $data = $this->data;

        add_dashboard_page($data['title'], $data['name'], 'read', $data['id'], $data['callback']);
    }
}
