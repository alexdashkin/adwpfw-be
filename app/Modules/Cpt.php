<?php

namespace AlexDashkin\Adwpfw\Modules;

/**
 * Custom Post Type
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

        $slug = $this->getProp('slug');

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
    public function register() // todo list all params
    {
        register_post_type($this->getProp('slug'), $this->getProps());
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

        if (!$column = $this->app->arraySearch($columns, ['name' => $colName], true)) {
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
        if (!($filters = $this->getProp('filters')) || $this->getProp('slug') !== $postType) {
            return;
        }

        // Iterate over filters
        foreach ($filters as $filter) {
            $name = $filter['name'];
            $arg = $filter['arg'];

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

            echo $this->app->render('layouts/post-actions', $args);
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

        $slug = $this->getProp('slug');

        foreach ($extraViews as $view) {
            $name = $view['name'];
            $arg = $view['arg'];
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
        // Skip if no custom views, filters, not in admin, not main query or not our cpt
        $currentScreen = $GLOBALS['current_screen'] ?? null;

        if (!($views = $this->getProp('views'))
            || !($filters = $this->getProp('filters'))
            || !is_admin()
            || !$query->is_main_query()
            || !$currentScreen
            || $this->getProp('slug') !== $currentScreen->post_type) {
            return;
        }

        // Iterate over views
        foreach ($views as $view) {
            $arg = $view['arg'];

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
            $arg = $filter['arg'];

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
            'columns' => [
                'type' => 'array',
                'default' => [],
            ],
            'views' => [
                'type' => 'array',
                'default' => [],
            ],
            'filters' => [
                'type' => 'array',
                'default' => [],
            ],
        ];

        return array_merge($baseProps, $fieldProps);
    }
}
