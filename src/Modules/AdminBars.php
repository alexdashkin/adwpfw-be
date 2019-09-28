<?php

namespace AlexDashkin\Adwpfw\Modules;

use AlexDashkin\Adwpfw\App;
use AlexDashkin\Adwpfw\Entities\AdminBar;

/**
 * WP Admin Top Bar Entry
 */
class AdminBars extends Module
{
    /**
     * Constructor
     *
     * @param App $app
     */
    public function __construct(App $app)
    {
        parent::__construct($app);
    }

    /**
     * Add hooks
     */
    protected function run()
    {
        add_action('admin_bar_menu', [$this, 'registerBar'], 999);
    }

    /**
     * Add an item to the Top Admin Bar
     *
     * @param array $data {
     * @type string $id
     * @type string $title
     * @type string $capability Who can see the Bar
     * @type string $href URL of the link
     * @type array $meta
     * }
     */
    public function addBar(array $data)
    {
        $this->items[] = new AdminBar($data);
    }

    /**
     * Add multiple items to the Top Admin Bar
     *
     * @param array $bars
     *
     * @see AdminBars::addBar()
     */
    public function addBars(array $bars)
    {
        foreach ($bars as $bar) {
            $this->addBar($bar);
        }
    }

    /**
     * @param \WP_Admin_Bar $wpAdminBar
     */
    public function registerBar($wpAdminBar)
    {
        foreach ($this->items as $bar) {
            if (!current_user_can($bar['capability'])) {
                continue;
            }

            foreach ($bar as $key => &$arg) {
                if ('meta' === $key) {
                    continue;
                }
                if (is_array($arg)) {
                    $arg = $this->m('Common\Utils')->renderTwig($arg[0], $arg[1]);
                }
            }

            $wpAdminBar->add_node($bar);
        }
    }
}
