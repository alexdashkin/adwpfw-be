<?php

namespace AlexDashkin\Adwpfw\Modules;

use AlexDashkin\Adwpfw\App;
use AlexDashkin\Adwpfw\Items\AdminPage;

/**
 * Admin Dashboard sub-menu pages
 */
class AdminPages extends ItemsModule
{
    /**
     * Constructor
     *
     * @param App $app
     */
    public function __construct(App $app)
    {
        parent::__construct($app);

        $this->itemClass = 'AdminPage';
    }

    /**
     * Add Admin Page
     *
     * @param array $data
     * @param App $app
     */
    public function add(array $data, App $app)
    {
        $this->items[] = new AdminPage($data, $app);
    }
}
