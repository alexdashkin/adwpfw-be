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
            $state = $this->getProp('state');
            $states[sanitize_key(str_replace(' ', '_', $this->prefix . '_' . $state))] = $state;
        }

        return $states;
    }
}
