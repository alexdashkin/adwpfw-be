<?php

namespace AlexDashkin\Adwpfw\Modules\RestApi;

/**
 * WP REST endpoint
 */
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

        if ($this->getProp('nonce') && !check_ajax_referer('wp_rest', '_wpnonce', false)) {
            return $this->error('Invalid nonce');
        }

        if ($this->getProp('admin') && !current_user_can('administrator')) {
            return $this->error('Endpoint is for Admins only');
        }

        $method = 'post' === strtolower($this->getProp('method')) ? 'get_body_params' : 'get_query_params';

        return $this->execute($request->$method());
    }

    /**
     * Get prop definitions
     *
     * @return array
     */
    protected function getPropDefs(): array
    {
        return [
            'namespace' => [
                'type' => 'string',
                'required' => true,
            ],
            'route' => [
                'type' => 'string',
                'required' => true,
            ],
            'method' => [
                'type' => 'string',
                'required' => true,
            ],
            'nonce' => [
                'type' => 'bool',
                'default' => false,
            ],
            'admin' => [
                'type' => 'bool',
                'default' => false,
            ],
            'callback' => [
                'type' => 'callable',
                'required' => true,
            ],
            'fields' => [
                'type' => 'array',
                'default' => [],
            ],
        ];
    }
}
