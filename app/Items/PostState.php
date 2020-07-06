<?php

namespace AlexDashkin\Adwpfw\Items;

use AlexDashkin\Adwpfw\Abstracts\Module;

class PostState extends Module
{
    /**
     * Init Module
     */
    public function init()
    {
        $this->hook('display_post_states', [$this, 'register']);
    }

    /**
     * Register Post States
     *
     * @param array $states States list
     * @param \WP_Post $post Post
     * @return array Modified States
     */
    public function register(array $states, $post)
    {
        if ($post->ID === $this->get('post_id')) {
            $states[] = $this->get('state');
        }

        return $states;
    }

    /**
     * Get Class props
     *
     * @return array
     */
    protected function props(): array
    {
        return [
            'post_id' => [
                'type' => 'int',
                'required' => true,
            ],
            'state' => [
                'required' => true,
            ],
        ];
    }
}
