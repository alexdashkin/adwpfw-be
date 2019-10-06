<?php

namespace AlexDashkin\Adwpfw\Items;

/**
 * Top Admin Bar Entry
 */
class AdminBar extends Item
{
    /**
     * Constructor
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
