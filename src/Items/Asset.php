<?php

namespace AlexDashkin\Adwpfw\Items;

use AlexDashkin\Adwpfw\App;
use AlexDashkin\Adwpfw\Exceptions\AdwpfwException;

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
     * @throws AdwpfwException
     */
    protected function __construct(App $app, array $data, array $props = [])
    {
        $url = $version = null;

        if (!empty($data['file'])) {
            $file = $data['file'];
            $path = $app->config['baseDir'] . $file;
            $url = $app->config['baseUrl'] . $file;
            $version = file_exists($path) ? filemtime($path) : null;
        }

        $defaults = [
            'id' => [
                'default' => $this->getDefaultId($data['af'] . '-' . $data['type']),
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

        parent::__construct($app, $data, array_merge($defaults, $props));
    }

    /**
     * Enqueue Asset.
     */
    abstract public function enqueue();
}
