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

        if ($this->getProp('admin') && !current_user_can('administrator')) {
            return $this->error('Endpoint is for Admins only');
        }

        return $this->execute(array_merge($request->get_query_params(), $request->get_body_params()));
    }

    /**
     * Get Default Prop value
     *
     * @param string $key
     * @return mixed
     */
    protected function getDefault(string $key)
    {
        switch ($key) {
            case 'method':
                return 'post';
        }

        return null;
    }
}
