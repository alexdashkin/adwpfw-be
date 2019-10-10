<?php

namespace AlexDashkin\Adwpfw\Modules\WithItems;

use AlexDashkin\Adwpfw\App;
use AlexDashkin\Adwpfw\Items\Basic\Endpoint;

/**
 * REST API Endpoints.
 */
class Rest extends ModuleAjax
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
     * Add Endpoint.
     *
     * @param array $data
     * @param App $app
     *
     * @throws \AlexDashkin\Adwpfw\Exceptions\AdwpfwException
     *
     * @see Endpoint::__construct();
     */
    public function add(array $data, App $app)
    {
        $this->items[] = new Endpoint($data, $app);
    }

    /**
     * Hooks to register Items in WP.
     */
    protected function init()
    {
        add_filter('rest_api_init', [$this, 'register']);
    }

    /**
     * Register Endpoints.
     */
    public function register()
    {
        foreach ($this->items as $item) {
            $item->register();
        }
    }
}