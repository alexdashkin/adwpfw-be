<?php

namespace AlexDashkin\Adwpfw\Modules;

use AlexDashkin\Adwpfw\App;
use AlexDashkin\Adwpfw\Items\Widget;

/**
 * Admin Dashboard widgets
 */
class Widgets extends ItemsModule
{
    /**
     * Constructor
     *
     * @param App $app
     */
    protected function __construct(App $app)
    {
        parent::__construct($app);
    }

    /**
     * Add an Item
     *
     * @param array $data
     * @param App $app
     */
    public function add(array $data, App $app)
    {
        $this->items[] = new Widget($data, $app);
    }

    /**
     * Hooks to register Items in WP
     */
    protected function hooks()
    {
        add_action('wp_dashboard_setup', [$this, 'register']);
    }

    public function register()
    {
        foreach ($this->items as $item) {
            $item->register();
        }
    }
}
