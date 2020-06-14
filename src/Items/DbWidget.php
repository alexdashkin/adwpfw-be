<?php

namespace AlexDashkin\Adwpfw\Items;

use AlexDashkin\Adwpfw\App;
use AlexDashkin\Adwpfw\Exceptions\AdwpfwException;

/**
 * Admin Dashboard Widget.
 */
class DbWidget extends Item
{
    /**
     * Constructor.
     *
     * @param App $app
     * @param array $data {
     * @type string $id Defaults to sanitized $title.
     * @type string $title Widget Title. Required.
     * @type callable $callback Renders the widget. Required.
     * @type string $capability Minimum capability. Default 'read'.
     * }
     *
     * @throws AdwpfwException
     * @see wp_add_dashboard_widget()
     *
     */
    public function __construct(App $app, array $data)
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

        parent::__construct($app, $data, $props);
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
