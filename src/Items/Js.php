<?php

namespace AlexDashkin\Adwpfw\Items;

use AlexDashkin\Adwpfw\App;
use AlexDashkin\Adwpfw\Exceptions\AdwpfwException;

/**
 * CSS file
 */
class Js extends Asset
{
    /**
     * Constructor.
     *
     * @param App $app
     * @param array $data {
     * @type string $id Asset ID. Defaults to sanitized $type. Must be unique.
     * @type string $type css/js. Required.
     * @type string $af admin/front. Required.
     * @type string $file Path relative to the Plugin root. Default null.
     * @type string $url Asset URL. Default null.
     * @type string $ver Version added as a query string param. Defaults to filemtime() if $file is specified.
     * @type array $deps List of Dependencies (slugs).
     * @type callable $callback Must return true to enqueue the Asset.
     * @type array $localize Key-value pairs to be passed to the script as an object with name equals to $prefix.
     * }
     *
     * @throws AdwpfwException
     */
    public function __construct(App $app, array $data)
    {
        $props = [
            'deps' => [
                'type' => 'array',
                'default' => ['jquery'],
            ],
            'localize' => [
                'type' => 'array',
                'default' => [],
            ],
        ];

        parent::__construct($app, $data, $props);
    }

    public function enqueue()
    {
        $data = $this->data;

        $callback = $data['callback'];

        if ($callback && is_callable($callback) && !$callback()) {
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
