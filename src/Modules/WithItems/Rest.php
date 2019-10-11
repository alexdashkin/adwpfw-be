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
     *
     * @see Endpoint::__construct();
     *
     * @throws \AlexDashkin\Adwpfw\Exceptions\AdwpfwException
     */
    public function add(array $data)
    {
        $this->items[] = new Endpoint($data, $this->app);
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