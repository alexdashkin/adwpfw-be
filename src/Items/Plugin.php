<?php

namespace AlexDashkin\Adwpfw\Items;

use AlexDashkin\Adwpfw\App;

/**
 * Plugin with Self-Update feature
 */
class Plugin extends Item
{
    /**
     * Constructor
     *
     * @param array $data {
     * @type string $path Path to the plugin's main file
     * @type string $package URL of the package
     * }
     */
    public function __construct(array $data, App $app)
    {
        $this->props = [
            'path' => [
                'required' => true,
            ],
            'package' => [
                'required' => true,
            ],
        ];

        parent::__construct($data, $app);

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

        $data = [
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

        $this->data = $data;
    }

    /**
     * Filter Update transient
     */
    public function register($transient)
    {
        if (!empty($transient->checked)) {
            $transient->response[$this->data['id']] = (object)$this->data;
        }

        return $transient;
    }
}
