<?php

namespace AlexDashkin\Adwpfw\Modules;

use AlexDashkin\Adwpfw\Common\Helpers;

/**
 * Ajax actions and REST API Endpoints
 */
class Ajax extends Module
{
    private $actions = [];
    private $endpoints = [];

    /**
     * @var \AlexDashkin\Adwpfw\Common\Utils
     */
    private $utils;

    public function __construct($app)
    {
        parent::__construct($app);
        $this->utils = $this->m('Utils');
    }

    public function run()
    {
        if (wp_doing_ajax()) {
            add_action('wp_loaded', [$this, 'runAjax']);
        }

        add_action('rest_api_init', function () {
            foreach ($this->endpoints as $data) {
                register_rest_route($data['namespace'], $data['route'], [
                    'methods' => $data['method'],
                    'callback' => [$this, 'runEndpoint'],
                ]);
            }
        });
    }

    /**
     * Add an AJAX action (admin-ajax.php)
     *
     * @param array $action {
     * @type string $id Action ID without prefix (will be added automatically)
     * @type array $fields Accepted params [type, required]
     * @type callable $callback Handler
     * }
     */
    public function addAction(array $action)
    {
        $action = array_merge([
            'fields' => [],
        ], $action);

        foreach ($action['fields'] as &$field) {
            $field = array_merge([
                'type' => 'text',
                'required' => false,
            ], $field);
        }

        $this->actions[] = $action;
    }

    /**
     * Add multiple AJAX actions (admin-ajax.php)
     *
     * @param array $actions
     *
     * @see Ajax::addAction()
     */
    public function addActions(array $actions)
    {
        foreach ($actions as $action) {
            $this->addAction($action);
        }
    }

    /**
     * Add an REST API Endpoint (/wp-json/)
     *
     * @param array $endpoint {
     * @type string $namespace Namespace (i.e. prefix/v1/)
     * @type string $route Route (i.e. users)
     * @type string $method get/post
     * @type bool $admin Whether available for admins only
     * @type array $fields Accepted params [type, required]
     * @type callable $callback Handler
     * }
     */
    public function addEndpoint(array $endpoint)
    {
        $endpoint = array_merge([
            'namespace' => $this->config['prefix'] . '/v1/',
            'route' => '',
            'method' => 'post',
            'admin' => false,
            'fields' => [],
        ], $endpoint);

        foreach ($endpoint['fields'] as &$field) {
            $field = array_merge([
                'type' => 'text',
                'required' => false,
            ], $field);
        }

        $this->endpoints[] = $endpoint;
    }

    /**
     * Add multiple REST API Endpoints (/wp-json/)
     *
     * @param array $endpoints
     *
     * @see Ajax::addEndpoint()
     */
    public function addEndpoints($endpoints)
    {
        foreach ($endpoints as $endpoint) {
            $this->addEndpoint($endpoint);
        }
    }

    public function runAjax()
    {
        $prefix = $this->config['prefix'];
        $request = $_REQUEST;

        if (empty($request['action']) || false === strpos($request['action'], $prefix)) {
            return;
        }

        $actionId = str_replace($prefix . '_', '', $request['action']);
        $action = Helpers::arraySearch($this->actions, ['id' => $actionId], true);

        if (!isset($request['action']) || !$action) {
            return;
        }

        if (!check_ajax_referer($prefix, false, false)) {
            $this->error('Wrong nonce!', true);
        }

        $return = [
            'success' => false,
            'message' => '',
            'data' => '',
        ];

        $this->log('Ajax request received, action: ' . $request['action']);

        $data = isset($request['data']) ? $request['data'] : [];

        $validated = $this->sanitize($action, $data);

        if (!$validated['success']) {
            $this->error('Validation error: ' . $validated['message'], true);
        }

        $data = !empty($validated['data']) ? $validated['data'] : [];

        try {
            $result = call_user_func($action['callback'], $data);
        } catch (\Exception $e) {
            $msg = 'Exception: ' . $e->getMessage() . '. Execution aborted.';
            $this->log($msg);
            $return['message'] = $msg;
            wp_send_json($return);
        }

        if (is_array($result)) {
            $return = array_merge($return, $result);
        }

        wp_send_json($return);
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