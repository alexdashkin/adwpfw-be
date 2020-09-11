<?php

namespace AlexDashkin\Adwpfw\Modules;

class Widget extends Module
{
    /**
     * Init Module
     */
    public function init()
    {
        $this->addHook('widgets_init', [$this, 'register']);
    }

    /**
     * Register the Widget
     */
    public function register()
    {
        $id = $this->config('prefix') . '_' . $this->getProp('id');

        $args = [
            'id' => $id,
            'name' => $this->getProp('title'),
        ];

        eval($this->twig('php/widget', $args));

        register_widget($id);

        $this->addHook('form_' . $id, [$this, 'form']);
        $this->addHook('render_' . $id, [$this, 'render']);
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

        echo $this->getProp('title');

        echo $args['after_title'];

        echo $this->getProp('render')($args, $instance, $widget);

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
        if ($this->getProp('form')) {
            echo $this->getProp('form')($instance, $widget); // todo build form the same way as Metaboxes
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
