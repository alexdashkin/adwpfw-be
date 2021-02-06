<?php

namespace AlexDashkin\Adwpfw\Modules;

use AlexDashkin\Adwpfw\Modules\Fields\Field;

/**
 * title*, render*, form, id, assets[]
 */
class Widget extends Module
{
    private $frontHandles = [];

    /**
     * @var Field[]
     */
    protected $fields = [];

    /**
     * Add Field
     *
     * @param Field $field
     */
    public function addField(Field $field)
    {
        $this->fields[] = $field;
    }

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
            $af = $asset['af'] ?: 'front';
            $asset['type'] = $af;

            $args = [
                'id' => sprintf('%s-%s-%s-%d', $id, $type, $af, $index),

                // Do not enqueue front JS (to be done in render callback)
                'enqueue' => !('js' === $type && 'front' === $af),
            ];

            if ('admin' === $asset['type']) {
                $args['callback'] = function () {
                    return 'widgets' === get_current_screen()->id;
                };
            }

            // Add asset
            $asset = $this->m('asset.' . $type, array_merge($args, $asset));

            // Add handle to the list for front scripts to enqueue in render callback
            if (!$args['enqueue']) {
                $this->frontHandles[] = $asset->getProp('handle');
            }
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
        // Enqueue front scripts
        if (!is_admin()) {
            foreach ($this->frontHandles as $handle) {
                wp_enqueue_script($handle);
            }
        }

        try {
            echo sprintf('<div class="%s-widget">%s</div>', $this->prefix, $this->getProp('render')($args, $instance, $widget));
        } catch (\Exception $e) {
            $this->log('Exception in widget "%s": %s', [$this->getProp('id'), $e->getMessage()]);
        }
    }

    /**
     * Render Settings form
     *
     * @param array $instance
     * @param \WP_Widget $widget
     */
    public function form(array $instance, \WP_Widget $widget)
    {
        $args = $this->getProps();

        $fields = [];

        foreach ($this->fields as $field) {
            $context = new Fields\Contexts\Widget($field, $this->main);
            $context->setWidget($widget);
            $field->setProp('context', $context);

            $name = $field->getProp('name');
            $field->setProp('value', $instance[$name] ?? '');
            $fieldArgs = $field->getProps();
            $fieldArgs['content'] = $field->render();
            $fields[] = $fieldArgs;
        }

        $args['fields'] = $fields;

        echo $this->main->render('templates/widget', $args);
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
