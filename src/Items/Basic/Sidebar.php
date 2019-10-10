<?php

namespace AlexDashkin\Adwpfw\Items\Basic;

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
     * @type string $id Defaults to sanitized $name.
     * @type string $name Sidebar Title. Required.
     * @type string $description
     * @type string $class CSS class for container.
     * }
     * @see register_sidebar()
     * @throws \AlexDashkin\Adwpfw\Exceptions\AdwpfwException
     */
    public function __construct(array $data, App $app)
    {
        $props = [
            'id' => [
                'default' => $this->getDefaultId($data['name']),
            ],
            'name' => [
                'required' => true,
            ],
            'description' => [
                'default' => null,
            ],
            'class' => [
                'default' => null,
            ],
        ];

        parent::__construct($data, $app, $props);
    }

    /**
     * Register the Sidebar.
     */
    public function register()
    {
        register_sidebar($this->data);
    }
}
