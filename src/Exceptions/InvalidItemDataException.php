<?php

namespace AlexDashkin\Adwpfw\Exceptions;

/**
 * Thrown when invalid data is passed to the Item Constructor
 */
class InvalidItemDataException extends \Exception
{
    public function __construct($message)
    {
        parent::__construct($message);
    }
}
