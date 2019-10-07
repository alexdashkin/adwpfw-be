<?php

namespace AlexDashkin\Adwpfw\Items;

use AlexDashkin\Adwpfw\App;

/**
 * Top Admin Bar Entry
 */
class AdminBar extends Item
{
    /**
     * Constructor
     *
     * @param array $data {
     * @type string $title Bar Title. Required.
     * @type string $slug Defaults to sanitized Title
     * @type int $parent Parent node ID
     * @type string $capability Who can see the Bar
     * @type string $href URL of the link
     * @type bool $group
     * @type array $meta
     * }
     * @see \WP_Admin_Bar::add_node()
     */
    public function __construct(array $data, App $app)
    {
        $this->props = [
            'title' => [
                'required' => true,
            ],
            'slug' => [
                'default' => null,
            ],
            'parent' => [
                'type' => 'int',
                'default' => 0,
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

        parent::__construct($data, $app);
    }

    /**
     * Add hooks
     */
    protected function hooks()
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
        $data = $this->data;

        if (!current_user_can($data['capability'])) {
            return;
        }

        $wpAdminBar->add_node($data);
    }
}
