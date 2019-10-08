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
     * @type string $type admin/front. Required.
     * @type string $url Required, but can be omitted if $file is specified
     * @type string $slug Used as a reference when registering in WP. Defaults to $prefix + sanitized $type + index. Must be unique.
     * @type string $file Path relative to Plugin Root
     * @type string $ver Version added as a query string param. Defaults to filemtime() if $file is specified.
     * @type array $deps List of Dependencies (slugs)
     * @type array $localize Key-value pairs to be passed to the script as an object with name equals to $prefix
     * }
     */
    public function __construct(array $data, App $app)
    {
        $this->props = [
            'type' => [
                'required' => true,
            ],
            'url' => [
                'required' => true,
            ],
            'slug' => [
                'default' => $this->getDefaultSlug($data['type']),
            ],
            'file' => [
                'default' => null,
            ],
            'ver' => [
                'default' => null,
            ],
            'deps' => [
                'type' => 'array',
                'default' => [],
            ],
            'localize' => [
                'type' => 'array',
                'default' => [],
            ],
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
