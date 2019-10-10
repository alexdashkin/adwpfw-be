<?php

namespace AlexDashkin\Adwpfw\Items\Basic;

use AlexDashkin\Adwpfw\App;
use AlexDashkin\Adwpfw\Exceptions\AdwpfwException;

/**
 * REST API Endpoint
 */
class Endpoint extends Ajax
{
    /**
     * Constructor
     *
     * @param array $data {
     * @type string $namespace Namespace with trailing slash (i.e. prefix/v1/)
     * @type string $route Route without slashes (i.e. users)
     * @type string $method get/post
     * @type bool $admin Whether available for admins only
     * @type array $fields Accepted params [type, required]
     * @type callable $callback Handler
     * }
     */
    public function __construct(array $data, App $app)
    {
        $props = [
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

        parent::__construct($data, $app, $props);
    }

    public function register()
    {
        $data = $this->data;

        register_rest_route($data['namespace'], $data['route'], [
            'methods' => $data['method'],
            'callback' => [$this, 'run'],
        ]);
    }

    /**
     * Handle the Request
     *
     * @param \WP_REST_Request $request
     * @throws AdwpfwException
     */
    public function run(\WP_REST_Request $request)
    {
        try {
            if ($this->data['admin'] && !current_user_can('administrator')) {
                throw new AdwpfwException('Endpoint is for Admins only');
            }

            $params = array_merge($request->get_query_params(), $request->get_body_params());

            $data = $params ? $this->validateRequest($params) : [];
            $result = call_user_func($this->data['callback'], $data);

        } catch (\Exception $e) {
            $this->error('Exception: ' . $e->getMessage() . '. Execution aborted.', true);
        }

        if (is_array($result)) {
            $return = array_merge(['success' => false, 'message' => '', 'data' => ''], $result);
        }

        wp_send_json($return);
    }
}
