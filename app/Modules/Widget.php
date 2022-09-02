<?php

namespace AlexDashkin\Adwpfw\Modules;

use AlexDashkin\Adwpfw\{Fields\Field, Modules\Assets\Asset};

/**
 * Custom Front-End Widget
 */
class Widget extends FieldHolder
{
    /**
     * @var Asset[]
     */
    protected $assets = [];

    /**
     * @var \WP_Widget
     */
    private $widget;

    /**
     * Admin Form values
     *
     * @var array
     */
    private $instance;

    /**
     * Init Module
     */
    public function init()
    {
        $this->addHook('widgets_init', [$this, 'register']);
    }

    /**
     * Add Asset
     *
     * @param Asset $asset
     */
    public function addAsset(Asset $asset)
    {
        $this->assets[] = $asset;
    }

    /**
     * Register the Widget
     */
    public function register()
    {
        $id = $this->prefixIt($this->getProp('id'));

        $args = [
            'id' => $id,
            'name' => $this->getProp('title'),
            'description' => $this->getProp('description'),
        ];

        // Register the class
        eval($this->app->render('php/widget', $args));

        // Register widget
        register_widget($id);

        // Add render hooks
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
        // Enqueue assets
        foreach ($this->assets as $asset) {
            $asset->enqueue();
        }

        // Call render function and output the result
        try {
            $feHtml = $this->getProp('render')($args, $instance, $widget);
            echo sprintf('<div class="adwpfw-widget">%s</div>', $feHtml);
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
        $this->instance = $instance;
        $this->widget = $widget;

        $args = [
            'fields' => $this->getFieldsArgs(),
        ];

        echo $this->app->render('layouts/widget', $args);
    }

    /**
     * Get field "name" attr for template
     *
     * @param Field $field
     * @return string
     */
    public function getFieldName(Field $field): string
    {
        return $this->widget->get_field_name($this->prefixIt($field->getProp('name')));
    }

    /**
     * Get field value
     *
     * @param Field $field
     * @param int $objectId
     * @return mixed
     */
    public function getFieldValue(Field $field, int $objectId = 0)
    {
        return $this->instance[$field->getProp('name')] ?? '';
    }

    /**
     * Set field value
     *
     * @param Field $field
     * @param $value
     * @param int $objectId
     * @return bool
     */
    public function setFieldValue(Field $field, $value, int $objectId = 0): bool
    {
        return false;
    }

    /**
     * Get prop definitions
     *
     * @return array
     */
    protected function getPropDefs(): array
    {
        return [
            'title' => [
                'type' => 'string',
                'required' => true,
            ],
            'render' => [
                'type' => 'callable',
                'required' => true,
            ],
            'description' => [
                'type' => 'string',
                'default' => '',
            ],
            'id' => [
                'type' => 'string',
                'default' => function () {
                    return 'widget_' . sanitize_key(str_replace(' ', '_', $this->getProp('title')));
                },
            ],
            'assets' => [
                'type' => 'array',
                'default' => [],
            ],
        ];
    }
}
