<?php

namespace AlexDashkin\Adwpfw\Modules;

use AlexDashkin\Adwpfw\App;
use AlexDashkin\Adwpfw\Items\Shortcode;

/**
 * Manage Shortcodes
 */
class Shortcodes extends ItemsModule
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
        $this->items[] = new Shortcode($data, $app);
    }

    /**
     * Hooks to register Items in WP
     */
    protected function hooks()
    {
        add_action('init', [$this, 'register'], 999);
    }

    public function register()
    {
        foreach ($this->items as $item) {
            $item->register();
        }
    }
}
