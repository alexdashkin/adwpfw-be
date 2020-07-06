<?php

namespace AlexDashkin\Adwpfw\Items;

use AlexDashkin\Adwpfw\Abstracts\Module;
use AlexDashkin\Adwpfw\App;

class Widget extends Module
{
    /**
     * Init Module
     */
    public function init()
    {
        App::get(
            'hook',
            [
                'tag' => 'widgets_init',
                'callback' => [$this, 'register'],
            ]
        );
    }

    /**
     * Register the Widget
     */
    public function register()
    {
        $id = $this->get('prefix') . '_' . $this->get('id');

        $args = [
            'id' => $id,
            'name' => $this->get('title'),
        ];

        eval($this->twig('php/widget', $args));

        register_widget($id);

        $this->hook('form_' . $id, [$this, 'form']);
        $this->hook('render_' . $id, [$this, 'render']);
    }

    /**
     * Render the Widget
     *
     * @param array $args
     * @param array $instance
     * @param \WP_Widget $widget
     */
    public function render(array $args, array $instance, \WP_Widget $widget)
    {
        echo $args['before_widget'];

        echo $args['before_title'];

        echo $this->get('title');

        echo $args['after_title'];

        echo $this->get('render')($args, $instance, $widget);

        echo $args['after_widget'];
    }

    /**
     * Render Settings form.
     *
     * @param array $instance
     * @param \WP_Widget $widget
     */
    public function form(array $instance, \WP_Widget $widget)
    {
        if ($this->get('form')) {
            echo $this->get('form')($instance, $widget); // todo build form the same way as Metaboxes
        }
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
            'render' => [
                'type' => 'callable',
                'required' => true,
            ],
            'id' => [
                'default' => function ($data) {
                    return 'widget_' . sanitize_key(str_replace(' ', '_', $data['title']));
                },
            ],
            'form' => [
                'type' => 'callable',
                'default' => null,
            ],
        ];
    }
}
