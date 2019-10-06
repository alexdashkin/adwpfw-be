<?php

namespace AlexDashkin\Adwpfw\Modules;

use AlexDashkin\Adwpfw\App;

/**
 * Top Admin Bar Items
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
        add_action('admin_bar_menu', [$this, 'register'], 999);
    }

    /**
     * Register Admin Bars in WP
     * Hooked to "admin_bar_menu" action
     *
     * @param \WP_Admin_Bar $wpAdminBar
     */
    public function register(\WP_Admin_Bar $wpAdminBar)
    {
        foreach ($this->items as $item) {
            $data = $item->data;
            
            if (!current_user_can($data['capability'])) {
                continue;
            }

            foreach ($data as $key => &$arg) {
                if ('meta' === $key) {
                    continue;
                }

                if (is_array($arg)) {
                    $arg = $this->m('Utils')->renderTwig($arg[0], $arg[1]);
                }
            }

            $wpAdminBar->add_node($data);
        }
    }
}
