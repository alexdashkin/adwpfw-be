<?php

namespace AlexDashkin\Adwpfw\Modules;

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
        if (!current_user_can($this->gp('capability'))) {
            return;
        }

        $this->sp('id', $this->gp('prefix') . '-' . $this->gp('id'));

        $wpAdminBar->add_node($this->gp());
    }

    /**
     * Get Class props
     *
     * @return array
     */
    protected function getInitialPropDefs(): array
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
