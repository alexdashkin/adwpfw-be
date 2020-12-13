<?php

namespace AlexDashkin\Adwpfw\Modules\Api;

use AlexDashkin\Adwpfw\Modules\Module;

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
            $data = $this->main->validateFields($params, $this->getProp('fields'));
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
     * Return Error array
     *
     * @param string $message Error message.
     * @return array
     */
    protected function error(string $message = ''): array
    {
        return $this->main->returnError($message);
    }
}
