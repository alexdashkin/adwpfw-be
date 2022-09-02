<?php

namespace AlexDashkin\Adwpfw\Modules\Updater;

use AlexDashkin\Adwpfw\Modules\Module;

/**
 * Theme auto updater
 */
class Theme extends Module
{
    /**
     * @var array Theme update transient
     */
    private $transient;

    /**
     * Init Module
     */
    public function init()
    {
        $newVer = '100.0.0';

        $slug = $this->getProp('slug');

        if ($themeData = wp_get_theme($slug)) {
            $oldVer = $themeData->get('Version');
            $last = (int)substr($oldVer, -1);
            $newVer = substr($oldVer, 0, strlen($oldVer) - 1) . ++$last;
        }

        $this->transient = [
            'theme' => $slug,
            'new_version' => $newVer,
            'package' => $this->getProp('package'),
            'url' => '',
        ];

        $this->addHook('pre_set_site_transient_update_themes', [$this, 'register']);
        $this->addHook('upgrader_process_complete', [$this, 'onUpdate']);
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
            $transient->response[$this->getProp('slug')] = $this->transient;
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
        if ($data['action'] !== 'update' || $data['type'] !== 'theme'
            || empty($data['themes']) || !in_array($this->getProp('slug'), $data['themes'])) {
            return;
        }

        // Call callback
        if ($this->getProp('callback')) {
            $this->getProp('callback')();
        }
    }

    /**
     * Get prop definitions
     *
     * @return array
     */
    protected function getPropDefs(): array
    {
        return [
            'package' => [
                'type' => 'string',
                'required' => true,
            ],
            'slug' => [
                'type' => 'string',
                'default' => get_stylesheet(),
            ],
            'callback' => [
                'type' => 'callable',
            ],
        ];
    }
}
