<?php

namespace AlexDashkin\Adwpfw\Items;

use AlexDashkin\Adwpfw\App;

/**
 * Admin Dashboard widget
 */
class Widget extends Item
{
    /**
     * Constructor
     *
     * @param array $data {
     * @type string $title Widget Title. Required.
     * @type callable $callback Renders the widget. Required.
     * @type string $slug Defaults to prefixed sanitized title.
     * @type string $capability Who can see the Widget
     * }
     *
     * @see wp_add_dashboard_widget()
     */
    public function __construct(array $data, App $app)
    {
        $props = [
            'title' => [
                'required' => true,
            ],
            'callback' => [
                'type' => 'callable',
                'required' => true,
            ],
            'slug' => [
                'default' => $this->getDefaultSlug($data['title']),
            ],
            'capability' => [
                'default' => 'read',
            ],
        ];

        parent::__construct($data, $app, $props);
    }

    /**
     * Register Items in WP
     */
    public function register()
    {
        if (!current_user_can($this->data['capability'])) {
            return;
        }

        wp_add_dashboard_widget($this->data['slug'], $this->data['title'], $this->data['callback']);
    }
}
