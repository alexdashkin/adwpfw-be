<?php

namespace AlexDashkin\Adwpfw\Modules;

use AlexDashkin\Adwpfw\{Fields\Field, Modules\Assets\Asset, Modules\RestApi\AdminAjax};

/**
 * Admin Page Tab
 */
class AdminPageTab extends FieldHolder
{
    /**
     * @var AdminPage
     */
    protected $parent;

    /**
     * @var Asset[]
     */
    protected $assets = [];

    /**
     * Init Module
     */
    public function init()
    {
        if ($this->getProp('form')) {
            new AdminAjax([
                'action' => $this->getProp('action'),
                'fields' => [
                    'form' => [
                        'type' => 'form',
                        'required' => true,
                    ],
                ],
                'callback' => [$this, 'save'],
            ], $this->app);
        }
    }

    /**
     * Enqueue tab assets
     */
    public function enqueueAssets()
    {
        foreach ($this->assets as $asset) {
            $asset->enqueue();
        }
    }

    /**
     * Set Parent Page
     *
     * @param AdminPage $parent
     */
    public function setParent(AdminPage $parent)
    {
        $this->parent = $parent;
    }

    /**
     * Add Asset
     *
     * @param Asset $asset
     */
    public function addAsset(Asset $asset)
    {
        $asset->setProp('enqueue', '__return_false');

        $this->assets[] = $asset;
    }

    /**
     * Render Tab
     *
     * @return string
     */
    public function render(): string
    {
        // Enqueue assets
        /*        foreach ($this->assets as $asset) {
                    $asset->enqueue();
                }*/

        // Prepare args
        $args = [
            'title' => $this->getProp('title'),
            'action' => $this->prefixIt($this->getProp('action')),
            'form' => $this->getProp('form'),
            'fields' => $this->getFieldsArgs(),
        ];

        // Render template
        return $this->app->render('layouts/admin-page-tab', $args);
    }

    /**
     * Save the posted data
     *
     * @param array $request
     * @return array
     */
    public function save(array $request): array
    {
        Field::setMany($this->fields, $request['form']);

        do_action('adwpfw_settings_saved', $this, $request['form']);

        return $this->app->returnSuccess('Saved');
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
        return $this->getOption($field->getProp('name'));
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
        return $this->updateOption($field->getProp('name'), $value);
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
            'slug' => [
                'type' => 'string',
                'default' => function () {
                    return sanitize_key(str_replace(' ', '-', $this->getProp('title')));
                },
            ],
            'action' => [
                'type' => 'string',
                'default' => function () {
                    return sprintf('save_%s', $this->getProp('slug'));
                },
            ],
            'form' => [
                'type' => 'bool',
                'default' => false,
            ],
            'option' => [
                'type' => 'string',
                'default' => function () {
                    return $this->getProp('form');
                },
            ],
        ];
    }
}
