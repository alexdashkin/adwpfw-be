<?php

namespace AlexDashkin\Adwpfw\Items;

/**
 * CSS file
 */
class Css extends Item
{
    /**
     * Constructor
     *
     * @param array $data {
     * @type string $type admin/front
     * @type string $id Used as a reference when registering in WP. Defaults to $prefix + sanitized $type + index. Must be unique.
     * @type string $file Path relative to Plugin Root
     * @type string $url Ignored if $file is specified
     * @type string $ver Version added as a query string param. Defaults to filemtime() if $file is specified.
     * @type array $deps List of Dependencies (slugs)
     * }
     */
    public function __construct(array $data)
    {
        $this->defaults = [
            'type' => '',
            'id' => '',
            'file' => '',
            'url' => '',
            'ver' => '',
            'deps' => [],
        ];

        parent::__construct($data);
    }
}
