<?php

namespace AlexDashkin\Adwpfw;

use AlexDashkin\Adwpfw\Abstracts\Module;
use AlexDashkin\Adwpfw\Exceptions\AppException;
use AlexDashkin\Adwpfw\Fields\Field;
use AlexDashkin\Adwpfw\Items\AdminPage;
use AlexDashkin\Adwpfw\Items\AdminPageTab;
use AlexDashkin\Adwpfw\Items\Customizer\Panel;
use AlexDashkin\Adwpfw\Items\Customizer\Section;
use AlexDashkin\Adwpfw\Items\Customizer\Setting;
use AlexDashkin\Adwpfw\Items\Metabox;
use AlexDashkin\Adwpfw\Items\ProfileSection;
use AlexDashkin\Adwpfw\Modules\Logger;

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
     */
    public static function db(string $table)
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
     */
    public static function addHook(string $tag, callable $callback, int $priority = 10)
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
     */
    public static function addAdminBar(array $args)
    {
        return App::get('admin_bar', $args);
    }

    /**
     * Add CSS asset
     *
     * @param array $args
     */
    public static function addCss(array $args)
    {
        return App::get('asset.css', $args);
    }

    /**
     * Add JS asset
     *
     * @param array $args
     */
    public static function addJs(array $args)
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
     */
    public static function addAdminAjax(array $args)
    {
        return App::get('admin_ajax', $args);
    }

    /**
     * Add REST API Endpoint
     *
     * @param array $args
     */
    public static function addRestEndpoint(array $args)
    {
        return App::get('rest', $args);
    }

    /**
     * Add Plugin Updater
     *
     * @param array $args
     */
    public static function updaterPlugin(array $args)
    {
        return App::get('updater.plugin', $args);
    }

    /**
     * Add Theme Updater
     *
     * @param array $args
     */
    public static function updaterTheme(array $args)
    {
        return App::get('updater.theme', $args);
    }

    /**
     * Add Sidebar
     *
     * @param array $args
     */
    public static function addSidebar(array $args)
    {
        return App::get('sidebar', $args);
    }

    /**
     * Add Widget
     *
     * @param array $args
     */
    public static function addWidget(array $args)
    {
        return App::get('widget', $args);
    }

    /**
     * Add Shortcode
     *
     * @param array $args
     */
    public static function addShortcode(array $args)
    {
        return App::get('shortcode', $args);
    }

    /**
     * Add Admin Notice
     *
     * @param array $args
     */
    public static function addNotice(array $args)
    {
        return App::get('notice', $args);
    }

    /**
     * Add Custom Post Type
     *
     * @param array $args
     */
    public static function addCpt(array $args)
    {
        return App::get('post_type', $args);
    }

    /**
     * Add Post State
     *
     * @param int $postId
     * @param string $state
     */
    public static function addPostState(int $postId, string $state)
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
     */
    public static function addAdminPage(array $args)
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

                $field->setMany(
                    [
                        'layout' => 'admin-page-field',
                        'form' => $tab->get('slug'),
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
     */
    public static function addMetabox(array $args)
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

            $field->setMany(
                [
                    'layout' => 'metabox-field',
                    'form' => $metabox->get('id'),
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
     */
    public static function addProfileSection(array $args)
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

            $field->setMany(
                [
                    'layout' => 'profile-field',
                    'form' => $section->get('id'),
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
     */
    public static function addCustomizerPanel(array $args)
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

            $section->set('panel', $panel->get('id'));

            foreach ($sectionArgs['settings'] as $settingArgs) {
                /**
                 * @var Setting $setting
                 */
                $setting = App::get('customizer.setting', $settingArgs);

                $setting->set('section', $section->get('id'));

                $section->addSetting($setting);
            }

            $panel->addSection($section);
        }

        return $panel;
    }
}