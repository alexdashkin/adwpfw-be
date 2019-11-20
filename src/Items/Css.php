<?php

namespace AlexDashkin\Adwpfw\Items;

use AlexDashkin\Adwpfw\App;
use AlexDashkin\Adwpfw\Exceptions\AdwpfwException;

/**
 * CSS file
 */
class Css extends Asset
{
    /**
     * Constructor.
     *
     * @param App $app
     * @param array $data {
     * @type string $id Asset ID. Defaults to sanitized $type. Must be unique.
     * @type string $type admin/front. Required.
     * @type string $file Path relative to the Plugin root.
     * @type string $url Asset URL. Defaults to $file URL if $file is specified.
     * @type string $ver Version added as a query string param. Defaults to filemtime() if $file is specified.
     * @type array $deps List of Dependencies (slugs).
     * @type callable $callback Must return true to enqueue the Asset.
     * }
     *
     * @throws AdwpfwException
     */
    public function __construct(App $app, array $data)
    {
        parent::__construct($app, $data);
    }

    /**
     * Enqueue Asset.
     */
    public function enqueue()
    {
        $data = $this->data;

        $callback = $data['callback'];

        if ($callback && is_callable($callback) && !$callback()) {
            return;
        }

        $id = $this->prefix . '-' . sanitize_title($data['id']);

        wp_enqueue_style($id, $data['url'], $data['deps'], $data['ver']);
    }
}
