<?php

namespace AlexDashkin\Adwpfw\Items\Basic;

use AlexDashkin\Adwpfw\App;

/**
 * Plugin Self-Update feature
 */
class Plugin extends Item
{
    /**
     * Constructor.
     *
     * @param array $data {
     * @type string $id ID for internal use. Defaults to sanitized $path.
     * @type string $path Path to the plugin's main file. Required.
     * @type string $package URL of the package. Required.
     * }
     * @throws \AlexDashkin\Adwpfw\Exceptions\AdwpfwException
     */
    public function __construct(array $data, App $app)
    {
        $props = [
            'id' => [
                'default' => $this->getDefaultId($data['path']),
            ],
            'path' => [
                'required' => true,
            ],
            'package' => [
                'required' => true,
            ],
        ];

        parent::__construct($data, $app, $props);

        require_once ABSPATH . 'wp-includes/plugin.php';
        require_once ABSPATH . 'wp-admin/includes/plugin.php';

        $file = plugin_basename($this->data['path']);
        $exploded = explode('/', $file);
        $newVer = '100.0.0';

        if ($pluginData = get_plugin_data($this->data['path'], false, false)) {
            $oldVer = $pluginData['Version'];
            $last = (int)substr($oldVer, -1);
            $newVer = substr($oldVer, 0, strlen($oldVer) - 1) . ++$last;
        }

        $this->data = [
            'id' => $file,
            'plugin' => $file,
            'slug' => $exploded[0],
            'new_version' => $newVer,
            'package' => $this->data['package'],
            'url' => '',
            'icons' => [],
            'banners' => [],
            'banners_rtl' => [],
            'tested' => '10.0.0',
            'compatibility' => new \stdClass(),
        ];
    }

    /**
     * Filter Update transient.
     *
     * @param object $transient Transient passed to the filter.
     * @return object Modified Transient.
     */
    public function register($transient)
    {
        if (!empty($transient->checked)) {
            $transient->response[$this->data['id']] = (object)$this->data;
        }

        return $transient;
    }
}
