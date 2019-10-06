<?php

namespace AlexDashkin\Adwpfw\Items;

/**
 * CSS file
 */
class Js extends Item
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
     * @type bool $async Whether to load async
     * @type array $localize Key-value pairs to be passed to the script as an object with name equals to $prefix
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
            'async' => false,
            'localize' => [],
        ];

        parent::__construct($data);
    }
}
