<?php

namespace AlexDashkin\Adwpfw\Items;

use AlexDashkin\Adwpfw\App;
use AlexDashkin\Adwpfw\Exceptions\AdwpfwException;

/**
 * Post State
 */
class PostState extends Item
{
    /**
     * Constructor.
     *
     * @param App $app
     * @param array $data {
     * @type int $post_id Post ID. Required.
     * @type string $state State text. Required.
     * }
     *
     * @throws AdwpfwException
     */
    public function __construct(App $app, array $data)
    {
        $props = [
            'id' => [
                'default' => $this->getDefaultId($data['post_id']),
            ],
            'post_id' => [
                'type' => 'int',
                'required' => true,
            ],
            'state' => [
                'required' => true,
            ],
        ];

        parent::__construct($app, $data, $props);
    }

    /**
     * Filter Post States.
     *
     * @param array $states States list.
     * @param \WP_Post $post Post.
     * @return array Modified States.
     */
    public function register(array $states, $post)
    {
        if ($post->ID === $this->data['post_id']) {
            $states[] = $this->data['state'];
        }

        return $states;
    }
}
