<?php

namespace AlexDashkin\Adwpfw\Front;

/**
 * Widget sidebars
 */
class Sidebar extends \AlexDashkin\Adwpfw\Common\Base
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