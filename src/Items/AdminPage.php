<?php

namespace AlexDashkin\Adwpfw\Items;

/**
 * Admin Dashboard sub-menu page
 */
class AdminPage extends Item
{
    /**
     * Constructor
     *
     * @param array $data {
     * @type string $id
     * @type string $title
     * @type string $name
     * @type callable $callback Renders the page
     * }
     */
    public function __construct(array $data)
    {
        $this->defaults = [
            'id' => '',
            'title' => 'Page',
            'name' => 'Page',
            'callback' => '',
        ];

        parent::__construct($data);
    }


}
