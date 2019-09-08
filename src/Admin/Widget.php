<?php

namespace AlexDashkin\Adwpfw\Admin;

/**
 * Admin Dashboard widgets
 */
class Widget extends \AlexDashkin\Adwpfw\Common\Base
{
    private $widgets = [];

    public function __construct($app)
    {
        parent::__construct($app);
    }

    public function run()
    {
        add_action('wp_dashboard_setup', [$this, 'registerWidgets'], 1);
    }

    /**
     * Add a Widget
     *
     * @param array $widget {
     * @type string $id
     * @type string $title
     * @type string $capability
     * @type callable $callback Renders the widget
     * }
     */
    public function addWidget($widget)
    {
        $widget = array_merge([
            'id' => '',
            'title' => 'Widget',
            'capability' => 'manage_options',
        ], $widget);

        $widget['id'] = $widget['id'] ?: sanitize_title($widget['title']);

        $this->widgets[] = $widget;
    }

    /**
     * Add multiple Widgets
     *
     * @param array $widgets
     *
     * @see Widget::addWidget()
     */
    public function addWidgets(array $widgets)
    {
        foreach ($widgets as $widget) {
            $this->addWidget($widget);
        }
    }

    public function registerWidgets()
    {
        foreach ($this->widgets as $widget) {
            if (!current_user_can($widget['capability'])) {
                continue;
            }

            wp_add_dashboard_widget($widget['id'], $widget['title'], $widget['callback']);
        }
    }
}
