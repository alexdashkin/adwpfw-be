<?php

namespace AlexDashkin\Adwpfw\Modules;

/**
 * Custom Taxonomy
 */
class Taxonomy extends Module
{
    /**
     * Init Module
     */
    public function init()
    {
        // Populate labels
        $this->setLabels();

        // Register
        $this->addHook('init', [$this, 'register'], 20);

        $slug = $this->getProp('slug');

        // Columns
        $this->addHook(sprintf('manage_edit-%s_columns', $slug), [$this, 'colNames']);
        $this->addHook(sprintf('manage_%s_custom_column', $slug), [$this, 'colValues']);
    }

    /**
     * Register CPT
     */
    public function register()
    {
        register_taxonomy($this->getProp('slug'), $this->getProp('postTypes'), $this->getProps());
    }

    /**
     * Generate labels from existing $singular and $plural
     */
    private function setLabels()
    {
        $labels = $this->getProp('labels');

        $singular = $labels['singular'] ?? $this->getProp('singular');
        $plural = $labels['plural'] ?? $this->getProp('plural');

        $defaults = [
            'name' => $plural,
            'singular_name' => $singular,
            'add_new' => 'Add New',
            'add_new_item' => 'Add New ' . $singular,
            'edit_item' => 'Edit ' . $singular,
            'new_item' => 'New ' . $singular,
            'all_items' => 'All ' . $plural,
            'view_item' => 'View ' . $singular,
            'search_items' => 'Search ' . $plural,
            'not_found' => "No $plural Found",
            'not_found_in_trash' => "No $plural Found in Trash",
        ];

        $this->setProp('labels', array_merge($defaults, $labels));
    }

    /**
     * Add custom column headings
     *
     * @param array $cols
     * @return array
     */
    public function colNames(array $cols): array
    {
        if (!$columns = $this->getProp('columns')) {
            return $cols;
        }

        $extraCols = [];

        foreach ($columns as $column) {
            $extraCols[$column['name']] = $column['label'];
        }

        return array_merge($cols, $extraCols);
    }

    /**
     * Output custom column
     *
     * @param string $colName
     * @param int $termId
     */
    public function colValues(string $empty, string $colName, $termId)
    {
        $columns = $this->getProp('columns');

        if (!$column = Helpers::arraySearch($columns, ['name' => $colName], true)) {
            return;
        }

        echo $column['callback']($termId);
    }

    /**
     * Get Default prop values
     *
     * @return array
     */
    protected function defaults(): array
    {
        return [
            'plural' => $this->getProp('singular') . 's',
            'post_types' => [],
            'slug' => function () {
                return sanitize_key(str_replace(' ', '_', $this->getProp('singular')));
            },
            'public' => true,
            'labels' => [],
            'columns' => [],
            'rewrite' => function () {
                return ['slug' => sanitize_key(str_replace(' ', '_', $this->getProp('plural')))];
            },
        ];
    }

    /**
     * Get prop definitions
     *
     * @return array
     */
    protected function getPropDefs(): array
    {
        $baseProps = parent::getPropDefs();

        $fieldProps = [
            'singular' => [
                'type' => 'string',
                'required' => true,
            ],
            'plural' => [
                'type' => 'string',
                'default' => function () {
                    return $this->getProp('singular') . 's';
                },
            ],
            'description' => [
                'type' => 'string',
                'default' => '',
            ],
            'public' => [
                'type' => 'bool',
                'default' => true,
            ],
            'slug' => [
                'type' => 'string',
                'default' => function () {
                    return sanitize_key(str_replace(' ', '_', $this->getProp('singular')));
                },
            ],
            'rewrite' => [
                'type' => 'string',
                'default' => function () {
                    return ['slug' => sanitize_key(str_replace(' ', '-', $this->getProp('plural')))];
                },
            ],
            'labels' => [
                'type' => 'array',
                'default' => [],
            ],
            'postTypes' => [
                'type' => 'array',
                'default' => [],
            ],
            'columns' => [
                'type' => 'array',
                'default' => [],
            ],
        ];

        return array_merge($baseProps, $fieldProps);
    }
}
