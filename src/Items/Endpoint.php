<?php

namespace AlexDashkin\Adwpfw\Items;

use AlexDashkin\Adwpfw\App;
use AlexDashkin\Adwpfw\Exceptions\AdwpfwException;

/**
 * REST API Endpoint
 */
class Endpoint extends Ajax
{
    /**
     * Constructor.
     *
     * @param App $app
     * @param array $data {
     * @type string $id ID for internal use. Defaults to sanitized 'route'.
     * @type string $namespace Namespace with trailing slash (i.e. prefix/v1/).
     * @type string $route Route without slashes (i.e. users).
     * @type string $method get/post.
     * @type bool $admin Whether available for admins only.
     * @type array $fields Accepted params [type, required].
     * @type callable $callback Handler. Gets an array with $_REQUEST params.
     * Must return array ['success', 'message', 'data']. Required.
     * }
     *
     * @throws AdwpfwException
     */
    public function __construct(App $app, array $data)
    {
        $props = [
            'id' => [
                'default' => $this->getDefaultId($data['route']),
            ],
            'namespace' => [
                'required' => true,
            ],
            'route' => [
                'required' => true,
            ],
            'method' => [
                'default' => 'post',
            ],
            'admin' => [
                'type' => 'bool',
                'default' => false,
            ],
        ];

        parent::__construct($app, $data, $props);
    }

    /**
     * Register Endpoint.
     * @return True on success, false on error.
     */
    public function register()
    {
        $data = $this->data;

        return register_rest_route($data['namespace'], $data['route'], [
            'methods' => $data['method'],
            'callback' => [$this, 'run'],
        ]);
    }

    /**
     * Handle the Request.
     *
     * @param \WP_REST_Request $request
     */
    public function run(\WP_REST_Request $request)
    {
        $this->log('REST API request, endpoint "%s"', [$this->data['route']]);

        if ($this->data['admin'] && !current_user_can('administrator')) {
            $this->error('Endpoint is for Admins only', true);
        }

        $data = array_merge($request->get_query_params(), $request->get_body_params());

        parent::handle($data);
    }
}
