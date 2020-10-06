<?php

namespace AlexDashkin\Adwpfw\Modules;

/**
 * singular*, plural, slug, labels, description, public, columns
 */
class PostType extends Module
{
    /**
     * Init Module
     */
    public function init()
    {
        // Populate labels
        $this->setLabels();

        // Register CPT
        $this->addHook('init', [$this, 'register'], 20);

        $slug = $this->prefix . '_' . $this->getProp('slug');

        // Extra columns
        $this->addHook(sprintf('manage_%s_posts_columns', $slug), [$this, 'colNames']);
        $this->addHook(sprintf('manage_%s_posts_custom_column', $slug), [$this, 'colValues']);
    }

    /**
     * Register CPT
     */
    public function register()
    {
        register_post_type($this->prefix . '_' . $this->getProp('slug'), $this->getProps());
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
     * @param int $postId
     */
    public function colValues(string $colName, int $postId)
    {
        $columns = $this->getProp('columns');

        if (!$column = $this->main->arraySearch($columns, ['name' => $colName], true)) {
            return;
        }

        echo $column['callback']($postId);
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
            'slug' => function () {
                return sanitize_key(str_replace(' ', '_', $this->getProp('singular')));
            },
            'public' => true,
            'labels' => [],
        ];
    }
}
