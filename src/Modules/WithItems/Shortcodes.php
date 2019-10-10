<?php

namespace AlexDashkin\Adwpfw\Modules\WithItems;

use AlexDashkin\Adwpfw\App;
use AlexDashkin\Adwpfw\Items\Shortcode;

/**
 * Manage Shortcodes
 */
class Shortcodes extends ModuleWithItems
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
    protected function init()
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
