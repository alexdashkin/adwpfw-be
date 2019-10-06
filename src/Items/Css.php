<?php

namespace AlexDashkin\Adwpfw\Items;

use AlexDashkin\Adwpfw\App;

/**
 * CSS file
 */
class Css extends Asset
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
    public function __construct(array $data, App $app)
    {
        $this->defaults = [
            'type' => '',
            'id' => '',
            'file' => '',
            'url' => '',
            'ver' => '',
            'deps' => [],
        ];

        parent::__construct($data, $app);
    }

    public function enqueue()
    {
        if (!empty($item['callback']) && !$item['callback']()) {
            return;
        }

        wp_enqueue_style($item['id'], $item['url'], $item['deps'], $item['ver']);
    }
}
