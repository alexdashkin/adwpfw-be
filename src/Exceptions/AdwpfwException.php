<?php

namespace AlexDashkin\Adwpfw\Exceptions;

/**
 * Main Exception
 */
class AdwpfwException extends \Exception
{
    public function __construct($message, $code = 0, \Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
