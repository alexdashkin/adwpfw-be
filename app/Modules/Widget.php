<?php

namespace AlexDashkin\Adwpfw\Modules;

use AlexDashkin\Adwpfw\Abstracts\Module;

class Widget extends Module
{
    /**
     * Init Module
     */
    public function init()
    {
        $this->hook('widgets_init', [$this, 'register']);
    }

    /**
     * Register the Widget
     */
    public function register()
    {
        $id = $this->gp('prefix') . '_' . $this->gp('id');

        $args = [
            'id' => $id,
            'name' => $this->gp('title'),
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

        echo $this->gp('title');

        echo $args['after_title'];

        echo $this->gp('render')($args, $instance, $widget);

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
        if ($this->gp('form')) {
            echo $this->gp('form')($instance, $widget); // todo build form the same way as Metaboxes
        }
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
