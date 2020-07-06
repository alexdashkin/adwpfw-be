<?php

namespace AlexDashkin\Adwpfw\Items\Updater;

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
        $newVer = '100.0.0';

        $slug = $this->get('slug');

        if ($themeData = wp_get_theme($slug)) {
            $oldVer = $themeData->version;
            $last = (int)substr($oldVer, -1);
            $newVer = substr($oldVer, 0, strlen($oldVer) - 1) . ++$last;
        }

        $this->transient = [
            'theme' => $slug,
            'new_version' => $newVer,
            'package' => $this->get('package'),
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
            $transient->response[$this->get('slug')] = $this->transient;
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
            || empty($data['themes']) || !in_array($this->get('slug'), $data['themes'])) {
            return;
        }

        // Call callback
        if ($this->get('callback')) {
            $this->get('callback')();
        }

        // Clear Twig cache
        $twigPath = App::get('helpers')->getUploadsDir($this->get('prefix') . '/twig');

        if (file_exists($twigPath)) {
            App::get('helpers')->rmDir($twigPath);
        }
    }

    /**
     * Get Class props
     *
     * @return array
     */
    protected function props(): array
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
