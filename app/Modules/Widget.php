<?php

namespace AlexDashkin\Adwpfw\Modules;

/**
 * title*, render*, form, id, assets[]
 */
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
        $id = $this->prefix . '_' . $this->getProp('id');

        $args = [
            'id' => $id,
            'name' => $this->getProp('title'),
        ];

        // Register the class
        eval($this->main->render('php/widget', $args));

        // Register widget
        register_widget($id);

        // Add render hooks
        $this->addHook('form_' . $id, [$this, 'form']);
        $this->addHook('render_' . $id, [$this, 'render']);

        // Enqueue widget assets
        foreach ($this->getProp('assets') as $index => $asset) {
            // Type here is CSS/JS
            $type = $asset['type'] ?? 'css';

            // Type for particular asset is admin/front
            $asset['type'] = 'front';

            $args = [
                'id' => sprintf('%s-%d', $id, $index),
                'callback' => function () use ($id) {
                    is_active_widget(false, false, $id);
                },
            ];

            $this->m('asset.' . $type, array_merge($args, $asset));
        }
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
     * Render Settings form
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
     * Get Default prop values
     *
     * @return array
     */
    protected function defaults(): array
    {
        return [
            'title' => 'Widget',
            'id' => function () {
                return 'widget_' . sanitize_key(str_replace(' ', '_', $this->getProp('title')));
            },
            'assets' => [],
        ];
    }
}
