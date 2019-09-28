<?php

namespace AlexDashkin\Adwpfw\Entities;

/**
 * Top Admin Bar
 */
class AdminBar extends Entity
{
    /**
     * Add an item to the Top Admin Bar
     *
     * @param array $data {
     * @type string $id
     * @type string $title
     * @type string $capability Who can see the Bar
     * @type string $href URL of the link
     * @type array $meta
     * }
     */
    public function __construct(array $data)
    {
        $this->defaults = [
            'id' => '',
            'title' => 'Bar',
            'capability' => 'manage_options',
            'href' => '',
            'meta' => [],
        ];

        parent::__construct($data);
    }


}
