<?php

namespace AlexDashkin\Adwpfw\Modules;

use AlexDashkin\Adwpfw\App;
use AlexDashkin\Adwpfw\Exceptions\AdwpfwException;
use AlexDashkin\Adwpfw\Items\Shortcode;

/**
 * Shortcodes.
 */
class Shortcodes extends ModuleWithItems
{
    /**
     * @var Shortcode[]
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

        add_action('init', [$this, 'register'], 999);
    }

    /**
     * Add Shortcode.
     *
     * @param array $data . Data to pass to Shortcode constructor.
     *
     * @throws AdwpfwException
     * @see Shortcode::__construct();
     *
     */
    public function add(array $data)
    {
        $this->items[] = new Shortcode($this->app, $data);
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
