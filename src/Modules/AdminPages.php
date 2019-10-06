<?php

namespace AlexDashkin\Adwpfw\Modules;

use AlexDashkin\Adwpfw\App;

/**
 * Admin Dashboard sub-menu pages
 */
class AdminPages extends Module
{
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
     * Add hooks
     */
    protected function run()
    {
        add_action('admin_menu', [$this, 'register']);
    }

    /**
     * Register Pages in WP
     * Hooked to "admin_menu" action
     */
    public function register()
    {
        foreach ($this->items as $item) {

            $data = $item->data;

            add_dashboard_page($data['title'], $data['name'], 'read', $data['id'], $data['callback']);
        }
    }
}
