<?php

namespace AlexDashkin\Adwpfw\Items;

/**
 * Admin Ajax Action
 */
class AjaxAction extends Item
{
    /**
     * Constructor
     *
     * @param array $data {
     * @type string $id Action ID without prefix (will be added automatically)
     * @type array $fields Accepted params [type, required]
     * @type callable $callback Handler
     * }
     */
    public function __construct(array $data)
    {
        $this->defaults = [
            'id' => '',
            'fields' => [],
            'callback' => '',
        ];

        parent::__construct($data);
    }
}
