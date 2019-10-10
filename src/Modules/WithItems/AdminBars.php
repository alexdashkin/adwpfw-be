<?php

namespace AlexDashkin\Adwpfw\Modules\WithItems;

use AlexDashkin\Adwpfw\App;
use AlexDashkin\Adwpfw\Items\Basic\AdminBar;

/**
 * Top Admin Bar Items.
 */
class AdminBars extends ModuleWithItems
{
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
     * Add Admin Bar
     *
     * @param array $data
     * @param App $app
     *
     * @throws \AlexDashkin\Adwpfw\Exceptions\AdwpfwException
     *
     * @see AdminBar::__construct();
     */
    public function add(array $data, App $app)
    {
        $this->items[] = new AdminBar($data, $app);
    }

    /**
     * Init the Module
     */
    protected function init()
    {
        add_action('admin_bar_menu', [$this, 'register'], 999);
    }

    /**
     * Register Admin Bars in WP
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
