<?php

namespace AlexDashkin\Adwpfw\Items;

use AlexDashkin\Adwpfw\App;
use AlexDashkin\Adwpfw\Exceptions\AdwpfwException;

/**
 * Theme Widget.
 */
class Widget extends Item
{
    /**
     * Constructor.
     *
     * @param App $app
     * @param array $data {
     * @type string $id Defaults to sanitized $title.
     * @type string $title Widget Title. Required.
     * @type callable $render Renders front-end. Required.
     * @type callable $form Renders back-end Widget settings. Required.
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
                'default' => 'widget_' . $this->getDefaultId($data['title']),
            ],
            'title' => [
                'required' => true,
            ],
            'render' => [
                'type' => 'callable',
                'required' => true,
            ],
            'form' => [
                'type' => 'callable',
                'default' => null,
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

        add_action('form_' . $id, [$this, 'form'], 10, 2);
        add_action('render_' . $id, [$this, 'render'], 10, 3);
    }

    /**
     * Render the Widget.
     *
     * @param array $args
     * @param array $instance
     * @param \WP_Widget $widget
     */
    public function render($args, $instance, $widget)
    {
        echo $args['before_widget'];

        echo $args['before_title'];

        echo $this->data['title'];

        echo $args['after_title'];

        echo $this->data['render']($args, $instance, $widget);

        echo $args['after_widget'];
    }

    /**
     * Render Settings form.
     */
    public function form($instance, $widget)
    {
        if ($this->data['form']) {
            echo $this->data['form']($instance, $widget); // todo build form the same way as Metaboxes
        }
    }
}
