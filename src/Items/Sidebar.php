<?php

namespace AlexDashkin\Adwpfw\Items;

use AlexDashkin\Adwpfw\App;
use AlexDashkin\Adwpfw\Exceptions\AdwpfwException;

/**
 * Sidebar
 */
class Sidebar extends Item
{
    /**
     * Constructor
     *
     * @param App $app
     * @param array $data {
     * @type string $id Defaults to sanitized $name.
     * @type string $name Sidebar Title. Required.
     * @type string $description
     * @type string $class CSS class for container.
     * }
     * @see register_sidebar()
     *
     * @throws AdwpfwException
     */
    public function __construct(App $app, array $data)
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

        parent::__construct($app, $data, $props);
    }

    /**
     * Register the Sidebar.
     */
    public function register()
    {
        register_sidebar($this->data);
    }
}
