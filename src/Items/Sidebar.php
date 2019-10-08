<?php

namespace AlexDashkin\Adwpfw\Items;

use AlexDashkin\Adwpfw\App;

/**
 * Sidebar
 */
class Sidebar extends Item
{
    /**
     * Constructor
     *
     * @param array $data {
     * @type string $name Sidebar Title. Required.
     * @type string $slug Defaults to sanitized $name
     * @type string $description
     * @type string $class CSS class for container
     * }
     *
     * @see register_sidebar()
     */
    public function __construct(array $data, App $app)
    {
        $this->props = [
            'name' => [
                'required' => true,
            ],
            'slug' => [
                'default' => $this->getDefaultSlug($data['name']),
            ],
            'description' => [
                'default' => null,
            ],
            'class' => [
                'default' => null,
            ],
        ];

        parent::__construct($data, $app);
    }

    /**
     * Register Items in WP
     */
    public function register()
    {
        register_sidebar($this->data);
    }
}
