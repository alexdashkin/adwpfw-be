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
     * @type string $type admin/front. Required.
     * @type string $url Required, but can be omitted if $file is specified
     * @type string $slug Used as a reference when registering in WP. Defaults to $prefix + sanitized $type + index. Must be unique.
     * @type string $file Path relative to Plugin Root
     * @type string $ver Version added as a query string param. Defaults to filemtime() if $file is specified.
     * @type array $deps List of Dependencies (slugs)
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
        ];

        parent::__construct($data, $app);
    }

    public function enqueue()
    {
        $data = $this->data;

        if (!empty($data['callback']) && !$data['callback']()) {
            return;
        }

        wp_enqueue_style($data['slug'], $data['url'], $data['deps'], $data['ver']);
    }
}
