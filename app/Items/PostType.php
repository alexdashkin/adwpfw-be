<?php

namespace AlexDashkin\Adwpfw\Items;

use AlexDashkin\Adwpfw\Abstracts\Module;

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
        $this->hook('init', [$this, 'register'], 20);
    }

    /**
     * Register CPT
     */
    public function register()
    {
        register_post_type($this->get('prefix') . '_' . $this->get('slug'), $this->data);
    }

    /**
     * Generate labels from existing $singular and $plural
     */
    private function setLabels()
    {
        $labels = $this->get('labels');

        $singular = !empty($labels['singular']) ? $labels['singular'] : $this->get('singular');
        $plural = !empty($labels['plural']) ? $labels['plural'] : $this->get('plural');

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

        $this->set('labels', array_merge($defaults, $labels));
    }

    /**
     * Get Class props
     *
     * @return array
     */
    protected function props(): array
    {
        return [
            'prefix' => [
                'required' => true,
            ],
            'singular' => [
                'required' => true,
            ],
            'plural' => [
                'default' => function ($data) {
                    return $data['singular'] . 's';
                },
            ],
            'slug' => [
                'default' => function ($data) {
                    return sanitize_key(str_replace(' ', '_', $data['singular']));
                },
            ],
            'labels' => [
                'type' => 'array',
                'default' => [],
            ],
            'description' => [
                'default' => '',
            ],
            'public' => [
                'type' => 'bool',
                'default' => true,
            ],
        ];
    }
}
