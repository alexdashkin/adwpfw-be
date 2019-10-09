<?php

namespace AlexDashkin\Adwpfw\Items;

use AlexDashkin\Adwpfw\App;

/**
 * Admin Page Field
 */
abstract class FormField extends Item
{
    protected $tpl;

    /**
     * Constructor
     */
    public function __construct(array $data, App $app)
    {
        parent::__construct($data, $app);
    }

    abstract public function getArgs(array $values);
}
