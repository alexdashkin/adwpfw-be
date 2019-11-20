<?php

namespace AlexDashkin\Adwpfw\Modules;

use AlexDashkin\Adwpfw\App;
use AlexDashkin\Adwpfw\Exceptions\AdwpfwException;
use AlexDashkin\Adwpfw\Items\AdminBar;

/**
 * Top Admin Bar Items.
 */
class AdminBars extends ModuleWithItems
{
    /**
     * @var AdminBar[]
     */
    protected $items = [];

    /**
     * Constructor.
     *
     * @param App $app
     */
    public function __construct(App $app)
    {
        parent::__construct($app);

        add_action('admin_bar_menu', [$this, 'register'], 999);
    }

    /**
     * Add Admin Bar
     *
     * @param array $data
     *
     * @see AdminBar::__construct()
     *
     * @throws AdwpfwException
     */
    public function add(array $data)
    {
        $this->items[] = new AdminBar($this->app, $data);
    }

    /**
     * Register Admin Bars in WP
     *
     * @param \WP_Admin_Bar $adminBar
     */
    public function register(\WP_Admin_Bar $adminBar)
    {
        foreach ($this->items as $item) {
            $item->register($adminBar);
        }
    }
}
