<?php

namespace AlexDashkin\Adwpfw\Items;

use AlexDashkin\Adwpfw\App;
use AlexDashkin\Adwpfw\Exceptions\AdwpfwException;
use AlexDashkin\Adwpfw\Modules\Helpers;

/**
 * Abstract Ajax Endpoint. To be extended.
 */
abstract class Ajax extends Item
{
    /**
     * Constructor.
     *
     * @param array $data
     * @param App $app
     * @param array $props
     * @throws AdwpfwException
     */
    protected function __construct(App $app, array $data, array $props = []) // todo default values for fields
    {
        $defaults = [
            'callback' => [
                'type' => 'callback',
                'required' => true,
            ],
            'fields' => [
                'type' => 'array',
                'def' => [
                    'type' => 'string',
                    'required' => false,
                ],
            ],
        ];

        parent::__construct($app, $data, array_merge($defaults, $props));
    }

    /**
     * Validate and Sanitize values.
     *
     * @param $request $_REQUEST params.
     * @return array Sanitized key-value pairs.
     * @throws AdwpfwException
     */
    protected function validateRequest($request)
    {
        $actionData = $this->data;

        $fields = $request;

        if (!empty($actionData['fields'])) {
            foreach ($actionData['fields'] as $name => $settings) {
                if (!isset($request[$name]) && $settings['required']) {
                    throw new AdwpfwException('Missing required field: ' . $name);
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

        return $fields;
    }

    /**
     * Handle the Request.
     *
     * @param array $params $_REQUEST['data']
     */
    public function handle($params)
    {
        try {
            $data = $this->validateRequest($params);
            $result = call_user_func($this->data['callback'], $data);
        } catch (\Exception $e) {
            $this->error('Exception: ' . $e->getMessage(), true);
        }

        if (!is_array($result)) {
            $this->error('Result malformed', true);
        }

        if (!empty($result['success'])) {
            $this->success('Done', !empty($result['data']) ? $result['data'] : [], true);
        } else {
            $this->error(!empty($result['message']) ? $result['message'] : 'Unknown error', true);
        }
    }

    /**
     * Return Success array.
     *
     * @param string $message Message.
     * @param array $data Data to return as JSON.
     * @param bool $echo Whether to echo Response right away without returning.
     * @return array
     */
    protected function success($message = '', $data = [], $echo = false)
    {
        return Helpers::returnSuccess($message, $data, $echo);
    }

    /**
     * Return Error array.
     *
     * @param string $message Error message.
     * @param bool $echo Whether to echo Response right away without returning.
     * @return array
     */
    protected function error($message = '', $echo = false)
    {
        return Helpers::returnError($message, $echo);
    }
}
