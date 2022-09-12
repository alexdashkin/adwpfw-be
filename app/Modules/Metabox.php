<?php

namespace AlexDashkin\Adwpfw\Modules;

use AlexDashkin\Adwpfw\{Fields\Field, Modules\Assets\Asset};

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
        $enqueue = function () {
            return in_array(get_current_screen()->id, $this->getProp('screen'));
        };

        $asset->setProp('enqueue', $enqueue);

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
            $this->prefixIt($this->getProp('id')),
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
        // Prepare args
        $args = [
            'context' => $this->getProp('context'),
            'fields' => $this->getFieldsArgs($post->ID),
        ];

        // Output template
        echo $this->app->render('layouts/metabox', $args);
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
        return $this->getPostMeta($objectId, $field->getProp('name'));
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
        return $this->updatePostMeta($objectId, $field->getProp('name'), $value);
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
