<?php

namespace AlexDashkin\Adwpfw\Modules;

use AlexDashkin\Adwpfw\App;
use AlexDashkin\Adwpfw\Items\AdminBar;

/**
 * Top Admin Bar Items
 */
class AdminBars extends ItemsModule
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
     * Add Admin Bar
     *
     * @param array $data
     * @param App $app
     */
    public function add(array $data, App $app)
    {
        $this->items[] = new AdminBar($data, $app);
    }

    /**
     * Hooks to register Items in WP
     */
    protected function hooks()
    {
        add_action('admin_bar_menu', [$this, 'register'], 999);
    }

    /**
     * Register Items in WP
     *
     * @param \WP_Admin_Bar $adminBar
     */
    public function register(\WP_Admin_Bar $adminBar)
    {
        /**
         * @var AdminBar $item
         */
        foreach ($this->items as $item) {
            $item->register($adminBar);
        }
    }
}
