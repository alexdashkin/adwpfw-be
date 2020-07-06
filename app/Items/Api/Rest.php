<?php

namespace AlexDashkin\Adwpfw\Items\Api;

use AlexDashkin\Adwpfw\App;

class Rest extends Request
{
    /**
     * Init Module
     */
    public function init()
    {
        App::get(
            'hook',
            [
                'tag' => 'rest_api_init',
                'callback' => [$this, 'register'],
            ]
        );
    }

    /**
     * Register Endpoint
     */
    public function register()
    {
        register_rest_route(
            $this->get('namespace'),
            $this->get('route'),
            [
                'methods' => $this->get('method'),
                'callback' => [$this, 'handle'],
            ]
        );
    }

    /**
     * Handle the Request
     */
    public function handle(\WP_REST_Request $request)
    {
        if ($this->get('admin') && !current_user_can('administrator')) {
            return 'Endpoint is for Admins only';
        }

        return $this->execute(array_merge($request->get_query_params(), $request->get_body_params()));
    }

    /**
     * Get Class props
     *
     * @return array
     */
    protected function props(): array
    {
        return [
            'namespace' => [
                'required' => true,
            ],
            'route' => [
                'required' => true,
            ],
            'callback' => [
                'type' => 'callable',
                'required' => true,
            ],
            'method' => [
                'default' => 'post',
            ],
            'admin' => [
                'type' => 'bool',
                'default' => false,
            ],
            'fields' => [
                'type' => 'array',
                'default' => [],
            ],
        ];
    }
}
