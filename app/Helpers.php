<?php

namespace AlexDashkin\Adwpfw;

use AlexDashkin\Adwpfw\{Exceptions\AppException, Modules\AdminPage, Modules\AdminPageTab, Modules\Assets\Css, Modules\Assets\Js, Modules\Customizer\Panel, Modules\Customizer\Section, Modules\Customizer\Setting, Modules\Hook, Modules\Metabox, Modules\Shortcode, Modules\Widget};

/**
 * Helper functions
 */
class Helpers
{
    /**
     * Send Email
     *
     * @param array $data
     * @throws AppException
     */
    public static function sendMail(array $data)
    {
        // Check required fields
        foreach (['from_name', 'from_email', 'to_email', 'subject', 'body'] as $field) {
            if (empty($data[$field])) {
                throw new AppException(sprintf('Field "%s" is required', $field));
            }
        }

        if (!is_email($data['to_email'])) {
            throw new AppException('Invalid email');
        }

        $fromName = $data['from_name'];
        $fromEmail = $data['from_email'];
        $toEmail = $data['to_email'];
        $subject = $data['subject'];
        $body = $data['body'];
        $headers = sprintf("From: %s <%s>", $fromName, $fromEmail);

        if (!empty($data['format']) && 'html' === $data['format']) {
            add_filter(
                'wp_mail_content_type',
                function () {
                    return 'text/html';
                }
            );
        }

        // Catch email errors
        add_filter(
            'wp_mail_failed',
            function (\WP_Error $error) {
                throw new AppException($error->get_error_message());
            }
        );

        if (!wp_mail($toEmail, $subject, $body, $headers)) {
            throw new AppException('Unable to send email');
        }
    }

    /**
     * Get Prefixed Table Name
     *
     * @param string $name
     * @return string
     */
    public static function getTableName(string $name): string
    {
        return $GLOBALS['wpdb']->prefix . $name;
    }

    /**
     * Render PHP Template
     *
     * @param string $name Template file name with/without path
     * @param array $args Args to be passed to the Template. Default [].
     * @return string Rendered Template
     */
    public static function render(string $name, array $args = []): string
    {
        $fileName = $name . '.php';

        $paths = [$fileName, __DIR__ . '/../templates/' . $fileName];

        foreach ($paths as $path) {
            if (file_exists($path)) {
                ob_start();
                extract($args);
                include $path;
                return ob_get_clean();
            }
        }

        // Not found
        return sprintf('Template "%s" not found', $name);
    }

    /**
     * Render Twig Template
     *
     * @param string $name Template file name with/without path
     * @param array $args Args to be passed to the Template. Default [].
     * @return string Rendered Template
     */
    public static function renderTwig(string $name, array $args = []): string
    {
        try {
            return Twig::renderFile($name . '.twig', $args);
        } catch (\Exception $e) {
            $message = $e->getMessage();
            Logger::log($message);
            return 'Unable to render Template: ' . $message;
        }
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
        return new Hook(
            [
                'tag' => $tag,
                'callback' => $callback,
                'priority' => $priority,
            ]
        );
    }

    /**
     * Search in an array.
     *
     * @param array $array Array to parse.
     * @param array $conditions . Array of key-value pairs to compare with.
     * @param bool $single Whether to return a single item.
     * @return mixed
     */
    public static function arraySearch(array $array, array $conditions, bool $single = false)
    {
        $found = [];
        $searchValue = end($conditions);
        $searchField = key($conditions);
        array_pop($conditions);

        foreach ($array as $key => $value) {
            if (isset($value[$searchField]) && $value[$searchField] == $searchValue) {
                $found[$key] = $value;
            }
        }

        if (0 === count($found)) {
            return [];
        }

        if (0 !== count($conditions)) {
            $found = self::arraySearch($found, $conditions);
        }

        return $single ? reset($found) : $found;
    }

    /**
     * Filter an array.
     *
     * @param array $array Array to parse.
     * @param array $conditions Array of key-value pairs to compare with.
     * @param bool $single Whether to return a single item.
     * @return mixed
     */
    public static function arrayFilter(array $array, array $conditions, bool $single = false)
    {
        $new = [];
        foreach ($array as $item) {
            foreach ($conditions as $key => $value) {
                if ($item[$key] == $value) {
                    $new[] = $item;
                    if ($single) {
                        return $item;
                    }
                }
            }
        }

        return $new;
    }

    /**
     * Remove duplicates by key.
     *
     * @param array $array Array to parse.
     * @param string $key Key to search duplicates by.
     * @return array Filtered array.
     */
    public static function arrayUniqueByKey(array $array, string $key): array
    {
        $existing = [];

        foreach ($array as $arrayKey => $value) {
            if (in_array($value[$key], $existing)) {
                unset($array[$arrayKey]);
            } else {
                $existing[] = $value[$key];
            }
        }

        return $array;
    }

    /**
     * Transform an array.
     *
     * @param array $array Array to parse.
     * @param array $keys Keys to keep.
     * @param string $index Key to be used as index.
     * @param string $sort Key to sort by.
     * @return array
     */
    public static function arrayParse(array $array, array $keys = [], string $index = '', string $sort = ''): array
    {
        $new = [];

        foreach ($array as $item) {
            $row = [];

            if ($keys) {
                if (1 === count($keys)) {
                    $row = $item[reset($keys)];
                } else {
                    foreach ($keys as $key) {
                        if (is_array($key)) {
                            $row[current($key)] = $item[key($key)];
                        } else {
                            $row[$key] = $item[$key];
                        }
                    }
                }
            } else {
                $row = $item;
            }

            if ($index) {
                $new[$item[$index]] = $row;
            } else {
                $new[] = $row;
            }
        }

        if ($sort) {
            uasort(
                $new,
                function ($a, $b) use ($sort) {
                    return $a[$sort] > $b[$sort] ? 1 : -1;
                }
            );
        }

        return $new;
    }

    /**
     * Sort an array by key.
     *
     * @param array $array Array to parse.
     * @param string $key Key to sort by.
     * @param bool $keepKeys Keep key=>value assigment when sorting
     * @return array Resulting array.
     */
    public static function arraySortByKey(array $array, string $key, bool $keepKeys = false): array
    {
        $func = $keepKeys ? 'uasort' : 'usort';
        $func(
            $array,
            function ($a, $b) use ($key) {
                return $a[$key] > $b[$key] ? 1 : -1;
            }
        );

        return $array;
    }

    /**
     * Arrays deep merge.
     *
     * @param array $arr1
     * @param array $arr2
     * @return array
     */
    public static function arrayMerge(array $arr1, array $arr2): array
    {
        foreach ($arr2 as $key => $value) {
            if (!array_key_exists($key, $arr1)) {
                $arr1[$key] = $value;
                continue;
            }

            if (is_array($arr1[$key]) && is_array($value)) {
                $arr1[$key] = self::arrayMerge($arr1[$key], $value);
            } else {
                $arr1[$key] = $value;
            }
        }

        return $arr1;
    }

    /**
     * Add an element to an array if not exists.
     *
     * @param array $where Array to add to.
     * @param array $what Array to be added.
     * @return array
     */
    public static function arrayAddNonExistent(array $where, array $what): array
    {
        foreach ($what as $name => $value) {
            if (!isset($where[$name])) {
                $where[$name] = $value;
            } elseif (is_array($value)) {
                $where[$name] = self::arrayAddNonExistent($where[$name], $value);
            }
        }

        return $where;
    }

    /**
     * Recursive implode.
     *
     * @param array $array
     * @param string $glue
     * @return string
     */
    public static function deepImplode(array $array, string $glue = ''): string
    {
        $imploded = '';

        foreach ($array as $item) {
            $imploded = is_array($item) ? $imploded . self::deepImplode($item) : $imploded . $glue . $item;
        }

        return $imploded;
    }

    /**
     * Check functions/classes existence.
     * Used to check if a plugin/theme is active before proceed.
     *
     * @param array $items {
     * @type string $name Plugin or Theme name.
     * @type string $type Type of the dep (class/function).
     * @type string $dep Class or function name.
     * }
     * @return array Not found items.
     */
    public static function checkDeps(array $items): array
    {
        $notFound = [];

        foreach ($items as $name => $item) {
            if (('class' === $item['type'] && !class_exists($item['dep']))
                || ('function' === $item['type'] && !function_exists($item['dep']))) {
                $notFound[$name] = $item['name'];
            }
        }

        return $notFound;
    }

    /**
     * Get path to the WP Uploads dir with trailing slash
     *
     * @param string $path Path inside the uploads dir (will be created if not exists).
     * @return string
     */
    public static function getUploadsDir(string $path = ''): string
    {
        return self::getUploads($path);
    }

    /**
     * Get URL of the WP Uploads dir with trailing slash
     *
     * @param string $path Path inside the uploads dir (will be created if not exists).
     * @return string
     */
    public static function getUploadsUrl(string $path = ''): string
    {
        return self::getUploads($path, true);
    }

    /**
     * Get path/url to the WP Uploads dir with trailing slash.
     *
     * @param string $path Path inside the uploads dir (will be created if not exists).
     * @param bool $getUrl Whether to get URL.
     * @return string
     */
    public static function getUploads(string $path = '', bool $getUrl = false): string
    {
        $uploadDir = wp_upload_dir();

        $basePath = $uploadDir['basedir'];

        $path = $path ? '/' . trim($path, '/') . '/' : '/';

        $fullPath = $basePath . $path;

        if (!file_exists($fullPath)) {
            wp_mkdir_p($fullPath);
        }

        return $getUrl ? $uploadDir['baseurl'] . $path : $fullPath;
    }

    /**
     * External API request helper.
     *
     * @param array $args {
     * @type string $url . Required.
     * @type string $method Get/Post. Default 'get'.
     * @type array $headers . Default [].
     * @type array $data Data to send. Default [].
     * @type int $timeout . Default 0.
     * }
     *
     * @return mixed Response body or false on failure
     */
    public static function apiRequest(array $args)
    {
        $args = array_merge(
            [
                'method' => 'get',
                'headers' => [],
                'data' => [],
                'timeout' => 0,
            ],
            $args
        );

        $url = $args['url'];
        $method = strtoupper($args['method']);
        $data = $args['data'];

        $requestArgs = [
            'method' => $method,
            'headers' => $args['headers'],
            'timeout' => $args['timeout'],
        ];

        if (!empty($data)) {
            if ('GET' === $method) {
                $url .= '?' . http_build_query($data);
            } else {
                $requestArgs['body'] = $data;
            }
        }

        self::log('Performing api request...');
        $remoteResponse = wp_remote_request($url, $requestArgs);
        self::log('Response received');

        if (is_wp_error($remoteResponse)) {
            self::log(implode(' | ', $remoteResponse->get_error_messages()));
            return false;
        }

        if (200 !== ($code = wp_remote_retrieve_response_code($remoteResponse))) {
            self::log("Response code: $code");
            return false;
        }

        if (empty($remoteResponse['body'])) {
            self::log('Wrong response format');
            return false;
        }

        return $remoteResponse['body'];
    }

    /**
     * Return Success array.
     *
     * @param string $message Message. Default 'Done'.
     * @param array $data Data to return as JSON. Default [].
     * @param bool $echo Whether to echo Response right away without returning. Default false.
     * @return array
     */
    public static function returnSuccess(string $message = 'Success', array $data = [], bool $echo = false): array
    {
        $message = $message ?: 'Success';

        self::log($message);

        $return = [
            'success' => true,
            'message' => $message,
            'data' => $data,
        ];

        if ($echo) {
            wp_send_json($return);
        }

        return $return;
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
        $message = $message ?: 'Unknown Error';

        self::log($message);

        $return = [
            'success' => false,
            'message' => $message,
        ];

        if ($echo) {
            wp_send_json($return);
        }

        return $return;
    }

    /**
     * \WP_Error handler
     *
     * @param mixed $result Result of a function call
     * @return mixed|bool Function return or false on WP_Error
     */
    public static function pr($result)
    {
        if ($result instanceof \WP_Error) {
            self::log($result->get_error_message());

            return false;
        }

        return $result;
    }

    /**
     * Trim vars and arrays.
     *
     * @param array|string $var
     * @return array|string
     */
    public static function trim($var)
    {
        if (is_string($var)) {
            return trim($var);
        }

        if (is_array($var)) {
            array_walk_recursive(
                $var,
                function (&$value) {
                    $value = trim($value);
                }
            );
        }

        return $var;
    }

    /**
     * Get output of a function.
     * Used to put output in a variable instead of echo.
     *
     * @param callable $func Callable.
     * @param array $args Function args. Default [].
     * @return string Output
     */
    public static function getOutput(callable $func, array $args = []): string
    {
        ob_start();
        call_user_func_array($func, $args);
        return ob_get_clean();
    }

    /**
     * Convert HEX color to RGB.
     *
     * @param string $hex
     * @return string
     */
    public static function colorToRgb(string $hex): string
    {
        $pattern = strlen($hex) === 4 ? '#%1x%1x%1x' : '#%2x%2x%2x';
        return sscanf($hex, $pattern);
    }

    /**
     * Remove not empty directory.
     *
     * @param string $path
     */
    public static function rmDir(string $path)
    {
        if (!is_dir($path)) {
            return;
        }

        if (substr($path, strlen($path) - 1, 1) != '/') {
            $path .= '/';
        }

        $files = glob($path . '*', GLOB_MARK);

        foreach ($files as $file) {
            is_dir($file) ? self::rmDir($file) : unlink($file);
        }

        rmdir($path);
    }

    /**
     * Remove WP emojis.
     */
    public static function removeEmojis()
    {
        remove_action('wp_head', 'print_emoji_detection_script', 7);
        remove_action('admin_print_scripts', 'print_emoji_detection_script');
        remove_action('wp_print_styles', 'print_emoji_styles');
        remove_filter('the_content_feed', 'wp_staticize_emoji');
        remove_action('admin_print_styles', 'print_emoji_styles');
        remove_filter('comment_text_rss', 'wp_staticize_emoji');
        remove_filter('wp_mail', 'wp_staticize_emoji_for_email');
        add_filter(
            'tiny_mce_plugins',
            function ($plugins) {
                return is_array($plugins) ? array_diff($plugins, ['wpemoji']) : [];
            }
        );
    }

    /**
     * Add Image Sizes
     *
     * @param array $sizes
     */
    public static function addImageSizes(array $sizes)
    {
        $filter = [];
        foreach ($sizes as $size) {
            add_image_size($size['name'], $size['width']);
            $filter[$size['name']] = $size['title'];
        }
        add_filter(
            'image_size_names_choose',
            function ($sizes) use ($filter) {
                return array_merge($sizes, $filter);
            }
        );
    }

    /**
     * Add Theme Support
     *
     * @param array $features
     */
    public static function addThemeSupport(array $features)
    {
        foreach ($features as $feature => $args) {
            if (is_numeric($feature) && is_string($args)) {
                add_theme_support($args);
            } else {
                add_theme_support($feature, $args);
            }
        }
    }

    /**
     * Add Admin Page
     *
     * @param array $args
     * @return AdminPage
     */
    public static function addAdminPage(array $args): AdminPage
    {
        $adminPage = new AdminPage($args);

        if (empty($args['tabs'])) {
            return $adminPage;
        }

        foreach ($args['tabs'] as $tabArgs) {
            $tab = new AdminPageTab($tabArgs);

            self::addFields($tab, $tabArgs);

            self::addAssets($tab, $tabArgs);

            $adminPage->addTab($tab);
        }

        return $adminPage;
    }

    /**
     * Add Post Meta Box
     *
     * @param array $args
     * @return Metabox
     */
    public static function addMetaBox(array $args): Metabox
    {
        $metabox = new Metabox($args);

        self::addFields($metabox, $args);

        self::addAssets($metabox, $args);

        return $metabox;
    }

    /**
     * Add Widget
     *
     * @param array $args
     * @return Widget
     */
    public static function addWidget(array $args): Widget
    {
        $widget = new Widget($args);

        self::addFields($widget, $args);

        self::addAssets($widget, $args);

        return $widget;
    }

    /**
     * Add Post Meta Box
     *
     * @param array $args
     * @return Shortcode
     */
    public static function addShortcode(array $args): Shortcode
    {
        $shortcode = new Shortcode($args);

        if (empty($args['css']) && empty($args['js'])) {
            return $shortcode;
        }

        // Set scope front for assets
        foreach (['css', 'js'] as $type) {
            if (!empty($args[$type])) {
                foreach ($args[$type] as &$item) {
                    $item['scope'] = 'front';
                }
            }
        }

        self::addAssets($shortcode, $args);

        return $shortcode;
    }

    /**
     * Add fields to admin page/metabox
     *
     * @param $object
     * @param array $args
     */
    private static function addFields($object, array $args)
    {
        if (!empty($args['fields'])) {
            foreach ($args['fields'] as $field) {
                $object->addField($field);
            }
        }
    }

    /**
     * Add CSS/JS to admin page/metabox
     *
     * @param $object
     * @param array $args
     */
    private static function addAssets($object, array $args)
    {
        $assetsBaseProps = [
            'scope' => 'admin',
        ];

        if (!empty($args['css'])) {
            foreach ($args['css'] as $cssArgs) {
                $object->addAsset(new Css(array_merge($assetsBaseProps, $cssArgs)));
            }
        }

        if (!empty($args['js'])) {
            foreach ($args['js'] as $jsArgs) {
                $object->addAsset(new Js(array_merge($assetsBaseProps, $jsArgs)));
            }
        }
    }

    /**
     * Add Customizer Panel
     *
     * @param array $args
     * @return Panel
     */
    public static function addCustomizerPanel(array $args): Panel
    {
        $panel = new Panel($args);

        foreach ($args['sections'] as $sectionArgs) {
            $section = new Section($sectionArgs);

            $section->setProp('panel', $panel->getProp('id'));

            foreach ($sectionArgs['settings'] as $settingArgs) {
                $setting = new Setting($settingArgs);

                $setting->setProp('section', $section->getProp('id'));

                $section->addSetting($setting);
            }

            $panel->addSection($section);
        }

        return $panel;
    }

    /**
     * Add a log entry
     *
     * @param mixed $message Text or any other type including WP_Error.
     * @param array $values If passed, vsprintf() func is applied. Default [].
     */
    private static function log($message, array $values = [])
    {
        Logger::log($message, $values);
    }
}
