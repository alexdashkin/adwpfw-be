<?php

namespace AlexDashkin\Adwpfw\Modules\WithItems;

use AlexDashkin\Adwpfw\App;
use AlexDashkin\Adwpfw\Items\Basic\Widget;

/**
 * Admin Dashboard widgets.
 */
class Widgets extends ModuleWithItems
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
     * Add Widget.
     *
     * @param array $data
     *
     * @see Widget::__construct()
     *
     * @throws \AlexDashkin\Adwpfw\Exceptions\AdwpfwException
     */
    public function add(array $data)
    {
        $this->items[] = new Widget($data, $this->app);
    }

    /**
     * Hooks to register Items in WP.
     */
    protected function init()
    {
        add_action('wp_dashboard_setup', [$this, 'register']);
    }

    /**
     * Register Widgets.
     */
    public function register()
    {
        foreach ($this->items as $item) {
            $item->register();
        }
    }
}
