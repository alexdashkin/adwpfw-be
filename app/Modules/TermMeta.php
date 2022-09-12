<?php

namespace AlexDashkin\Adwpfw\Modules;

use AlexDashkin\Adwpfw\{Fields\Field, Modules\Assets\Asset};

/**
 * Taxonomy Custom Meta Box
 */
class TermMeta extends FieldHolder
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
        $taxonomy = $this->getProp('taxonomy');

        $this->addHook($taxonomy . '_edit_form', [$this, 'render']);
        $this->addHook('edited_' . $taxonomy, [$this, 'save']);
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
     * Render Section
     *
     * @param \WP_Term $term
     * @return string
     */
    public function render(\WP_Term $term): string
    {
        // Enqueue assets
        foreach ($this->assets as $asset) {
            $asset->enqueue();
        }

        // Prepare args
        $args = [
            'title' => $this->getProp('title'),
            'fields' => $this->getFieldsArgs($term->term_id),
        ];

        // Render template
        return $this->app->render('layouts/term-meta', $args);
    }

    /**
     * Save Term fields
     *
     * @param int $termId Term ID
     */
    public function save(int $termId)
    {
        Field::setMany($this->fields, $_POST, $termId);
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
        return $this->getTermMeta($objectId, $field->getProp('name'));
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
        return $this->updateTermMeta($objectId, $field->getProp('name'), $value);
    }

    /**
     * Get prop definitions
     *
     * @return array
     */
    protected function getPropDefs(): array
    {
        return [
            'taxonomy' => [
                'type' => 'string',
                'required' => true,
            ],
            'title' => [
                'type' => 'string',
                'default' => '',
            ],
        ];
    }
}
