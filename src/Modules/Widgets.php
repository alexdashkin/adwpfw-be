<?php

namespace AlexDashkin\Adwpfw\Modules;

use AlexDashkin\Adwpfw\App;
use AlexDashkin\Adwpfw\Exceptions\AdwpfwException;
use AlexDashkin\Adwpfw\Items\Widget;

/**
 * Admin Dashboard widgets.
 */
class Widgets extends ModuleWithItems
{
    /**
     * @var Widget[]
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

        add_action('wp_dashboard_setup', [$this, 'register']);
    }

    /**
     * Add Widget.
     *
     * @param array $data
     *
     * @see Widget::__construct()
     *
     * @throws AdwpfwException
     */
    public function add(array $data)
    {
        $this->items[] = new Widget($this->app, $data);
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
