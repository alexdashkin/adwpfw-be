<?php

namespace AlexDashkin\Adwpfw\Exceptions;

use AlexDashkin\Adwpfw\Logger;

/**
 * Basic App Exception
 */
class AppException extends \Exception
{
    public function __construct($message, $values = [])
    {
        // WP_Error to string
        if (is_wp_error($message)) {
            $message = 'WP_Error: ' . implode(' | ', $message->get_error_messages());
        }

        // Populate args if any
        if (is_string($message)) {
            $message = vsprintf($message, $values);
        }

        // Add log entry
        Logger::log('Exception: %s', [$message]);

        parent::__construct($message);
    }
}
