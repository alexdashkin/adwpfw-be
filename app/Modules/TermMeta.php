<?php

namespace AlexDashkin\Adwpfw\Modules;

/**
 * taxonomy*, title
 */
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
        $taxonomy = $this->getProp('taxonomy');

        $this->addHook($taxonomy . '_edit_form', [$this, 'render']);
        $this->addHook('edited_' . $taxonomy, [$this, 'save']);
    }

    /**
     * Render Section
     *
     * @param \WP_Term $term
     */
    public function render(\WP_Term $term)
    {
        $args = $this->getProps();
        $values = get_term_meta($term->term_id, '_' . $this->prefix . '_' . $this->getProp('id'), true) ?: [];
        $args['fields'] = Field::getArgsForMany($this->fields, $values);

        return $this->app->main->render('templates/term-meta', $args);
    }

    /**
     * Save Term fields
     *
     * @param int $termId Term ID
     */
    public function save(int $termId)
    {
        $id = $this->getProp('id');
        $prefix = $this->prefix;
        $metaKey = '_' . $prefix . '_' . $id;

        if (empty($_POST[$prefix][$id])) {
            return;
        }

        $form = $_POST[$prefix][$id];

        $values = [];

        foreach ($this->fields as $field) {
            $fieldName = $field->getProp('name');

            if (empty($fieldName) || !array_key_exists($fieldName, $form)) {
                continue;
            }

            $values[$fieldName] = $field->sanitize($form[$fieldName]);
        }

        update_term_meta($termId, $metaKey, $values);

        do_action('adwpfw_term_saved', $this, $values);
    }

    /**
     * Get Default prop values
     *
     * @return array
     */
    protected function defaults(): array
    {
        return [
            'title' => 'Custom',
            'taxonomy' => 'category',
        ];
    }
}
