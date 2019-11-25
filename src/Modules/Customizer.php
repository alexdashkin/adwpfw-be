<?php

namespace AlexDashkin\Adwpfw\Modules;

use AlexDashkin\Adwpfw\App;
use AlexDashkin\Adwpfw\Exceptions\AdwpfwException;
use AlexDashkin\Adwpfw\Items\Customizer\Panel;

/**
 * Customizer settings
 */
class Customizer extends ModuleWithItems
{
    /**
     * @var Panel[]
     */
    protected $items = [];

    /**
     * Constructor
     *
     * @param App $app
     */
    public function __construct(App $app)
    {
        parent::__construct($app);
        add_action('customize_register', [$this, 'register']);
    }

    /**
     * Add Panel
     *
     * @param array $data
     *
     * @see Panel::__construct();
     *
     * @throws AdwpfwException
     */
    public function add(array $data)
    {
        $this->items[] = new Panel($this->app, $data);
    }

    public function get($id)
    {
        return get_theme_mod($this->prefix . '_' . $id);
    }

    /**
     * Register Panel
     *
     * @param \WP_Customize_Manager $customizer
     */
    public function register(\WP_Customize_Manager $customizer)
    {
        foreach ($this->items as $item) {
            $item->register($customizer);
        }
    }
}
