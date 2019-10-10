<?php

namespace AlexDashkin\Adwpfw\Modules\WithItems;

use AlexDashkin\Adwpfw\App;
use AlexDashkin\Adwpfw\Items\Basic\Shortcode;

/**
 * Shortcodes.
 */
class Shortcodes extends ModuleWithItems
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
     * Add Shortcode.
     *
     * @param array $data
     * @param App $app
     *
     * @throws \AlexDashkin\Adwpfw\Exceptions\AdwpfwException
     *
     * @see Shortcode::__construct();
     */
    public function add(array $data, App $app)
    {
        $this->items[] = new Shortcode($data, $app);
    }

    /**
     * Hooks to register Items in WP.
     */
    protected function init()
    {
        add_action('init', [$this, 'register'], 999);
    }

    /**
     * Register Shortcodes.
     */
    public function register()
    {
        foreach ($this->items as $item) {
            $item->register();
        }
    }
}
