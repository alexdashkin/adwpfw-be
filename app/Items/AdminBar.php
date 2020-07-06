<?php

namespace AlexDashkin\Adwpfw\Items;

use AlexDashkin\Adwpfw\Abstracts\Module;

class AdminBar extends Module
{
    /**
     * Init Module
     */
    public function init()
    {
        $this->hook('admin_bar_menu', [$this, 'register'], 99);
    }

    /**
     * Register Admin Bars in WP
     * Hooked to "admin_bar_menu" action
     *
     * @param \WP_Admin_Bar $wpAdminBar
     */
    public function register(\WP_Admin_Bar $wpAdminBar)
    {
        if (!current_user_can($this->get('capability'))) {
            return;
        }

        $this->set('id', $this->get('prefix') . '-' . $this->get('id'));

        $wpAdminBar->add_node($this->data);
    }

    /**
     * Get Class props
     *
     * @return array
     */
    protected function props(): array
    {
        return [
            'prefix' => [
                'required' => true,
            ],
            'title' => [
                'required' => true,
            ],
            'id' => [
                'default' => function ($data) {
                    return sanitize_key(str_replace(' ', '-', $data['title']));
                },
            ],
            'parent' => [
                'default' => null,
            ],
            'capability' => [
                'default' => 'manage_options'
            ],
            'href' => [
                'default' => null,
            ],
            'group' => [
                'type' => 'bool',
                'default' => false,
            ],
            'meta' => [
                'type' => 'array',
                'default' => [],
            ],
        ];
    }
}
