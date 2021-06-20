<?php

namespace AlexDashkin\Adwpfw\Modules;

use AlexDashkin\Adwpfw\Modules\Fields\Contexts\Term;
use AlexDashkin\Adwpfw\Modules\Fields\Field;

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
        $field->setProp('context', new Term($field, $this->main));

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

        $args['fields'] = Field::renderMany($this->fields, $term->term_id);

        echo $this->main->render('templates/term-meta', $args);
    }

    /**
     * Save Term fields
     *
     * @param int $termId Term ID
     */
    public function save(int $termId)
    {
        if (empty($_POST[$this->prefix])) {
            return;
        }

        $values = $_POST[$this->prefix];

        Field::setMany($this->fields, $values, $termId);

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
            'title' => '',
            'taxonomy' => 'category',
        ];
    }
}
