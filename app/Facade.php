<?php

namespace AlexDashkin\Adwpfw;

use AlexDashkin\Adwpfw\Abstracts\Module;
use AlexDashkin\Adwpfw\Exceptions\AppException;
use AlexDashkin\Adwpfw\Fields\Field;
use AlexDashkin\Adwpfw\Modules\AdminBar;
use AlexDashkin\Adwpfw\Modules\AdminPage;
use AlexDashkin\Adwpfw\Modules\AdminPageTab;
use AlexDashkin\Adwpfw\Modules\Api\AdminAjax;
use AlexDashkin\Adwpfw\Modules\Api\Rest;
use AlexDashkin\Adwpfw\Modules\Assets\Css;
use AlexDashkin\Adwpfw\Modules\Assets\Js;
use AlexDashkin\Adwpfw\Modules\Customizer\Panel;
use AlexDashkin\Adwpfw\Modules\Customizer\Section;
use AlexDashkin\Adwpfw\Modules\Customizer\Setting;
use AlexDashkin\Adwpfw\Modules\Hook;
use AlexDashkin\Adwpfw\Modules\Logger;
use AlexDashkin\Adwpfw\Modules\Metabox;
use AlexDashkin\Adwpfw\Modules\Notice;
use AlexDashkin\Adwpfw\Modules\PostState;
use AlexDashkin\Adwpfw\Modules\PostType;
use AlexDashkin\Adwpfw\Modules\ProfileSection;
use AlexDashkin\Adwpfw\Modules\Query;
use AlexDashkin\Adwpfw\Modules\Shortcode;
use AlexDashkin\Adwpfw\Modules\Sidebar;
use AlexDashkin\Adwpfw\Modules\Updater\Plugin;
use AlexDashkin\Adwpfw\Modules\Updater\Theme;
use AlexDashkin\Adwpfw\Modules\Widget;

class Facade
{
    /**
     * Add params to App global config
     *
     * @param array $config
     */
    public static function setConfig(array $config)
    {
        App::the()->setConfig($config);
    }

    /**
     * Get Framework Module
     *
     * @param string $moduleName
     * @param array $args
     * @return Module
     * @throws AppException
     */
    public static function get(string $moduleName, array $args = [])
    {
        return App::get($moduleName, $args);
    }

    /**
     * Add a log entry
     *
     * @param mixed $message Text or any other type including WP_Error.
     * @param array $values If passed, vsprintf() func is applied. Default [].
     * @param int $level 1 = Error, 2 = Warning, 4 = Notice. Default 4.
     */
    public static function log($message, array $values = [], int $level = 4)
    {
        /**
         * @var Logger $logger
         */
        $logger = App::get('logger');

        $logger->log($message, $values, $level);
    }

    /**
     * Perform a Database query
     *
     * @param string $table
     * @return Query
     */
    public static function db(string $table = ''): Query
    {
        return App::get('db')->table($table);
    }

    /**
     * Render Twig Template
     *
     * @param string $name Template file name without .twig.
     * @param array $args Args to be passed to the Template. Default [].
     * @return string Rendered Template
     */
    public static function twig($name, $args = []): string
    {
        return App::get('twig')->renderFile($name, $args);
    }

    /**
     * Add Hook (action/filter)
     *
     * @param string $tag
     * @param callable $callback
     * @param int $priority
     * @return Hook
     */
    public static function addHook(string $tag, callable $callback, int $priority = 10): Hook
    {
        return App::get(
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
    public static function addAdminBar(array $args): AdminBar
    {
        return App::get('admin_bar', $args);
    }

    /**
     * Add CSS asset
     *
     * @param array $args
     * @return Css
     */
    public static function addCss(array $args): Css
    {
        return App::get('asset.css', $args);
    }

    /**
     * Add JS asset
     *
     * @param array $args
     * @return Js
     */
    public static function addJs(array $args): Js
    {
        return App::get('asset.js', $args);
    }

    /**
     * Add assets
     *
     * @param array $args
     */
    public static function addAssets(array $args)
    {
        $args = App::get('helpers')->arrayMerge(
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

                    App::get(
                        'asset.' . $type,
                        [
                            'id' => $asset['id'] ?? '',
                            'type' => $af,
                            'url' => $asset['url'] ?? $args['url'] . $file,
                            'ver' => empty($asset['url']) ? filemtime($args['dir'] . $file) : null,
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
    public static function addAdminAjax(array $args): AdminAjax
    {
        return App::get('admin_ajax', $args);
    }

    /**
     * Add REST API Endpoint
     *
     * @param array $args
     * @return Rest
     */
    public static function addRestEndpoint(array $args): Rest
    {
        return App::get('rest', $args);
    }

    /**
     * Add Plugin Updater
     *
     * @param array $args
     * @return Plugin
     */
    public static function updaterPlugin(array $args): Plugin
    {
        return App::get('updater.plugin', $args);
    }

    /**
     * Add Theme Updater
     *
     * @param array $args
     * @return Theme
     */
    public static function updaterTheme(array $args): Theme
    {
        return App::get('updater.theme', $args);
    }

    /**
     * Add Sidebar
     *
     * @param array $args
     * @return Sidebar
     */
    public static function addSidebar(array $args): Sidebar
    {
        return App::get('sidebar', $args);
    }

    /**
     * Add Widget
     *
     * @param array $args
     * @return Widget
     */
    public static function addWidget(array $args): Widget
    {
        return App::get('widget', $args);
    }

    /**
     * Add Shortcode
     *
     * @param array $args
     * @return Shortcode
     */
    public static function addShortcode(array $args): Shortcode
    {
        return App::get('shortcode', $args);
    }

    /**
     * Add Admin Notice
     *
     * @param array $args
     * @return Notice
     */
    public static function addNotice(array $args): Notice
    {
        return App::get('notice', $args);
    }

    /**
     * Add Custom Post Type
     *
     * @param array $args
     * @return PostType
     */
    public static function addCpt(array $args): PostType
    {
        return App::get('post_type', $args);
    }

    /**
     * Add Post State
     *
     * @param int $postId
     * @param string $state
     * @return PostState
     */
    public static function addPostState(int $postId, string $state): PostState
    {
        return App::get(
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
    public static function addAdminPage(array $args): AdminPage
    {
        /**
         * @var AdminPage $adminPage
         */
        $adminPage = App::get('admin_page', $args);

        foreach ($args['tabs'] as $tabArgs) {
            /**
             * @var AdminPageTab $tab
             */
            $tab = App::get('admin_page_tab', $tabArgs);

            foreach ($tabArgs['fields'] as $fieldArgs) {
                /**
                 * @var Field $field
                 */
                $field = App::get('field.' . $fieldArgs['type'], $fieldArgs);

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
    public static function addMetabox(array $args): Metabox
    {
        /**
         * @var Metabox $metabox
         */
        $metabox = App::get('metabox', $args);

        foreach ($args['fields'] as $fieldArgs) {
            /**
             * @var Field $field
             */
            $field = App::get('field.' . $fieldArgs['type'], $fieldArgs);

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
    public static function addProfileSection(array $args): ProfileSection
    {
        /**
         * @var ProfileSection $section
         */
        $section = App::get('profile_section', $args);

        foreach ($args['fields'] as $fieldArgs) {
            /**
             * @var Field $field
             */
            $field = App::get('field.' . $fieldArgs['type'], $fieldArgs);

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
    public static function addCustomizerPanel(array $args): Panel
    {
        /**
         * @var Panel $panel
         */
        $panel = App::get('customizer.panel', $args);

        foreach ($args['sections'] as $sectionArgs) {
            /**
             * @var Section $section
             */
            $section = App::get('customizer.section', $sectionArgs);

            $section->sp('panel', $panel->gp('id'));

            foreach ($sectionArgs['settings'] as $settingArgs) {
                /**
                 * @var Setting $setting
                 */
                $setting = App::get('customizer.setting', $settingArgs);

                $setting->sp('section', $section->gp('id'));

                $section->addSetting($setting);
            }

            $panel->addSection($section);
        }

        return $panel;
    }

    /**
     * Get Uploads Dir Path
     *
     * @param string $path
     * @return string
     */
    public static function getUploadsDir(string $path = ''): string
    {
        return App::get('helpers')->getUploads($path);
    }

    /**
     * Get Uploads URL
     *
     * @param string $path
     * @return string
     */
    public static function getUploadsUrl(string $path = ''): string
    {
        return App::get('helpers')->getUploads($path, true);
    }

    /**
     * Return Success array.
     *
     * @param string $message Message. Default 'Done'.
     * @param array $data Data to return as JSON. Default [].
     * @param bool $echo Whether to echo Response right away without returning. Default false.
     * @return array
     */
    public static function returnSuccess(string $message = 'Done', array $data = [], bool $echo = false): array
    {
        return App::get('helpers')->returnSuccess($message, $data, $echo);
    }

    /**
     * Return Error array.
     *
     * @param string $message Error message. Default 'Unknown Error'.
     * @param bool $echo Whether to echo Response right away without returning. Default false.
     * @return array
     */
    public static function returnError(string $message = 'Unknown Error', bool $echo = false)
    {
        return App::get('helpers')->returnError($message, $echo);
    }
}