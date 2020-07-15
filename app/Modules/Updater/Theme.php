<?php

namespace AlexDashkin\Adwpfw\Modules\Updater;

use AlexDashkin\Adwpfw\Abstracts\Module;
use AlexDashkin\Adwpfw\App;

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
        $this->validateData();

        $newVer = '100.0.0';

        $slug = $this->gp('slug');

        if ($themeData = wp_get_theme($slug)) {
            $oldVer = $themeData->version;
            $last = (int)substr($oldVer, -1);
            $newVer = substr($oldVer, 0, strlen($oldVer) - 1) . ++$last;
        }

        $this->transient = [
            'theme' => $slug,
            'new_version' => $newVer,
            'package' => $this->gp('package'),
            'url' => '',
        ];

        $this->hook('pre_set_site_transient_update_themes', [$this, 'register']);
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
            $transient->response[$this->gp('slug')] = $this->transient;
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
            || empty($data['themes']) || !in_array($this->gp('slug'), $data['themes'])) {
            return;
        }

        // Call callback
        if ($this->gp('callback')) {
            $this->gp('callback')();
        }

        // Clear Twig cache
        $twigPath = App::get('helpers')->getUploadsDir($this->gp('prefix') . '/twig');

        if (file_exists($twigPath)) {
            App::get('helpers')->rmDir($twigPath);
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
            'package' => [
                'required' => true,
            ],
            'slug' => [
                'default' => get_stylesheet(),
            ],
            'id' => [
                'default' => function ($data) {
                    return $data['slug'];
                },
            ],
            'callback' => [
                'type' => 'callable',
                'default' => null,
            ],
        ];
    }
}
