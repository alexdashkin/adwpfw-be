<?php

namespace AlexDashkin\Adwpfw\Items;

use AlexDashkin\Adwpfw\App;
use AlexDashkin\Adwpfw\Exceptions\AdwpfwException;

/**
 * Theme Widget
 */
class Widget extends Item
{
    private $widget;

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
            'options' => [
                'type' => 'array',
                'default' => [],
            ],
        ];

        parent::__construct($app, $data, $props);
    }

    /**
     * Register the Widget.
     */
    public function register()
    {
        $id = $this->prefix . '_' . $this->data['id'];

        $args = [
            'id' => $id,
            'name' => $this->data['title'],
        ];

        eval($this->m('Twig')->renderFile('php/widget', $args));

        register_widget($id);

        add_action($id, [$this, 'render'], 10, 2);
    }

    /**
     * Render the Widget.
     */
    public function render($args, $instance)
    {
        echo $args['before_widget'];

        echo $args['before_title'];
        echo $this->data['title'];
        echo $args['after_title'];

        echo $this->data['callback']();

        echo $args['after_widget'];
    }

    /**
     * Get default Widget ID.
     * Not working with uniqid() as on subsequent calls it's different
     * and the Widget disappears
     *
     * @param string $base
     * @return string
     */
    protected function getDefaultId($base)
    {
        return 'widget_' . esc_attr(sanitize_key(str_replace(' ', '_', $base)));
    }
}
