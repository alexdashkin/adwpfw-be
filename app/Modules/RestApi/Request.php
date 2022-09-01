<?php

namespace AlexDashkin\Adwpfw\Modules\RestApi;

use AlexDashkin\Adwpfw\{Exceptions\AppException, Modules\Module};

/**
 * Abstract Ajax Endpoint. To be extended.
 */
abstract class Request extends Module
{
    /**
     * Handle the Request
     *
     * @param array $params Request params
     * @return array
     */
    protected function execute(array $params): array
    {
        try {
            $data = $this->sanitizeData($params, $this->getProp('fields'));
            $result = $this->getProp('callback')($data);
        } catch (\Exception $e) {
            return $this->error('Exception: ' . $e->getMessage());
        }

        if (!is_array($result)) {
            return $this->error('Result malformed');
        }

        return $result;
    }

    /**
     * Validate and sanitize fields as per provided defs
     *
     * @param array $data
     * @param array $fieldDefs
     * @return array
     * @throws AppException
     */
    protected function sanitizeData(array $data, array $fieldDefs): array
    {
        foreach ($fieldDefs as $name => $settings) {
            if (!isset($data[$name]) && !empty($settings['required'])) {
                throw new AppException('Missing required field: ' . $name);
            }

            if (isset($data[$name])) {
                $value = $data[$name];
                $type = $settings['type'] ?? 'raw';

                if ('email' === $type && !is_email($value)) {
                    throw new AppException('Invalid Email: ' . $value);
                }

                switch ($type) {
                    case 'text':
                        $sanitized = sanitize_text_field($value);
                        break;

                    case 'textarea':
                        $sanitized = sanitize_textarea_field($value);
                        break;

                    case 'email':
                        $sanitized = sanitize_email($value);
                        break;

                    case 'number':
                        $sanitized = (int)$value;
                        break;

                    case 'url':
                        $sanitized = esc_url_raw($value);
                        break;

                    case 'array':
                        $sanitized = is_array($value) ? $value : [];
                        break;

                    case 'form':
                        parse_str($value, $sanitized);
                        $sanitized = array_map('stripslashes_deep', $sanitized);
                        break;

                    case 'raw':
                    default:
                        $sanitized = $value;
                }

                $data[$name] = $sanitized;
            }
        }

        return $data;
    }

    /**
     * Return Error array
     *
     * @param string $message Error message.
     * @return array
     */
    protected function error(string $message = ''): array
    {
        return $this->app->returnError($message);
    }
}
