<?php

namespace AlexDashkin\Adwpfw\Admin;

/**
 * Plugins/Themes self-update feature
 */
class Updater extends \AlexDashkin\Adwpfw\Common\Base
{
    private $plugins = [];
    private $themes = [];

    public function __construct($app)
    {
        parent::__construct($app);
    }

    public function run()
    {
        add_filter('pre_set_site_transient_update_plugins', [$this, 'pluginUpdateCheck'], 10, 1);
        add_filter('pre_set_site_transient_update_themes', [$this, 'themeUpdateCheck'], 10, 1);
    }

    /**
     * Add Self-Update feature for a plugin
     *
     * @param array $plugin {
     * @type string $path Path to the plugin's main file
     * @type string $package URL of the package
     * }
     */
    public function addPlugin(array $plugin)
    {
        require_once ABSPATH . 'wp-includes/plugin.php';
        require_once ABSPATH . 'wp-admin/includes/plugin.php';

        $file = plugin_basename($plugin['path']);
        $exploded = explode('/', $file);
        $newVer = '100.0.0';

        if ($data = get_plugin_data($plugin['path'], false, false)) {
            $oldVer = $data['Version'];
            $last = (int)substr($oldVer, -1);
            $newVer = substr($oldVer, 0, strlen($oldVer) - 1) . ++$last;
        }

        $data = [
            'id' => $file,
            'plugin' => $file,
            'slug' => $exploded[0],
            'new_version' => $newVer,
            'package' => $plugin['package'],
            'url' => '',
            'icons' => [],
            'banners' => [],
            'banners_rtl' => [],
            'tested' => '10.0.0',
            'compatibility' => new \stdClass(),
        ];

        $this->plugins[] = $data;
    }

    /**
     * Add Self-Update feature for a theme
     *
     * @param array $theme {
     * @type string $path Path to the theme's main file
     * @type string $package URL of the package
     * }
     */
    public function addTheme($theme)
    {
        $newVer = '100.0.0';

        if ($data = wp_get_theme($theme['name'])) {
            $oldVer = $data->version;
            $last = (int)substr($oldVer, -1);
            $newVer = substr($oldVer, 0, strlen($oldVer) - 1) . ++$last;
        }

        $data = [
            'name' => $theme['name'],
            'theme' => $theme['name'],
            'new_version' => $newVer,
            'package' => $theme['package'],
            'url' => '',
        ];

        $this->themes[] = $data;
    }

    public function pluginUpdateCheck($transient)
    {
        if (!empty($transient->checked)) {
            foreach ($this->plugins as $plugin) {
                $transient->response[$plugin['id']] = (object)$plugin;
            }
        }

        return $transient;
    }

    public function themeUpdateCheck($transient)
    {
        if (!empty($transient->checked)) {
            foreach ($this->themes as $theme) {
                $transient->response[$theme['name']] = $theme;
            }
        }

        return $transient;
    }

}