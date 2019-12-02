<?php

namespace AlexDashkin\Adwpfw\Modules;

use AlexDashkin\Adwpfw\App;
use AlexDashkin\Adwpfw\Exceptions\AdwpfwException;
use AlexDashkin\Adwpfw\Items\Sidebar;

/**
 * Sidebars.
 */
class Sidebars extends ModuleWithItems
{
    /**
     * @var Sidebar[]
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

        add_action('widgets_init', [$this, 'register']);
    }

    /**
     * Add Sidebar
     *
     * @param array $data. Data to pass to Sidebar constructor.
     *
     * @see Sidebar::__construct();
     *
     * @throws AdwpfwException
     */
    public function add(array $data)
    {
        $this->items[] = new Sidebar($this->app, $data);
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
