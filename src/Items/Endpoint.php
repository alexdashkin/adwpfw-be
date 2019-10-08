<?php

namespace AlexDashkin\Adwpfw\Items;

use AlexDashkin\Adwpfw\App;
use AlexDashkin\Adwpfw\Exceptions\InvalidRequestParamException;

/**
 * REST API Endpoint
 */
class Endpoint extends Item
{
    /**
     * Constructor
     *
     * @param array $data {
     * @type string $namespace Namespace with trailing slash (i.e. prefix/v1/)
     * @type string $route Route without slashes (i.e. users)
     * @type string $method get/post
     * @type bool $admin Whether available for admins only
     * @type array $fields Accepted params [type, required]
     * @type callable $callback Handler
     * }
     */
    public function __construct(array $data, App $app)
    {
        $this->props = [
            'namespace' => [
                'required' => true,
            ],
            'route' => [
                'required' => true,
            ],
            'method' => [
                'default' => 'post',
            ],
            'admin' => [
                'type' => 'bool',
                'default' => false,
            ],
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

        parent::__construct($data, $app);
    }

    /**
     * Run the Action
     */
    public function run($request)
    {
        $data = !empty($request['data']) ? $this->validateRequest($request['data']) : [];

        $result = call_user_func($this->data['callback'], $data);

        if (is_array($result)) {
            $return = array_merge(['success' => false, 'message' => '', 'data' => ''], $result);
        }

        return $return;
    }

    /**
     * Validate and Sanitize values
     *
     * @param $request $_REQUEST params
     * @return array
     * @throws InvalidRequestParamException
     */
    private function validateRequest($request)
    {
        $actionData = $this->data;

        $fields = $request;

        if (!empty($actionData['fields'])) {

            foreach ($actionData['fields'] as $name => $settings) {

                if (!isset($request[$name]) && $settings['required']) {
                    throw new InvalidRequestParamException('Missing required field: ' . $name);
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
}
