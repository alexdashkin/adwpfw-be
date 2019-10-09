<?php

namespace AlexDashkin\Adwpfw\Items;

use AlexDashkin\Adwpfw\App;

/**
 * Post State
 */
class PostState extends Item
{
    /**
     * Constructor
     *
     * @param array $data {
     * @param int $post_id Post ID
     * @param string $state State text
     * }
     */
    public function __construct(array $data, App $app)
    {
        $props = [
            'post_id' => [
                'required' => true,
            ],
            'state' => [
                'required' => true,
            ],
        ];

        parent::__construct($data, $app, $props);
    }

    /**
     * Register Items in WP
     */
    public function register($states, $post)
    {
        if ($post->ID === $this->data['post_id']) {
            $states = array_merge($states, $this->data['state']);
        }

        return $states;
    }
}
