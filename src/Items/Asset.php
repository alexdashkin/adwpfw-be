<?php

namespace AlexDashkin\Adwpfw\Items;

use AlexDashkin\Adwpfw\App;

/**
 * Asset file (CSS/JS)
 */
abstract class Asset extends Item
{
    /**
     * Constructor
     *
     * @param array $data {
     * @type string $type admin/front
     * @type string $id Used as a reference when registering in WP. Defaults to $prefix + $type + uniqid(). Must be unique.
     * @type string $file Path relative to Plugin Root
     * @type string $url Ignored if $file is specified
     * @type string $ver Version added as a query string param. Defaults to filemtime() if $file is specified.
     * @type array $deps List of Dependencies (slugs)
     * }
     */
    public function __construct(array $data, App $app)
    {
        $url = $version = null;

        if (!empty($data['file'])) {
            $file = $data['file'];
            $url = $app->config['baseUrl'] . $file;
            $path = $app->config['baseDir'] . $file;
            $version = file_exists($path) ? filemtime($path) : null;
        }

        $data = array_merge([
            'id' => $data['type'] . '-' . uniqid(),
            'url' => $url,
            'ver' => $version,
        ], $data);

        parent::__construct($data, $app);
    }

    /**
     * Hooks to register Item in WP
     */
    protected function hooks()
    {
        $hook = 'admin' === $this->data['type'] ? 'admin_enqueue_scripts' : 'wp_enqueue_scripts';
        add_action($hook, [$this, 'enqueue'], 20);
    }

    abstract public function enqueue();
}
