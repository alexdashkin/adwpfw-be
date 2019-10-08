<?php

namespace AlexDashkin\Adwpfw\Modules;

use AlexDashkin\Adwpfw\App;
use AlexDashkin\Adwpfw\Items\Plugin;
use AlexDashkin\Adwpfw\Items\Theme;

/**
 * Plugins/Themes self-update feature
 */
class Updater extends ItemsModule
{
    /**
     * Constructor
     *
     * @param App $app
     */
    public function __construct(App $app)
    {
        parent::__construct($app);
    }

    /**
     * Add an Item
     *
     * @param array $data {
     * @type string $type plugin/theme. Required.
     * }
     * @param App $app
     */
    public function add(array $data, App $app)
    {
        switch ($data['type']) {
            case 'plugin':
                $this->items[] = new Plugin($data, $app);
                break;

            case 'theme':
                $this->items[] = new Theme($data, $app);
                break;
        }
    }

    /**
     * Hooks to register Items in WP
     */
    protected function hooks()
    {
        add_filter('pre_set_site_transient_update_plugins', [$this, 'pluginUpdateCheck']);
        add_filter('pre_set_site_transient_update_themes', [$this, 'themeUpdateCheck']);
    }

    public function pluginUpdateCheck($transient)
    {
        foreach ($this->items as $item) {
            if ($item instanceof Plugin) {
                $transient = $item->register($transient);
            }
        }

        return $transient;
    }

    public function themeUpdateCheck($transient)
    {
        foreach ($this->items as $item) {
            if ($item instanceof Theme) {
                $transient = $item->register($transient);
            }
        }

        return $transient;
    }

}