<?php

namespace AlexDashkin\Adwpfw\Modules;

use AlexDashkin\Adwpfw\App;
use AlexDashkin\Adwpfw\Exceptions\AdwpfwException;
use AlexDashkin\Adwpfw\Items\Widget;

/**
 * Theme widgets.
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

        add_action('widgets_init', [$this, 'register']);
    }

    /**
     * Add Widget.
     *
     * @param array $data
     *
     * @throws AdwpfwException
     * @see Widget::__construct()
     *
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
