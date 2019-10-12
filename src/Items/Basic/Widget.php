<?php

namespace AlexDashkin\Adwpfw\Items\Basic;

use AlexDashkin\Adwpfw\App;

/**
 * Admin Dashboard Widget
 */
class Widget extends Item
{
    /**
     * Constructor.
     *
     * @param array $data {
     * @type string $id Defaults to sanitized $title.
     * @type string $title Widget Title. Required.
     * @type callable $callback Renders the widget. Required.
     * @type string $capability Minimum capability. Default 'read'.
     * }
     *
     * @see wp_add_dashboard_widget()
     * @throws \AlexDashkin\Adwpfw\Exceptions\AdwpfwException
     */
    public function __construct(array $data, App $app)
    {
        $props = [
            'id' => [
                'default' => $this->getDefaultId($data['title']),
            ],
            'title' => [
                'required' => true,
            ],
            'callback' => [
                'type' => 'callable',
                'required' => true,
            ],
            'capability' => [
                'default' => 'read',
            ],
        ];

        parent::__construct($data, $app, $props);
    }

    /**
     * Register the Widget.
     */
    public function register()
    {
        if (!current_user_can($this->data['capability'])) {
            return;
        }

        wp_add_dashboard_widget($this->data['id'], $this->data['title'], $this->data['callback']);
    }
}
