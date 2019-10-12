<?php

namespace AlexDashkin\Adwpfw\Items\Basic;

use AlexDashkin\Adwpfw\App;

/**
 * Post Type
 */
class PostType extends Item
{
    /**
     * Constructor
     *
     * @param array $data {
     * @param string $id ID. Defaults to sanitized $label.
     * @param string $label Name shown in the menu. Usually plural.
     * @param string $description A short descriptive summary of what the post type is.
     * @param array $labels $singular and $plural are required, rest are auto-populated.
     * @param bool $public Whether to show in Admin. Default true.
     * }
     *
     * @see register_post_type()
     *
     * @throws \AlexDashkin\Adwpfw\Exceptions\AdwpfwException
     */
    public function __construct(array $data, App $app)
    {
        $props = [
            'id' => [
                'default' => $this->getDefaultId($data['label']),
            ],
            'label' => [
                'required' => true,
            ],
            'description' => [
                'default' => null,
            ],
            'labels' => [
                'type' => 'array',
                'required' => true,
            ],
            'public' => [
                'type' => 'bool',
                'default' => true,
            ],
        ];

        parent::__construct($data, $app, $props);

        $this->setLabels();
    }

    /**
     * Generate labels from existing $singular and $plural
     */
    private function setLabels()
    {
        $labels = $this->data['labels'];

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

        $this->data['labels'] = array_merge($defaults, $labels);
    }

    /**
     * Register CPT
     */
    public function register()
    {
        register_post_type($this->prefix . '_' . $this->data['id'], $this->data);
    }
}
