<?php

namespace AlexDashkin\Adwpfw\Modules;

use AlexDashkin\Adwpfw\{Fields\Field, Helpers, Modules\Assets\Asset};

/**
 * Post Meta Box
 */
class Metabox extends FieldHolder
{
    /**
     * @var Asset[]
     */
    protected $assets = [];

    /**
     * Init Module
     */
    public function init()
    {
        $this->addHook('add_meta_boxes', [$this, 'register'], 20);
        $this->addHook('save_post', [$this, 'save']);
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
     * Register the Page
     */
    public function register()
    {
        // Do not render if callback returns false
        $callback = $this->getProp('callback');

        if ($callback && is_callable($callback) && !$callback()) {
            return;
        }

        add_meta_box(
            $this->getProp('id'),
            $this->getProp('title'),
            [$this, 'render'],
            $this->getProp('screen'),
            $this->getProp('context'),
            $this->getProp('priority')
        );
    }

    /**
     * Render the Metabox
     *
     * @param \WP_Post $post
     */
    public function render(\WP_Post $post)
    {
        // Enqueue assets
        foreach ($this->assets as $asset) {
            $asset->enqueue();
        }

        // Prepare args
        $args = [
            'context' => $this->getProp('context'),
            'fields' => $this->getFieldsArgs($post->ID),
        ];

        // Output template
        echo Helpers::render('layouts/metabox', $args);
    }

    /**
     * Save the posted data
     *
     * @param int $postId
     */
    public function save(int $postId)
    {
        Field::setMany($this->fields, $_POST, $postId);
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
        return get_post_meta($objectId, $field->getProp('name'), true);
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
        return update_post_meta($objectId, $field->getProp('name'), $value);
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
            'id' => [
                'type' => 'string',
                'default' => function () {
                    return sanitize_key(str_replace(' ', '-', sprintf('%s-%s', $this->getProp('title'), implode('-', $this->getProp('screen')))));
                },
            ],
            'screen' => [
                'type' => 'array',
                'default' => ['post'],
            ],
            'assets' => [
                'type' => 'array',
                'default' => [],
            ],
            'context' => [
                'type' => 'string',
                'default' => 'normal',
            ],
        ];
    }
}
