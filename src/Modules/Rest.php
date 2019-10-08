<?php

namespace AlexDashkin\Adwpfw\Modules;

use AlexDashkin\Adwpfw\App;

/**
 * REST API Endpoints
 */
class Rest extends ItemsModule
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
     * Add an Item
     *
     * @param array $data
     * @param App $app
     */
    public function add(array $data, App $app)
    {
        $this->items[] = new PostState($data, $app);
    }

    /**
     * Hooks to register Items in WP
     */
    protected function hooks()
    {
        add_filter('rest_api_init', [$this, 'register'], 10, 2);
    }

    public function run()
    {
            foreach ($this->endpoints as $data) {
                register_rest_route($data['namespace'], $data['route'], [
                    'methods' => $data['method'],
                    'callback' => [$this, 'runEndpoint'],
                ]);
            }
    }

    /**
     * @param \WP_REST_Request $request
     * @return array Result to be served to the client
     */
    public function runEndpoint(\WP_REST_Request $request)
    {
        $route = trim($request->get_route(), ' /');

        $found = false;
        foreach ($this->endpoints as $endpoint) {
            if ($endpoint['namespace'] . $endpoint['route'] === $route) {
                $found = $endpoint;
                break;
            }
        }

        if (!$found) {
            return $this->error('Endpoint is not registered');
        }

        if ($found['admin'] && !current_user_can('administrator')) {
            return $this->error('Endpoint is for Admins only');
        }

        $this->log('Ajax request received on Endpoint ' . $route);

        $return = [
            'success' => false,
            'message' => '',
            'data' => '',
        ];

        $data = array_merge($request->get_query_params(), $request->get_body_params());
        unset($data['_wpnonce']);

        $sanitized = $this->sanitize($found, $data);

        if (!$sanitized['success']) {
            return $this->error('Validation error: ' . $sanitized['message']);
        }

        $data = !empty($sanitized['data']) ? $sanitized['data'] : [];

        $meta['route'] = $route;
        $meta['cookies'] = $this->getCookies($request->get_header('cookie'));
        $meta['referrer'] = $request->get_header('referer');

        try {
            $result = call_user_func($found['callback'], $data, $meta);
        } catch (\Exception $e) {
            $msg = 'Exception: ' . $e->getMessage() . '. Execution aborted.';
            $this->log($msg);
            $return['message'] = $msg;
            return $return;
        }

        if (is_array($result)) {
            $return = array_merge($return, $result);
        }

        return $return;
    }

    private function sanitize(array $actionData, $request)
    {
        $fields = $request;

        if (!empty($actionData['fields'])) {

            foreach ($actionData['fields'] as $name => $settings) {

                if (!isset($request[$name]) && $settings['required']) {
                    return $this->error('Missing required field: ' . $name);
                }

                if (isset($request[$name])) {
                    $sanitized = $request[$name];
                    switch ($settings['type']) {
                        case 'text':
                            $sanitized = sanitize_text_field($sanitized);
                            break;
                        case 'textarea':
                            $sanitized = sanitize_textarea_field($sanitized);
                            break;
                        case 'email':
                            $sanitized = sanitize_email($sanitized);
                            break;
                        case 'number':
                            $sanitized = (int)$sanitized;
                            break;
                        case 'url':
                            $sanitized = esc_url_raw($sanitized);
                            break;
                        case 'array':
                            $sanitized = is_array($sanitized) ? $sanitized : [];
                            break;
                        case 'form':
                            parse_str($request['form'], $sanitized);
                            break;
                    }

                    $fields[$name] = $sanitized;
                }
            }
        }

        return ['success' => true, 'data' => $fields];
    }

    private function getCookies($header)
    {
        $cookies = [];

        if (preg_match_all('/([^;\s]+)=([^;\s]+)/', $header, $matches)) {
            foreach ($matches[1] as $index => $key) {
                $cookies[$key] = $matches[2][$index];
            }
        }

        return $cookies;
    }

    private function error($message = '', $echo = false)
    {
        return $this->utils->returnError($message, $echo);
    }
}