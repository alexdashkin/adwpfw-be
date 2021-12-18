<?php

namespace AlexDashkin\Adwpfw\Modules;

/**
 * Top Admin Bar Item
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
     * Register Admin Bar in WP
     * Hooked to "admin_bar_menu" action
     *
     * @param \WP_Admin_Bar $wpAdminBar
     */
    public function register(\WP_Admin_Bar $wpAdminBar)
    {
        if (!current_user_can($this->getProp('capability'))) {
            return;
        }

        $wpAdminBar->add_node($this->getProps());
    }

    /**
     * Get prop definitions
     *
     * @return array
     */
    protected function getPropDefs(): array
    {
        return [
            'title' => [
                'type' => 'string',
                'required' => true,
            ],
            'id' => [
                'type' => 'string',
                'default' => function () {
                    return sanitize_key(str_replace(' ', '-', $this->getProp('title')));
                },
            ],
            'capability' => [
                'type' => 'string',
                'default' => 'administrator',
            ],
        ];
    }
}
