<?php

namespace AlexDashkin\Adwpfw\Modules;

use AlexDashkin\Adwpfw\App;
use AlexDashkin\Adwpfw\Exceptions\AdwpfwException;
use AlexDashkin\Adwpfw\Items\PostState;

/**
 * Add States to the posts/pages (comments displayed on the right in the posts list).
 */
class PostStates extends ModuleWithItems
{
    /**
     * @var PostState[]
     */
    protected $items = [];

    /**
     * Constructor
     *
     * @param App $app
     */
    public function __construct(App $app)
    {
        parent::__construct($app);

        add_filter('display_post_states', [$this, 'register'], 10, 2);
    }

    /**
     * Add Post State.
     *
     * @param array $data . Data to pass to PostState constructor.
     *
     * @throws AdwpfwException
     * @see PostState::__construct();
     *
     */
    public function add(array $data)
    {
        $this->items[] = new PostState($this->app, $data);
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
