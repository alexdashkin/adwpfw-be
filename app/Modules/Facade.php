<?php

namespace AlexDashkin\Adwpfw\Modules;

use AlexDashkin\Adwpfw\App;
use AlexDashkin\Adwpfw\Exceptions\AppException;
use AlexDashkin\Adwpfw\Fields\Field;
use AlexDashkin\Adwpfw\Modules\Api\AdminAjax;
use AlexDashkin\Adwpfw\Modules\Api\Rest;
use AlexDashkin\Adwpfw\Modules\Assets\Css;
use AlexDashkin\Adwpfw\Modules\Assets\Js;
use AlexDashkin\Adwpfw\Modules\Customizer\Panel;
use AlexDashkin\Adwpfw\Modules\Customizer\Section;
use AlexDashkin\Adwpfw\Modules\Customizer\Setting;
use AlexDashkin\Adwpfw\Modules\Updater\Plugin;
use AlexDashkin\Adwpfw\Modules\Updater\Theme;

/**
 * Magic methods delegated to Helpers
 *
 * @method mixed getPostMeta(string $name, int $postId = 0) Get prefixed post meta
 * @method mixed getUserMeta(string $name, int $userId = 0) Get prefixed user meta
 * @method int|bool updatePostMeta(string $name, $value, int $postId = 0) Update prefixed post meta
 * @method int|bool updateUserMeta(string $name, $value, int $userId = 0) Update prefixed user meta
 * @method mixed getOption(string $name) Get prefixed option
 * @method bool updateOption(string $name, $value) Update prefixed option
 * @method mixed setting(string $key, string $optionName = 'settings') Get Setting value
 * @method mixed cacheGet(string $name) Get prefixed cache entry
 * @method mixed cacheSet(string $name, $value) Set prefixed cache entry
 * @method mixed pr($result) WP_Error handler
 * @method array returnSuccess(string $message = 'Done', array $data = [], bool $echo = false) Return Success array
 * @method array returnError(string $message = 'Unknown Error', bool $echo = false) Return Error array
 * @method array arraySearch(array $array, array $conditions, bool $single = false) Search in an array
 * @method string getUploadsDir(string $path = '') Get path to the WP Uploads dir with trailing slash
 * @method string getUploadsUrl(string $path = '') Get URL of the WP Uploads dir with trailing slash
 * @method string getOutput(callable $func, array $args = []) Get output of a function
 */
class Facade extends Module
{
    /**
     * Get Framework Facade
     * Used as a factory for Framework App
     * Returns self instance as a FW Module
     *
     * @return self
     * @throws AppException
     */
    public static function getInstance(): self
    {
        $app = new App();

        return $app->getModule('facade');
    }

    /**
     * Add params to App global config
     *
     * @param array $config
     */
    public function setConfig(array $config)
    {
        $this->app->setConfig($config);
    }

    /**
     * Get Framework Module
     *
     * @param string $moduleName
     * @param array $args
     * @return object
     */
    public function get(string $moduleName, array $args = [])
    {
        return $this->m($moduleName, $args);
    }

    /**
     * Add a log entry
     *
     * @param mixed $message Text or any other type including WP_Error.
     * @param array $values If passed, vsprintf() func is applied. Default [].
     * @param int $level 1 = Error, 2 = Warning, 4 = Notice. Default 4.
     */
    public function log($message, array $values = [], int $level = 4)
    {
        /**
         * @var Logger $logger
         */
        $logger = $this->m('logger');

        $logger->log($message, $values, $level);
    }

    /**
     * Perform a Database query
     *
     * @param string $table
     * @return Query
     */
    public function db(string $table = ''): Query
    {
        return $this->m('db')->table($table);
    }

    /**
     * Get Prefixed Table Name
     *
     * @param string $name
     * @return string
     */
    public function getTableName(string $name): string
    {
        return $this->m('db')->getTableName($name);
    }

    /**
     * Render Twig Template
     *
     * @param string $name Template file name without .twig.
     * @param array $args Args to be passed to the Template. Default [].
     * @return string Rendered Template
     */
    public function twig(string $name, array $args = []): string
    {
        return $this->m('twig')->renderFile($name, $args);
    }

    /**
     * Add Hook (action/filter)
     *
     * @param string $tag
     * @param callable $callback
     * @param int $priority
     * @return Hook
     */
    public function addHook(string $tag, callable $callback, int $priority = 10): Hook
    {
        return $this->m(
            'hook',
            [
                'tag' => $tag,
                'callback' => $callback,
                'priority' => $priority,
            ]
        );
    }

    /**
     * Add Admin Bar
     *
     * @param array $args
     * @return AdminBar
     */
    public function addAdminBar(array $args): AdminBar
    {
        return $this->m('admin_bar', $args);
    }

    /**
     * Add Dashboard Widget
     *
     * @param array $args
     * @return DbWidget
     */
    public function addDbWidget(array $args): DbWidget
    {
        return $this->m('dashboard_widget', $args);
    }

    /**
     * Add Cron Job
     *
     * @param array $args
     * @return CronJob
     */
    public function addCronJob(array $args): CronJob
    {
        return $this->m('cron', $args);
    }

    /**
     * Add CSS asset
     *
     * @param array $args
     * @return Css
     */
    public function addCss(array $args): Css
    {
        return $this->m('asset.css', $args);
    }

    /**
     * Add JS asset
     *
     * @param array $args
     * @return Js
     */
    public function addJs(array $args): Js
    {
        return $this->m('asset.js', $args);
    }

    /**
     * Add assets
     *
     * @param array $args
     */
    public function addAssets(array $args)
    {
        $args = $this->m('helpers')->arrayMerge(
            [
                'admin' => ['css' => [], 'js' => []],
                'front' => ['css' => [], 'js' => []],
            ],
            $args
        );

        foreach (['admin', 'front'] as $af) {
            foreach (['css', 'js'] as $type) {
                foreach ($args[$af][$type] as $asset) {
                    $file = is_array($asset) && !empty($asset['file']) ? $asset['file'] : $asset;

                    $this->m(
                        'asset.' . $type,
                        [
                            'id' => $asset['id'] ?? '',
                            'type' => $af,
                            'url' => $asset['url'] ?? $args['url'] . $file,
                            'ver' => empty($asset['url']) ? filemtime($args['dir'] . $file) : null,
                            'deps' => $asset['deps'] ?? [],
                            'callback' => $asset['callback'] ?? [],
                            'localize' => $asset['localize'] ?? [],
                        ]
                    );
                }
            }
        }
    }

    /**
     * Add AdminAjax action
     *
     * @param array $args
     * @return AdminAjax
     */
    public function addAdminAjax(array $args): AdminAjax
    {
        return $this->m('admin_ajax', $args);
    }

    /**
     * Add REST API Endpoint
     *
     * @param array $args
     * @return Rest
     */
    public function addRestEndpoint(array $args): Rest
    {
        return $this->m('rest', $args);
    }

    /**
     * Add Plugin Updater
     *
     * @param array $args
     * @return Plugin
     */
    public function updaterPlugin(array $args): Plugin
    {
        return $this->m('updater.plugin', $args);
    }

    /**
     * Add Theme Updater
     *
     * @param array $args
     * @return Theme
     */
    public function updaterTheme(array $args): Theme
    {
        return $this->m('updater.theme', $args);
    }

    /**
     * Add Sidebar
     *
     * @param array $args
     * @return Sidebar
     */
    public function addSidebar(array $args): Sidebar
    {
        return $this->m('sidebar', $args);
    }

    /**
     * Add Widget
     *
     * @param array $args
     * @return Widget
     */
    public function addWidget(array $args): Widget
    {
        return $this->m('widget', $args);
    }

    /**
     * Add Shortcode
     *
     * @param array $args
     * @return Shortcode
     */
    public function addShortcode(array $args): Shortcode
    {
        return $this->m('shortcode', $args);
    }

    /**
     * Add Admin Notice
     *
     * @param array $args
     * @return Notice
     */
    public function addNotice(array $args): Notice
    {
        return $this->m('notice', $args);
    }

    /**
     * Add Custom Post Type
     *
     * @param array $args
     * @return PostType
     */
    public function addCpt(array $args): PostType
    {
        return $this->m('post_type', $args);
    }

    /**
     * Add Post State
     *
     * @param int $postId
     * @param string $state
     * @return PostState
     */
    public function addPostState(int $postId, string $state): PostState
    {
        return $this->m(
            'post_state',
            [
                'post_id' => $postId,
                'state' => $state,
            ]
        );
    }

    /**
     * Add Admin Page
     *
     * @param array $args
     * @return AdminPage
     */
    public function addAdminPage(array $args): AdminPage
    {
        /**
         * @var AdminPage $adminPage
         */
        $adminPage = $this->m('admin_page', $args);

        if (empty($args['tabs'])) {
            return $adminPage;
        }

        foreach ($args['tabs'] as $tabArgs) {
            /**
             * @var AdminPageTab $tab
             */
            $tab = $this->m('admin_page_tab', $tabArgs);

            foreach ($tabArgs['fields'] as $fieldArgs) {
                /**
                 * @var Field $field
                 */
                $field = $this->m('field.' . $fieldArgs['type'], $fieldArgs);

                $field->spm(
                    [
                        'layout' => 'admin-page-field',
                        'form' => $tab->gp('slug'),
                    ]
                );

                $tab->addField($field);
            }

            $adminPage->addTab($tab);
        }

        return $adminPage;
    }

    /**
     * Add Metabox
     *
     * @param array $args
     * @return Metabox
     */
    public function addMetabox(array $args): Metabox
    {
        /**
         * @var Metabox $metabox
         */
        $metabox = $this->m('metabox', $args);

        foreach ($args['fields'] as $fieldArgs) {
            /**
             * @var Field $field
             */
            $field = $this->m('field.' . $fieldArgs['type'], $fieldArgs);

            $field->spm(
                [
                    'layout' => 'metabox-field',
                    'form' => $metabox->gp('id'),
                ]
            );

            $metabox->addField($field);
        }

        return $metabox;
    }

    /**
     * Add User Profile Editor Section
     *
     * @param array $args
     * @return ProfileSection
     */
    public function addProfileSection(array $args): ProfileSection
    {
        /**
         * @var ProfileSection $section
         */
        $section = $this->m('profile_section', $args);

        foreach ($args['fields'] as $fieldArgs) {
            /**
             * @var Field $field
             */
            $field = $this->m('field.' . $fieldArgs['type'], $fieldArgs);

            $field->spm(
                [
                    'layout' => 'profile-field',
                    'form' => $section->gp('id'),
                    'class' => 'regular-text',
                ]
            );

            $section->addField($field);
        }

        return $section;
    }

    /**
     * Add Customizer Panel
     *
     * @param array $args
     * @return Panel
     */
    public function addCustomizerPanel(array $args): Panel
    {
        /**
         * @var Panel $panel
         */
        $panel = $this->m('customizer.panel', $args);

        foreach ($args['sections'] as $sectionArgs) {
            /**
             * @var Section $section
             */
            $section = $this->m('customizer.section', $sectionArgs);

            $section->sp('panel', $panel->gp('id'));

            foreach ($sectionArgs['settings'] as $settingArgs) {
                /**
                 * @var Setting $setting
                 */
                $setting = $this->m('customizer.setting', $settingArgs);

                $setting->sp('section', $section->gp('id'));

                $section->addSetting($setting);
            }

            $panel->addSection($section);
        }

        return $panel;
    }

    /**
     * Call a method in a loop (e.g. to add multiple items)
     *
     * @param string $method
     * @param array $args
     * @return array
     * @throws AppException
     */
    public function addMany(string $method, array $args): array
    {
        // If singular method exists - call it in a loop
        if (method_exists($this, $method) && is_array($args)) {
            $return = [];

            foreach ($args as $item) {
                $return[] = $this->$method($item);
            }
        } else {
            throw new AppException(sprintf('Method %s not found', $method));
        }

        return $return;
    }

    /**
     * Delegate call to Helpers OR
     * Magic call of singular methods for plural calls
     * E.g. addAdminBars() etc.
     *
     * @param string $method
     * @param array $args
     * @return mixed
     * @throws AppException
     */
    public function __call(string $method, array $args)
    {
        // Helpers shorthand
        $helpers = $this->m('helpers');

        // If method is found in Helpers - run it
        if (method_exists($helpers, $method)) {
            return $helpers->$method(...$args);

        } else {
            // Get last char of the called method
            $lastChar = substr($method, -1);

            // Get method name without last char
            $singularMethod = substr($method, 0, -1);

            // If singular method exists - call it in a loop
            if ('s' === $lastChar && method_exists($this, $singularMethod) && is_array($args[0])) {
                $return = [];

                foreach ($args[0] as $item) {
                    $return[] = $this->$singularMethod($item);
                }
            } else {
                throw new AppException(sprintf('Method %s not found', $singularMethod));
            }
        }

        return $return;
    }

    /**
     * Mock to be able to extend parent Module
     *
     * @return array
     */
    protected function getInitialPropDefs(): array
    {
        return [];
    }
}