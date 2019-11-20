<?php

namespace AlexDashkin\Adwpfw\Items;

use AlexDashkin\Adwpfw\App;
use AlexDashkin\Adwpfw\Exceptions\AdwpfwException;

/**
 * Plugin Self-Update feature
 */
class Plugin extends Item
{
    /**
     * @var array Update transient data
     */
    private $transient;

    /**
     * Constructor.
     *
     * @param App $app
     * @param array $data {
     * @type string $id ID for internal use. Defaults to sanitized $path.
     * @type string $path Path to the plugin's main file. Required.
     * @type string $package URL of the package. Required.
     * @type callable $update_callback Function to call on plugin update.
     * }
     *
     * @throws AdwpfwException
     */
    public function __construct(App $app, array $data)
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
            'update_callback' => [
                'type' => 'callable',
                'default' => null,
            ],
        ];

        parent::__construct($app, $data, $props);

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

        $this->transient = [
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
            $transient->response[$this->transient['id']] = (object)$this->transient;
        }

        return $transient;
    }

    /**
     * Hooked into "upgrader_process_complete".
     *
     * @param array $data
     */
    public function onUpdate($data)
    {
        if (!$this->data['update_callback'] || $data['action'] !== 'update' || $data['type'] !== 'plugin'
            || empty($data['plugins']) || !in_array($this->transient['id'], $data['plugins'])) {
            return;
        }

        $this->data['update_callback']();
    }
}
