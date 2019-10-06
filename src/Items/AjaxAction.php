<?php

namespace AlexDashkin\Adwpfw\Items;

use AlexDashkin\Adwpfw\App;

/**
 * Admin Ajax Action
 */
class AjaxAction extends Item
{
    /**
     * Constructor
     *
     * @param array $data {
     * @type string $id Action ID without prefix (will be added automatically)
     * @type array $fields Accepted params [type, required]
     * @type callable $callback Handler
     * }
     */
    public function __construct(array $data, App $app)
    {
        $this->defaults = [
            'id' => '',
            'fields' => [],
            'callback' => '',
        ];

        parent::__construct($data, $app);
    }

    /**
     * Hooks to register Item in WP
     */
    protected function hooks()
    {
        add_action('wp_loaded', [$this, 'handle']);
    }

    /**
     * Handle the Request
     */
    public function handle()
    {
        $prefix = $this->app->config['prefix'];
        $request = $_REQUEST;

        if (empty($request['action']) || false === strpos($request['action'], $prefix)) {
            return;
        }

        $actionId = str_replace($prefix . '_', '', $request['action']);

        if ($actionId !== $this->data['id']) {
            return;
        }

        if (!check_ajax_referer($prefix, false, false)) {
            $this->error('Wrong nonce!', true);
        }

        $this->log('Ajax request received, action: ' . $actionId);

        if ($data = !empty($request['data']) ? $request['data'] : []) {
            $validated = $this->validate($data);

            if (!$validated['success']) {
                $this->error('Validation error: ' . $validated['message'], true);
            }

            $data = !empty($validated['data']) ? $validated['data'] : [];
        }

        try {
            $result = call_user_func($this->data['callback'], $data);

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
     * @param $request $_REQUEST params
     * @return array
     */
    private function validate($request)
    {
        $actionData = $this->data;

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
