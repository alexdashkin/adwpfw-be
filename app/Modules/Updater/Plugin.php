<?php

namespace AlexDashkin\Adwpfw\Modules\Updater;

use AlexDashkin\Adwpfw\Modules\Module;

class Plugin extends Module
{
    /**
     * @var object Plugin update transient
     */
    private $transient;

    /**
     * Init Module
     */
    public function init()
    {
        $this->validateData();

        require_once ABSPATH . 'wp-includes/plugin.php';
        require_once ABSPATH . 'wp-admin/includes/plugin.php';

        $file = plugin_basename($this->gp('file'));
        $exploded = explode('/', $file);
        $newVer = '100.0.0';

        if ($pluginData = get_plugin_data($this->gp('file'), false, false)) {
            $oldVer = $pluginData['Version'];
            $last = (int)substr($oldVer, -1);
            $newVer = substr($oldVer, 0, strlen($oldVer) - 1) . ++$last;
        }

        $this->transient = [
            'id' => $file,
            'slug' => $exploded[0],
            'plugin' => $file,
            'new_version' => $newVer,
            'package' => $this->gp('package'),
            'url' => '',
            'icons' => [],
            'banners' => [],
            'banners_rtl' => [],
            'tested' => '10.0.0',
            'compatibility' => new \stdClass(),
        ];

        $this->hook('pre_set_site_transient_update_plugins', [$this, 'register']);
        $this->hook('upgrader_process_complete', [$this, 'onUpdate']);
    }

    /**
     * Filter Update transient
     *
     * @param object $transient Transient passed to the filter
     * @return object Modified Transient
     */
    public function register($transient)
    {
        if (!empty($transient->checked)) {
            $transient->response[$this->transient['id']] = (object)$this->transient;
        }

        return $transient;
    }

    /**
     * Hooked into "upgrader_process_complete"
     *
     * @param \WP_Upgrader $upgrader
     * @param array $data
     */
    public function onUpdate(\WP_Upgrader $upgrader, array $data)
    {
        if ($data['action'] !== 'update' || $data['type'] !== 'plugin'
            || empty($data['plugins']) || !in_array($this->transient['id'], $data['plugins'])) {
            return;
        }

        // Call callback
        if ($this->gp('callback')) {
            $this->gp('callback')();
        }

        // Clear Twig cache
        $twigPath = $this->m('helpers')->getUploadsDir($this->gp('prefix') . '/twig');

        if (file_exists($twigPath)) {
            $this->m('helpers')->rmDir($twigPath);
        }
    }

    /**
     * Get Class props
     *
     * @return array
     */
    protected function getInitialPropDefs(): array
    {
        return [
            'prefix' => [
                'required' => true,
            ],
            'file' => [
                'required' => true,
            ],
            'package' => [
                'required' => true,
            ],
            'id' => [
                'default' => function ($data) {
                    return $data['file'];
                },
            ],
            'callback' => [
                'type' => 'callable',
                'default' => null,
            ],
        ];
    }
}
