<?php

namespace AlexDashkin\Adwpfw\Modules\Api;

/**
 * namespace*, route*, callback*, method, admin, fields
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

        if ($this->getProp('nonce')) {
            check_ajax_referer('wp_rest');
        }

        if ($this->getProp('admin') && !current_user_can('administrator')) {
            return $this->error('Endpoint is for Admins only');
        }

        $method = 'post' === strtolower($this->getProp('method')) ? 'get_body_params' : 'get_query_params';

        return $this->execute($request->$method());
    }

    /**
     * Get Default prop values
     *
     * @return array
     */
    protected function defaults(): array
    {
        $defaults = [
            'namespace' => 'adwpfw/v1',
            'route' => 'test',
            'method' => 'post',
            'nonce' => false,
        ];

        return array_merge(parent::defaults(), $defaults);
    }
}
