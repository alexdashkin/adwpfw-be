<?php

namespace AlexDashkin\Adwpfw\Modules;

class AdminBar extends Module
{
    /**
     * Init Module
     */
    public function init()
    {
        $this->addHook('admin_bar_menu', [$this, 'register'], 99);
    }

    /**
     * Register Admin Bars in WP
     * Hooked to "admin_bar_menu" action
     *
     * @param \WP_Admin_Bar $wpAdminBar
     */
    public function register(\WP_Admin_Bar $wpAdminBar)
    {
        if (!current_user_can($this->getProp('capability', 'manage_options'))) {
            return;
        }

        $defaultId = sanitize_key(str_replace(' ', '-', $this->getProp('title')));

        $this->setProp('id', $this->config('prefix') . '-' . $this->getProp('id', $defaultId));

        $wpAdminBar->add_node($this->getProps());
    }
}
