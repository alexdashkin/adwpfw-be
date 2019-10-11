<?php

namespace AlexDashkin\Adwpfw\Items\Basic;

use AlexDashkin\Adwpfw\App;

/**
 * CSS file
 */
class Js extends Asset
{
    /**
     * Constructor.
     *
     * @param array $data {
     * @type string $id Asset ID. Defaults to sanitized $type. Must be unique.
     * @type string $type css/js. Required.
     * @type string $af admin/front. Required.
     * @type string $file Path relative to the Plugin root.
     * @type string $url Asset URL. Defaults to $file URL if $file is specified.
     * @type string $ver Version added as a query string param. Defaults to filemtime() if $file is specified.
     * @type array $deps List of Dependencies (slugs).
     * @type callable $callback Must return true to enqueue the Asset.
     * @type array $localize Key-value pairs to be passed to the script as an object with name equals to $prefix.
     * }
     * @throws \AlexDashkin\Adwpfw\Exceptions\AdwpfwException
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

        if ($data['callback'] && !$data['callback']()) {
            return;
        }

        $prefix = $this->prefix;

        $id = $prefix . '-' . sanitize_title($data['id']);

        wp_enqueue_script($id, $data['url'], $data['deps'], $data['ver'], true);

        $localize = array_merge([
            'prefix' => $prefix,
            'dev' => !empty($this->config['dev']),
            'nonce' => wp_create_nonce($prefix),
            'restNonce' => wp_create_nonce('wp_rest'),
        ], $data['localize']);

        wp_localize_script($id, $prefix, $localize);
    }
}
