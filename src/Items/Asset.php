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
     * @type string $type admin/front. Required.
     * @type string $slug Used as a reference when registering in WP. Defaults to $prefix + $type + uniqid(). Must be unique.
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
            'url' => $url,
            'ver' => $version,
        ], $data);

        parent::__construct($data, $app);
    }

    abstract public function enqueue();
}
