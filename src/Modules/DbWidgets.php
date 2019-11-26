<?php

namespace AlexDashkin\Adwpfw\Modules;

use AlexDashkin\Adwpfw\App;
use AlexDashkin\Adwpfw\Exceptions\AdwpfwException;
use AlexDashkin\Adwpfw\Items\DbWidget;

/**
 * Admin Dashboard widgets.
 */
class DbWidgets extends ModuleWithItems
{
    /**
     * @var DbWidget[]
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
     * @throws AdwpfwException
     *@see DbWidget::__construct()
     *
     */
    public function add(array $data)
    {
        $this->items[] = new DbWidget($this->app, $data);
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
