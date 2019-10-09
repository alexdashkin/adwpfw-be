<?php

namespace AlexDashkin\Adwpfw\Modules;

use AlexDashkin\Adwpfw\App;
use AlexDashkin\Adwpfw\Items\PostState;

/**
 * Add States to the posts/pages (comments displayed on the right in the posts list)
 */
class PostStates extends ItemsModule
{
    /**
     * Constructor
     *
     * @param App $app
     */
    protected function __construct(App $app)
    {
        parent::__construct($app);
    }

    /**
     * Add an Item
     *
     * @param array $data
     * @param App $app
     */
    public function add(array $data, App $app)
    {
        $this->items[] = new PostState($data, $app);
    }

    /**
     * Hooks to register Items in WP
     */
    protected function hooks()
    {
        add_filter('display_post_states', [$this, 'register'], 10, 2);
    }

    public function register($states, $post)
    {
        foreach ($this->items as $item) {
            $states = $item->register($states, $post);
        }

        return $states;
    }
}
