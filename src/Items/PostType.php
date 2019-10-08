<?php

namespace AlexDashkin\Adwpfw\Items;

use AlexDashkin\Adwpfw\App;

/**
 * Post State
 */
class PostType extends Item
{
    /**
     * Constructor
     *
     * @param array $data {
     * @param string $slug CPT Slug. Required.
     * }
     *
     * @see register_post_type()
     */
    public function __construct(array $data, App $app)
    {
        $this->props = [
            'slug' => [
                'required' => true,
            ],
            'description' => [
                'default' => null,
            ],
            'labels' => [
                'type' => 'array',
                'default' => [],
            ],
            'public' => [
                'type' => 'bool',
                'default' => true,
            ],
            'hierarchical' => [
                'type' => 'bool',
                'default' => false,
            ],
            'show_in_menu' => [
                'type' => 'bool',
                'default' => true,
            ],
            'supports' => [
                'type' => 'array',
                'default' => [],
            ],
            'rewrite' => [
                'type' => 'array',
                'default' => [],
            ],
        ];

        parent::__construct($data, $app);

        $this->setLabels();
    }

    private function setLabels()
    {
        $labels =& $this->data['labels'];

        $singular = !empty($labels['singular']) ? $labels['singular'] : 'Item';
        $plural = !empty($labels['plural']) ? $labels['plural'] : 'Items';

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

        $labels = array_merge($defaults, $labels);
    }

    /**
     * Register Items in WP
     */
    public function register()
    {
        register_post_type($this->config['prefix'] . '_' . $this->data['slug'], $this->data);
    }
}
