<?php

namespace AlexDashkin\Adwpfw\Modules;

use AlexDashkin\Adwpfw\App;
use AlexDashkin\Adwpfw\Exceptions\AdwpfwException;
use AlexDashkin\Adwpfw\Items\Endpoint;

/**
 * REST API Endpoints.
 */
class Rest extends ModuleAjax
{
    /**
     * @var Endpoint[]
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

        add_filter('rest_api_init', [$this, 'register']);
    }

    /**
     * Add Endpoint.
     *
     * @param array $data
     *
     * @see Endpoint::__construct();
     *
     * @throws AdwpfwException
     */
    public function add(array $data)
    {
        $this->items[] = new Endpoint($this->app, $data);
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