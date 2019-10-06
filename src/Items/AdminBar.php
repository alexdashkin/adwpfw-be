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
     * @type string $id
     * @type string $title
     * @type int $parent
     * @type string $capability Who can see the Bar
     * @type string $href URL of the link
     * @type bool $group
     * @type array $meta
     * @see \WP_Admin_Bar::add_node()
     * }
     */
    public function __construct(array $data, App $app)
    {
        $this->defaults = [
            'id' => '',
            'title' => 'Bar',
            'parent' => 0,
            'capability' => 'manage_options',
            'href' => '',
            'group' => false,
            'meta' => [],
        ];

        $data['id'] = $data['id'] ?: sanitize_title($data['title']) . uniqid();

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
