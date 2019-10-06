<?php

namespace AlexDashkin\Adwpfw\Modules;

use AlexDashkin\Adwpfw\App;

/**
 * Admin Ajax Actions
 */
class Ajax extends Module
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
     * Add hooks
     */
    public function run()
    {
        add_action('wp_loaded', [$this, 'handle']);
    }

    /**
     * Handle the Request
     */
    public function handle()
    {
        $prefix = $this->config['prefix'];
        $request = $_REQUEST;

        if (empty($request['action']) || false === strpos($request['action'], $prefix)) {
            return;
        }

        $actionId = str_replace($prefix . '_', '', $request['action']);
        $action = $this->searchItems(['id' => $actionId], true);

        if (empty($request['action']) || !$action) {
            return;
        }

        if (!check_ajax_referer($prefix, false, false)) {
            $this->error('Wrong nonce!', true);
        }

        $this->log('Ajax request received, action: ' . $request['action']);

        if ($data = !empty($request['data']) ? $request['data'] : []) {
            $validated = $this->validate($action, $data);

            if (!$validated['success']) {
                $this->error('Validation error: ' . $validated['message'], true);
            }

            $data = !empty($validated['data']) ? $validated['data'] : [];
        }

        try {
            $result = call_user_func($action['callback'], $data);

        } catch (\Exception $e) {
            $msg = 'Exception: ' . $e->getMessage();
            $this->log($msg);
            $return['message'] = $msg;
            wp_send_json($return);
        }

        if (is_array($result)) {
            $return = array_merge(['success' => false, 'message' => '', 'data' => ''], $result);
        }

        wp_send_json($return);
    }

    /**
     * Validate params
     *
     * @param array $actionData
     * @param $request $_REQUEST params
     * @return array
     */
    private function validate(array $actionData, $request)
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

        return $this->success('', $fields);
    }

    /**
     * Return Success array
     *
     * @param string $message
     * @param array $data Data to return as JSON
     * @param bool $echo Whether to echo Response right away without returning
     * @return array
     */
    private function success($message = '', $data = [], $echo = false)
    {
        return $this->m('Utils')->returnSuccess($message, $data, $echo);
    }

    /**
     * Return Error array
     *
     * @param string $message
     * @param bool $echo Whether to echo Response right away without returning
     * @return array
     */
    private function error($message = '', $echo = false)
    {
        return $this->m('Utils')->returnError($message, $echo);
    }
}