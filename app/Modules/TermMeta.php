<?php

namespace AlexDashkin\Adwpfw\Modules;

use AlexDashkin\Adwpfw\Fields\Field;

class TermMeta extends Module
{
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
        $this->validateData();

        $taxonomy = $this->gp('taxonomy');

        $this->hook($taxonomy . '_edit_form', [$this, 'render']);
        $this->hook('edited_' . $taxonomy, [$this, 'save']);
    }

    /**
     * Render Section
     *
     * @param \WP_Term $term
     */
    public function render(\WP_Term $term)
    {
        $values = get_term_meta($term->term_id, '_' . $this->gp('prefix') . '_' . $this->gp('id'), true) ?: [];

        $fields = [];

        foreach ($this->fields as $field) {
            $fieldName = $field->gp('name');

            $twigArgs = $field->getTwigArgs($values[$fieldName] ?? null);

            $fields[] = $twigArgs;
        }

        $args = [
            'fields' => $fields,
        ];

        echo $this->twig('templates/term-meta', $args);
    }

    /**
     * Save Term fields
     *
     * @param int $termId Term ID
     */
    public function save(int $termId)
    {
        $id = $this->gp('id');
        $prefix = $this->gp('prefix');
        $metaKey = '_' . $prefix . '_' . $id;

        if (empty($_POST[$prefix][$id])) {
            return;
        }

        $form = $_POST[$prefix][$id];

        $values = [];

        foreach ($this->fields as $field) {
            $fieldName = $field->gp('name');

            if (empty($fieldName) || !array_key_exists($fieldName, $form)) {
                continue;
            }

            $values[$fieldName] = $field->sanitize($form[$fieldName]);
        }

        update_term_meta($termId, $metaKey, $values);

        do_action('adwpfw_term_saved', $this, $values);
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
            'id' => [
                'required' => true,
            ],
            'taxonomy' => [
                'required' => true,
            ],
            'heading' => [
                'default' => '',
            ],
        ];
    }
}
