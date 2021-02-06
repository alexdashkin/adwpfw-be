<?php

namespace AlexDashkin\Adwpfw\Modules;

/**
 * singular*, plural, slug, labels, description, public, columns
 */
class Cpt extends Module
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

        $slug = $this->getPrefixedSlug();

        // Columns
        $this->addHook(sprintf('manage_%s_posts_columns', $slug), [$this, 'colNames']);
        $this->addHook(sprintf('manage_%s_posts_custom_column', $slug), [$this, 'colValues']);

        // Views
        $this->addHook(sprintf('views_edit-%s', $slug), [$this, 'views']);

        // Filters
        $this->addHook('restrict_manage_posts', [$this, 'filters']);
        $this->addHook('pre_get_posts', [$this, 'filterPosts']);
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
     * Custom Admin filters
     *
     * @param string $postType
     * @param string $location
     */
    public function filters(string $postType, string $location)
    {
        // If no filters or not our CPT - return
        if (!($filters = $this->getProp('filters')) || $this->getPrefixedSlug() !== $postType) {
            return;
        }

        // Iterate over filters
        foreach ($filters as $filter) {
            $name = $this->main->prefix($filter['name']);
            $arg = $this->main->prefix($filter['arg']);

            $options = [];

            foreach ($filter['options'] as $value => $label) {
                $options[] = [
                    'value' => $value,
                    'label' => $label,
                    'selected' => array_key_exists($arg, $_REQUEST) && $_REQUEST[$arg] == $value ? 'selected' : '',
                ];
            }

            $args = [
                'name' => $name,
                'label' => $filter['label'],
                'options' => $options,
            ];

            echo $this->main->render('templates/post-actions', $args);
        }
    }

    /**
     * Custom Admin views (along with All, Published, Drafts...)
     *
     * @param array $views
     * @return array
     */
    public function views(array $views): array
    {
        if (!$extraViews = $this->getProp('views')) {
            return $views;
        }

        $slug = $this->getPrefixedSlug();

        foreach ($extraViews as $view) {
            $name = $this->main->prefix($view['name']);
            $arg = $this->main->prefix($view['arg']);
            $class = array_key_exists($arg, $_REQUEST) && $_REQUEST[$arg] === $view['value'] ? 'current' : '';

            $url = add_query_arg(['post_type' => $slug, $arg => $view['value']], 'edit.php');

            $views[$name] = sprintf('<a href="%s" class="%s">%s</a>', $url, $class, $view['label']);
        }

        return $views;
    }

    /**
     * Filter posts in Admin as per view or filter
     *
     * @param \WP_Query $query
     */
    public function filterPosts(\WP_Query $query)
    {
        // Skip if no custom views, not main query or not our cpt
        if (!($views = $this->getProp('views'))
            || !($filters = $this->getProp('filters'))
            || !$query->is_main_query()
            || $this->getPrefixedSlug() !== get_current_screen()->post_type) {
            return;
        }

        // Iterate over views
        foreach ($views as $view) {
            $arg = $this->main->prefix($view['arg']);

            // If view arg not set - skip
            if (!array_key_exists($arg, $_REQUEST) || $_REQUEST[$arg] !== $view['value']) {
                continue;
            }

            // Otherwise - call the callback
            try {
                $view['callback']($query);
            } catch (\Exception $e) {
                $this->log('Exception in CPT "%s": %s', [$this->getProp('name'), $e->getMessage()]);
            }
        }

        // Iterate over filters
        foreach ($filters as $filter) {
            $arg = $this->main->prefix($filter['arg']);

            // If view arg not set - skip
            if (!array_key_exists($arg, $_REQUEST)) {
                continue;
            }

            // Otherwise - call the callback
            try {
                $filter['callback']($query, $_REQUEST[$arg]);
            } catch (\Exception $e) {
                $this->log('Exception in CPT "%s": %s', [$this->getProp('name'), $e->getMessage()]);
            }
        }
    }

    /**
     * Get prefixed slug
     *
     * @return string
     */
    protected function getPrefixedSlug(): string
    {
        return $this->main->prefix($this->getProp('slug'));
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
            'columns' => [],
            'views' => [],
            'filters' => [],
            'labels' => [],
            'rewrite' => function () {
                return ['slug' => sanitize_key(str_replace(' ', '_', $this->getProp('plural')))];
            },
        ];
    }
}
