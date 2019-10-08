<?php

namespace AlexDashkin\Adwpfw\Exceptions;

/**
 * Thrown when invalid data is passed to an Ajax action
 */
class InvalidRequestParamException extends \Exception
{
    public function __construct($message)
    {
        parent::__construct($message);
    }
}
