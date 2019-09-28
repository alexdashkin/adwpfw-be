<?php

namespace AlexDashkin\Adwpfw\Admin;

/**
 * Add States to the posts/pages
 * (a comment displayed on the right in the posts list)
 */
class PostStates extends \AlexDashkin\Adwpfw\Common\Base
{
    private $states = [];

    public function __construct($app)
    {
        parent::__construct($app);
    }

    public function run()
    {
        add_filter('display_post_states', [$this, 'displayStates'], 10, 2);
    }

    /**
     * Add a post state
     *
     * @param int $postId
     * @param string $state State text
     */
    public function addState($postId, $state)
    {
        $this->states[$postId][] = $state;
    }

    public function displayStates($states, $post)
    {
        if (array_key_exists($post->ID, $this->states)) {
            $states = array_merge($states, $this->states[$post->ID]);
        }

        return $states;
    }
}
