<?php

namespace AlexDashkin\Adwpfw\Modules\WithItems;

use AlexDashkin\Adwpfw\App;
use AlexDashkin\Adwpfw\Items\Basic\Sidebar;

/**
 * Sidebars.
 */
class Sidebars extends ModuleWithItems
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
     * Add Sidebar
     *
     * @param array $data
     *
     * @see Sidebar::__construct();
     *
     * @throws \AlexDashkin\Adwpfw\Exceptions\AdwpfwException
     */
    public function add(array $data)
    {
        $this->items[] = new Sidebar($data, $this->app);
    }

    /**
     * Hooks to register Items in WP.
     */
    protected function init()
    {
        add_action('widgets_init', [$this, 'register']);
    }

    /**
     * Register Sidebars.
     */
    public function register()
    {
        foreach ($this->items as $item) {
            $item->register();
        }
    }
}
