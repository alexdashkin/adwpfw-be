<?php

namespace AlexDashkin\Adwpfw\Items\Basic;

use AlexDashkin\Adwpfw\App;

/**
 * Post State
 */
class PostState extends Item
{
    /**
     * Constructor.
     *
     * @param array $data {
     * @param int $post_id Post ID.
     * @param string $state State text.
     * }
     * @throws \AlexDashkin\Adwpfw\Exceptions\AdwpfwException
     */
    public function __construct(array $data, App $app)
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

        parent::__construct($data, $app, $props);
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
