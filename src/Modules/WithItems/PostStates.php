<?php

namespace AlexDashkin\Adwpfw\Modules\WithItems;

use AlexDashkin\Adwpfw\App;
use AlexDashkin\Adwpfw\Items\Basic\PostState;

/**
 * Add States to the posts/pages (comments displayed on the right in the posts list).
 */
class PostStates extends ModuleWithItems
{
    /**
     * Constructor
     *
     * @param App $app
     */
    public function __construct(App $app)
    {
        parent::__construct($app);
    }

    /**
     * Add Post State.
     *
     * @param array $data
     * @param App $app
     *
     * @throws \AlexDashkin\Adwpfw\Exceptions\AdwpfwException
     *
     * @see PostState::__construct();
     */
    public function add(array $data, App $app)
    {
        $this->items[] = new PostState($data, $app);
    }

    /**
     * Hooks to register Items in WP
     */
    protected function init()
    {
        add_filter('display_post_states', [$this, 'register'], 10, 2);
    }

    /**
     * Filter states and add ours.
     *
     * @param array $states States list.
     * @param \WP_Post $post Post.
     * @return array Modified States.
     */
    public function register($states, $post)
    {
        foreach ($this->items as $item) {
            $states = $item->register($states, $post);
        }

        return $states;
    }
}
