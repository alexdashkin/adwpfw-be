<?php

namespace AlexDashkin\Adwpfw\Modules\Api;

use AlexDashkin\Adwpfw\Exceptions\AppException;
use AlexDashkin\Adwpfw\Modules\Module;

/**
 * Abstract Ajax Endpoint. To be extended.
 */
abstract class Request extends Module
{
    /**
     * Validate and Sanitize values.
     *
     * @param array $request $_REQUEST params.
     * @return array Sanitized key-value pairs.
     * @throws AppException
     */
    protected function validateRequest(array $request): array
    {
        $fields = $request;

        if ($fieldDefs = $this->gp('fields')) {
            foreach ($fieldDefs as $name => $settings) {
                if (!isset($request[$name]) && $settings['required']) {
                    throw new AppException('Missing required field: ' . $name);
                }

                $type = $settings['type'] ?? 'text';

                if (isset($request[$name])) {
                    $sanitized = $request[$name];

                    switch ($type) {
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
                            parse_str($sanitized, $sanitized);
                            $sanitized = array_map('stripslashes_deep', $sanitized);
                            break;
                    }

                    $fields[$name] = $sanitized;
                }
            }
        }

        return $fields;
    }

    /**
     * Handle the Request
     *
     * @param array $params Request params
     * @return array
     */
    protected function execute(array $params): array
    {
        try {
            $data = $this->validateRequest($params);
            $result = $this->gp('callback')($data);
        } catch (\Exception $e) {
            return $this->error('Exception: ' . $e->getMessage());
        }

        if (!is_array($result)) {
            return $this->error('Result malformed');
        }

        return !empty($result['success']) ? $this->success($result['data'] ?? []) : $this->error($result['message'] ?? 'Unknown error');
    }

    /**
     * Return Success array
     *
     * @param array $data Data to return as JSON.
     * @return array
     */
    protected function success(array $data = []): array
    {
        return $this->m('helpers')->returnSuccess('Done', $data);
    }

    /**
     * Return Error array
     *
     * @param string $message Error message.
     * @return array
     */
    protected function error(string $message = ''): array
    {
        return $this->m('helpers')->returnError($message);
    }
}
