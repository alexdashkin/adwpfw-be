<?php

namespace AlexDashkin\Adwpfw\Modules;

/**
 * Widget sidebars
 */
class Sidebar extends Module
{
    private $sidebars = [];

    public function __construct($app)
    {
        parent::__construct($app);
    }

    public function run()
    {
        add_action('widgets_init', [$this, 'register']);
    }

    /**
     * Add a sidebar
     *
     * @param array $sidebar
     *
     * @see register_sidebar()
     */
    public function addSidebar(array $sidebar)
    {
        $this->sidebars[] = $sidebar;
    }

    public function register()
    {
        foreach ($this->sidebars as $sidebar) {
            register_sidebar($sidebar);
        }
    }
}
