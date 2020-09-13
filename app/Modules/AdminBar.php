<?php

namespace AlexDashkin\Adwpfw\Modules;

/**
 * title*, id, parent, href, group, meta, capability
 */
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
        if (!current_user_can($this->getProp('capability'))) {
            return;
        }

        $this->setProp('id', $this->prefix . '-' . $this->getProp('id'));

        $wpAdminBar->add_node($this->getProps());
    }

    /**
     * Get Default prop values
     *
     * @return array
     */
    protected function defaults(): array
    {
        return [
            'title' => 'Admin Bar',
            'id' => function () {
                return sanitize_key(str_replace(' ', '-', $this->getProp('title')));
            }
        ];
    }
}
