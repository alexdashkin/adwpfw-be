<?php

namespace AlexDashkin\Adwpfw\Modules\WithItems;

use AlexDashkin\Adwpfw\App;
use AlexDashkin\Adwpfw\Items\Basic\PostType;

/**
 * Custom Post Types.
 */
class PostTypes extends ModuleWithItems
{
    /**
     * Constructor.
     *
     * @param App $app
     */
    public function __construct(App $app)
    {
        parent::__construct($app);
    }

    /**
     * Add Post Type.
     *
     * @param array $data
     *
     * @see PostType::__construct();
     *
     * @throws \AlexDashkin\Adwpfw\Exceptions\AdwpfwException
     */
    public function add(array $data)
    {
        $this->items[] = new PostType($data, $this->app);
    }

    /**
     * Hooks to register Items in WP
     */
    protected function init()
    {
        add_action('init', [$this, 'register'], 20);
    }

    /**
     * Register Post Types in WP
     */
    public function register()
    {
        foreach ($this->items as $item) {
            $item->register();
        }
    }
}
