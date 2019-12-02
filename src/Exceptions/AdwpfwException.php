<?php

namespace AlexDashkin\Adwpfw\Exceptions;

/**
 * Exception Class
 */
class AdwpfwException extends \Exception
{
    public function __construct($message, $code = 0, \Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
