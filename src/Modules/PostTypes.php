<?php

namespace AlexDashkin\Adwpfw\Admin;

/**
 * Custom Post Types
 */
class PostTypes extends \AlexDashkin\Adwpfw\Common\Base
{
    private $postTypes = [];

    public function __construct($app)
    {
        parent::__construct($app);
    }

    public function run()
    {
        add_action('init', [$this, 'register'], 20, 0);
    }

    /**
     * Add a Custom Post Type
     *
     * @param array $postType
     *
     * @see register_post_type()
     */
    public function addPostType(array $postType)
    {
        $postType = array_merge([
            'labels' => [],
            'description' => 'My New Post Type',
            'public' => true,
        ], $postType);

        $postType['labels'] = $this->getLabels($postType['labels']);

        $this->postTypes[] = $postType;
    }

    /**
     * Add multiple Post Types
     *
     * @param array $postTypes
     *
     * @see PostTypes::addPostType()
     */
    public function addPostTypes(array $postTypes)
    {
        foreach ($postTypes as $postType) {
            $this->addPostType($postType);
        }
    }

    public function getLabels($labels)
    {
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

        return array_merge($defaults, $labels);
    }

    public function register()
    {
        foreach ($this->postTypes as $postType) {
            register_post_type($this->config['prefix'] . '_' . $postType['name'], $postType);
        }
    }
}
