<?php

namespace AlexDashkin\Adwpfw\Modules\WithItems;

use AlexDashkin\Adwpfw\App;
use AlexDashkin\Adwpfw\Items\Basic\Plugin;
use AlexDashkin\Adwpfw\Items\Basic\Theme;

/**
 * Plugins/Themes self-update feature.
 */
class Updater extends ModuleWithItems
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
     * Add Item.
     *
     * @param array $data
     *
     * @see Plugin::__construct(), Theme::__construct()
     *
     * @throws \AlexDashkin\Adwpfw\Exceptions\AdwpfwException
     */
    public function add(array $data)
    {
        switch ($data['type']) {
            case 'plugin':
                $this->items[] = new Plugin($data, $this->app);
                break;

            case 'theme':
                $this->items[] = new Theme($data, $this->app);
                break;
        }
    }

    /**
     * Hooks to register Items in WP.
     */
    protected function init()
    {
        add_filter('pre_set_site_transient_update_plugins', [$this, 'pluginUpdateCheck']);
        add_filter('pre_set_site_transient_update_themes', [$this, 'themeUpdateCheck']);
        add_action('upgrader_process_complete', [$this, 'onUpdate'], 10, 2);
    }

    /**
     * Filter Update transient.
     *
     * @param object $transient Transient passed to the filter.
     * @return object Modified Transient.
     */
    public function pluginUpdateCheck($transient)
    {
        foreach ($this->items as $item) {
            if ($item instanceof Plugin) {
                $transient = $item->register($transient);
            }
        }

        return $transient;
    }

    /**
     * Filter Update transient.
     *
     * @param object $transient Transient passed to the filter.
     * @return object Modified Transient.
     */
    public function themeUpdateCheck($transient)
    {
        foreach ($this->items as $item) {
            if ($item instanceof Theme) {
                $transient = $item->register($transient);
            }
        }

        return $transient;
    }

    /**
     * Hooked into "upgrader_process_complete".
     *
     * @param \WP_Upgrader $upgrader
     * @param array $data
     */
    public function onUpdate($upgrader, $data)
    {
        foreach ($this->items as $item) {
            $item->onUpdate($data);
        }
    }
}