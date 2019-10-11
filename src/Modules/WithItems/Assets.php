<?php

namespace AlexDashkin\Adwpfw\Modules\WithItems;

use AlexDashkin\Adwpfw\App;
use AlexDashkin\Adwpfw\Items\Basic\Css;
use AlexDashkin\Adwpfw\Items\Basic\Js;

/**
 * Enqueue CSS/JS.
 */
class Assets extends ModuleWithItems
{
    /**
     * @var array Registered assets ids to enqueue
     */
    private $enqueue = [];

    /**
     * @var array Registered assets ids to remove
     */
    private $remove = [];

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
     * Hooks to register Items in WP
     */
    protected function init()
    {
        add_action('admin_enqueue_scripts', [$this, 'enqueueAdmin'], 20);
        add_action('wp_enqueue_scripts', [$this, 'enqueueFront'], 20);
    }

    /**
     * Add Asset
     *
     * @param array $data
     *
     * @see Css::__construct(), Js::__construct()
     *
     * @throws \AlexDashkin\Adwpfw\Exceptions\AdwpfwException
     */
    public function add(array $data)
    {
        switch ($data['type']) {
            case 'css':
                $this->items[] = new Css($data, $this->app);
                break;

            case 'js':
                $this->items[] = new Js($data, $this->app);
                break;
        }
    }

    /**
     * Enqueue registered assets.
     *
     * @param array $ids Registered assets IDs to add.
     */
    public function addRegistered(array $ids)
    {
        $this->enqueue = array_merge($this->enqueue, $ids);
    }

    /**
     * Remove registered assets.
     *
     * @param array $ids Registered assets IDs to remove.
     */
    public function remove(array $ids)
    {
        $this->remove = array_merge($this->remove, $ids);
    }

    /**
     * Enqueue admin assets
     *
     * Hooked on "admin_enqueue_scripts"
     */
    public function enqueueAdmin()
    {
        foreach ($this->searchItems(['af' => 'admin']) as $item) {
            $item->enqueue();
        }

        $this->enqueue();
    }

    /**
     * Enqueue front assets
     *
     * Hooked on "wp_enqueue_scripts"
     */
    public function enqueueFront()
    {
        foreach ($this->searchItems(['af' => 'front']) as $item) {
            $item->enqueue();
        }

        $this->enqueue();
    }

    /**
     * Remove unnecessary and Enqueue registered
     */
    private function enqueue()
    {
        foreach ($this->remove as $item) {
            if (wp_script_is($item, 'registered')) {
                wp_deregister_script($item);
            }
        }

        foreach ($this->enqueue as $item) {
            wp_enqueue_script($item);
        }
    }
}
