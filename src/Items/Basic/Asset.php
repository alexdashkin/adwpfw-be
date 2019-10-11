<?php

namespace AlexDashkin\Adwpfw\Items\Basic;

use AlexDashkin\Adwpfw\App;

/**
 * Asset file (CSS/JS)
 */
abstract class Asset extends Item
{
    /**
     * Constructor.
     *
     * @param array $data
     * @param App $app
     * @param array $props
     * @throws \AlexDashkin\Adwpfw\Exceptions\AdwpfwException
     */
    protected function __construct(array $data, App $app, array $props = [])
    {
        $url = $version = null;

        if (!empty($data['file'])) {
            $file = $data['file'];
            $url = $app->config['baseUrl'] . $file;
            $path = $app->config['baseDir'] . $file;
            $version = file_exists($path) ? filemtime($path) : null;
        }

        $defaults = [
            'id' => [
                'default' => $this->getDefaultId($data),
            ],
            'af' => [
                'required' => true,
            ],
            'file' => [
                'default' => null,
            ],
            'url' => [
                'default' => $url,
            ],
            'ver' => [
                'default' => $version,
            ],
            'deps' => [
                'type' => 'array',
                'default' => [],
            ],
            'callback' => [
                'type' => 'callable',
                'default' => null,
            ],
        ];

        parent::__construct($data, $app, array_merge($defaults, $props));
    }

    protected function getDefaultId($data)
    {
        $id = $data['af'] . '-' . $data['type'] . '-' . uniqid();

        return esc_attr(sanitize_key($id));
    }

    /**
     * Enqueue Asset.
     */
    abstract public function enqueue();
}
