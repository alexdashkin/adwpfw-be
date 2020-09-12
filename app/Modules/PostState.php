<?php

namespace AlexDashkin\Adwpfw\Modules;

/**
 * post_id*, state*
 */
class PostState extends Module
{
    /**
     * Init Module
     */
    public function init()
    {
        $this->addHook('display_post_states', [$this, 'register']);
    }

    /**
     * Register Post States
     *
     * @param array $states States list
     * @param \WP_Post $post Post
     * @return array Modified States
     */
    public function register(array $states, \WP_Post $post): array
    {
        if ($post->ID === $this->getProp('post_id')) {
            $states[] = $this->getProp('state');
        }

        return $states;
    }
}
