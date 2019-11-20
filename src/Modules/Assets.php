<?php

namespace AlexDashkin\Adwpfw\Modules;

use AlexDashkin\Adwpfw\App;
use AlexDashkin\Adwpfw\Exceptions\AdwpfwException;
use AlexDashkin\Adwpfw\Items\Asset;
use AlexDashkin\Adwpfw\Items\Css;
use AlexDashkin\Adwpfw\Items\Js;

/**
 * Enqueue CSS/JS.
 */
class Assets extends ModuleWithItems
{
    /**
     * @var Asset[]
     */
    protected $items = [];

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
     * @throws AdwpfwException
     */
    public function add(array $data)
    {
        switch ($data['type']) {
            case 'css':
                $this->items[] = new Css($this->app, $data);
                break;

            case 'js':
                $this->items[] = new Js($this->app, $data);
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
