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
        $version = null;

        if (!empty($data['file'])) {
            $file = $data['file'];
            $path = $app->config['baseDir'] . $file;
            $version = file_exists($path) ? filemtime($path) : null;
        }

        $defaults = [
            'id' => [
                'default' => $this->getDefaultId($id = $data['af'] . '-' . $data['type'] . '-' . uniqid()),
            ],
            'af' => [
                'required' => true,
            ],
            'file' => [
                'default' => null,
            ],
            'url' => [
                'default' => null,
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
