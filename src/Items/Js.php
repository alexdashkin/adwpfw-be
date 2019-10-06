<?php

namespace AlexDashkin\Adwpfw\Items;

use AlexDashkin\Adwpfw\App;

/**
 * CSS file
 */
class Js extends Asset
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
    public function __construct(array $data, App $app)
    {
        $this->defaults = [
            'type' => '',
            'id' => '',
            'file' => '',
            'url' => '',
            'ver' => '',
            'deps' => ['jquery'],
            'async' => false,
            'localize' => [],
        ];

        parent::__construct($data, $app);
    }

    public function enqueue()
    {
        if (!empty($item['callback']) && !$item['callback']()) {
            return;
        }

        $prefix = $this->config['prefix'];

        $id = $prefix . '-' . sanitize_title($item['id']);

        wp_enqueue_script($id, $item['url'], $item['deps'], $item['ver'], true);

        $localize = array_merge([
            'prefix' => $prefix,
            'debug' => !empty($this->config['debug']),
            'nonce' => wp_create_nonce($prefix),
            'restNonce' => wp_create_nonce('wp_rest'),
        ], $item['localize']);

        wp_localize_script($item['id'], $prefix, $localize);
    }
}
