<?php

namespace AlexDashkin\Adwpfw\Items\Basic;

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
        $props = [
            'localize' => [
                'type' => 'array',
                'default' => [],
            ],
        ];

        parent::__construct($data, $app, $props);
    }

    public function enqueue()
    {
        $data = $this->data;

        if (!empty($data['callback']) && !$data['callback']()) {
            return;
        }

        $prefix = $this->config['prefix'];

        $id = $prefix . '-' . sanitize_title($data['slug']);

        wp_enqueue_script($id, $data['url'], $data['deps'], $data['ver'], true);

        $localize = array_merge([
            'prefix' => $prefix,
            'dev' => !empty($this->config['dev']),
            'nonce' => wp_create_nonce($prefix),
            'restNonce' => wp_create_nonce('wp_rest'),
        ], $data['localize']);

        wp_localize_script($data['slug'], $prefix, $localize);
    }
}
