<?php

namespace AlexDashkin\Adwpfw\Modules;

use AlexDashkin\Adwpfw\App;
use AlexDashkin\Adwpfw\Exceptions\AdwpfwException;
use AlexDashkin\Adwpfw\Items\PostType;

/**
 * Custom Post Types.
 */
class PostTypes extends ModuleWithItems
{
    /**
     * @var PostType[]
     */
    protected $items = [];

    /**
     * Constructor.
     *
     * @param App $app
     */
    public function __construct(App $app)
    {
        parent::__construct($app);

        add_action('init', [$this, 'register'], 20);
    }

    /**
     * Add Post Type.
     *
     * @param array $data. Data to pass to PostType constructor.
     *
     * @see PostType::__construct();
     *
     * @throws AdwpfwException
     */
    public function add(array $data)
    {
        $this->items[] = new PostType($this->app, $data);
    }

    /**
     * Register Post Types.
     */
    public function register()
    {
        foreach ($this->items as $item) {
            $item->register();
        }
    }
}
