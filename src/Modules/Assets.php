<?php

namespace AlexDashkin\Adwpfw\Modules;

use AlexDashkin\Adwpfw\App;
use AlexDashkin\Adwpfw\Items\Css;
use AlexDashkin\Adwpfw\Items\Js;

/**
 * Enqueue CSS/JS
 */
class Assets extends ItemsModule
{
    private $enqueue = [];
    private $remove = [];

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
     * Hooks to register Items in WP
     */
    protected function hooks()
    {
        add_action('admin_enqueue_scripts', [$this, 'enqueueAdmin'], 20);
        add_action('wp_enqueue_scripts', [$this, 'enqueueFront'], 20);
    }

    /**
     * Add an item
     *
     * @param array $data
     * @param App $app
     */
    public function add(array $data, App $app)
    {
        switch ($data['type']) {
            case 'css':
                $this->items[] = new Css($data, $app);
                break;

            case 'js':
                $this->items[] = new Js($data, $app);
                break;
        }
    }

    /**
     * Enqueue registered assets
     *
     * @param array $ids Registered assets IDs to add
     */
    public function addRegistered(array $ids)
    {
        $this->enqueue = array_merge($this->enqueue, $ids);
    }

    /**
     * Remove assets
     *
     * @param array $ids Registered assets IDs to be removed
     */
    public function remove(array $ids)
    {
        $this->remove = array_merge($this->remove, $ids);
    }

    /**
     * Hooked on "admin_enqueue_scripts"
     */
    public function enqueueAdmin()
    {
        foreach ($this->searchItems(['type' => 'admin']) as $item) {
            $item->enqueue();
        }

        $this->enqueue();
    }

    /**
     * Hooked on "wp_enqueue_scripts"
     */
    public function enqueueFront()
    {
        foreach ($this->searchItems(['type' => 'front']) as $item) {
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
