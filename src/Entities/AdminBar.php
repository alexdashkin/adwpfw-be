<?php

namespace AlexDashkin\Adwpfw\Entities;

/**
 * Top Admin Bar
 */
class AdminBar
{
    private $data;

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
        $data = array_merge([
            'id' => '',
            'title' => 'Bar',
            'capability' => 'manage_options',
            'href' => '',
            'meta' => [],
        ], $data);

        $data['id'] = $data['id'] ?: sanitize_title($data['title']);

        $this->data = $data;
    }


}
