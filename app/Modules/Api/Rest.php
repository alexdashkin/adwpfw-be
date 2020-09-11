<?php

namespace AlexDashkin\Adwpfw\Modules\Api;

class Rest extends Request
{
    /**
     * Init Module
     */
    public function init()
    {
        $this->addHook('rest_api_init', [$this, 'register']);
    }

    /**
     * Register Endpoint
     */
    public function register()
    {
        register_rest_route(
            $this->getProp('namespace'),
            $this->getProp('route'),
            [
                'methods' => $this->getProp('method'),
                'callback' => [$this, 'handle'],
                'permission_callback' => '__return_true'
            ]
        );
    }

    /**
     * Handle the Request
     *
     * @param \WP_REST_Request $request
     * @return array
     */
    public function handle(\WP_REST_Request $request): array
    {
        $this->log('REST request: "%s%s"', [$this->getProp('namespace'), $this->getProp('route')]);

        if ($this->getProp('admin') && !current_user_can('administrator')) {
            return $this->error('Endpoint is for Admins only');
        }

        return $this->execute(array_merge($request->get_query_params(), $request->get_body_params()));
    }

    /**
     * Get Class props
     *
     * @return array
     */
    protected function getInitialPropDefs(): array
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
