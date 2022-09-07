<?php

namespace AlexDashkin\Adwpfw;

use AlexDashkin\Adwpfw\Exceptions\AppException;
use AlexDashkin\Adwpfw\Fields\Field;
use AlexDashkin\Adwpfw\Modules\{AdminBar, AdminPage, AdminPageTab, Assets\Css, Assets\Js, CronJob, Customizer\Panel, Customizer\Section, Customizer\Setting, DbWidget, Hook, Metabox, Notice, RestApi\AdminAjax, RestApi\Rest, Shortcode, Sidebar, Updater\Plugin, Updater\Theme, Widget};
use AlexDashkin\Adwpfw\Specials\{Db, Logger, Twig};

class App
{
    /** @var array */
    private $config;

    /** @var Logger */
    private $logger;

    /** @var Twig */
    private $twig;

    /**
     * App constructor
     */
    public function __construct(array $config)
    {
        foreach (['prefix', 'env', 'type', 'baseFile'] as $fieldName) {
            if (empty($config[$fieldName])) {
                throw new AppException(sprintf('ADWPFW: field "%s" is required', $fieldName));
            }
        }

        $this->config = $config;
        $prefix = $this->config('prefix');
        $uploadsDir = $this->getUploadsDir($prefix);

        // Init Logger
        $this->logger = new Logger([
            'prefix' => $prefix,
            'maxLogSize' => $this->config('maxLogSize') ?: 1000000,
            'path' => $uploadsDir . 'logs/',
        ]);

        // Init Twig
        if ($twigPaths = $this->config('twigPaths')) {
            $this->twig = new Twig([
                'env' => $this->config('env'),
                'paths' => $twigPaths,
                'cachePath' => $uploadsDir,
            ]);
        }

        // Updater
        if ($package = $this->config('package')) {
            switch ($this->config('type')) {
                case 'plugin':
                    new Plugin([
                        'file' => $this->config('baseFile'),
                        'package' => $package,
                    ], $this);

                    break;
                case 'theme':
                    new Theme([
                        'package' => $package,
                    ], $this);

                    break;
            }
        }
    }

    /**
     * Get Config
     *
     * @param string $key
     * @return mixed
     */
    public function config(string $key = '')
    {
        if (!$key) {
            return $this->config;
        }

        return array_key_exists($key, $this->config) ? $this->config[$key] : null;
    }

    /**
     * Prefix a string
     *
     * @param string $string
     * @param string $separator
     * @param bool $leadingUnderscore
     * @return string
     */
    public function prefixIt(string $string, string $separator = '_', bool $leadingUnderscore = false): string
    {
        return sprintf('%s%s%s%s', $leadingUnderscore ? '_' : '', $this->config('prefix'), $separator, $string);
    }

    /**
     * Send Email
     *
     * @param array $data
     * @throws AppException
     */
    public function sendMail(array $data)
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
    public function getTableName(string $name): string
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
    public function render(string $name, array $args = []): string
    {
        $paths = $this->config('templatePaths') ?: [];
        $paths[] = __DIR__ . '/../templates/';
        $fileName = $name . '.php';
        $args['prefix'] = $this->config('prefix');

        foreach ($paths as $path) {
            if (file_exists($path . $fileName)) {
                ob_start();
                extract($args);
                include $path . $fileName;
                return ob_get_clean();
            }
        }

        return sprintf('Template "%s" not found', $name);
    }

    /**
     * Render Twig Template
     *
     * @param string $name Template file name with/without path
     * @param array $args Args to be passed to the Template. Default [].
     * @return string Rendered Template
     */
    public function renderTwig(string $name, array $args = []): string
    {
        if (!$this->twig) {
            return 'Twig is not initialized';
        }

        try {
            return $this->twig->renderFile($name . '.twig', $args);
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $this->log($message);
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
    public function addHook(string $tag, callable $callback, int $priority = 10): Hook
    {
        return new Hook([
            'tag' => $tag,
            'callback' => $callback,
            'priority' => $priority,
        ], $this);
    }

    /**
     * Search in an array.
     *
     * @param array $array Array to parse.
     * @param array $conditions . Array of key-value pairs to compare with.
     * @param bool $single Whether to return a single item.
     * @return mixed
     */
    public function arraySearch(array $array, array $conditions, bool $single = false)
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
            $found = $this->arraySearch($found, $conditions);
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
    public function arrayFilter(array $array, array $conditions, bool $single = false)
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
    public function arrayUniqueByKey(array $array, string $key): array
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
    public function arrayParse(array $array, array $keys = [], string $index = '', string $sort = ''): array
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
    public function arraySortByKey(array $array, string $key, bool $keepKeys = false): array
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
    public function arrayMerge(array $arr1, array $arr2): array
    {
        foreach ($arr2 as $key => $value) {
            if (!array_key_exists($key, $arr1)) {
                $arr1[$key] = $value;
                continue;
            }

            if (is_array($arr1[$key]) && is_array($value)) {
                $arr1[$key] = $this->arrayMerge($arr1[$key], $value);
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
    public function arrayAddNonExistent(array $where, array $what): array
    {
        foreach ($what as $name => $value) {
            if (!isset($where[$name])) {
                $where[$name] = $value;
            } elseif (is_array($value)) {
                $where[$name] = $this->arrayAddNonExistent($where[$name], $value);
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
    public function deepImplode(array $array, string $glue = ''): string
    {
        $imploded = '';

        foreach ($array as $item) {
            $imploded = is_array($item) ? $imploded . $this->deepImplode($item) : $imploded . $glue . $item;
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
    public function checkDeps(array $items): array
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
    public function getUploadsDir(string $path = ''): string
    {
        return $this->getUploads($path);
    }

    /**
     * Get URL of the WP Uploads dir with trailing slash
     *
     * @param string $path Path inside the uploads dir (will be created if not exists).
     * @return string
     */
    public function getUploadsUrl(string $path = ''): string
    {
        return $this->getUploads($path, true);
    }

    /**
     * Get path/url to the WP Uploads dir with trailing slash.
     *
     * @param string $path Path inside the uploads dir (will be created if not exists).
     * @param bool $getUrl Whether to get URL.
     * @return string
     */
    public function getUploads(string $path = '', bool $getUrl = false): string
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
    public function apiRequest(array $args)
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

        $this->log('Performing api request...');
        $remoteResponse = wp_remote_request($url, $requestArgs);
        $this->log('Response received');

        if (is_wp_error($remoteResponse)) {
            $this->log(implode(' | ', $remoteResponse->get_error_messages()));
            return false;
        }

        if (200 !== ($code = wp_remote_retrieve_response_code($remoteResponse))) {
            $this->log("Response code: $code");
            return false;
        }

        if (empty($remoteResponse['body'])) {
            $this->log('Wrong response format');
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
    public function returnSuccess(string $message = 'Success', array $data = [], bool $echo = false): array
    {
        $message = $message ?: 'Success';

        $this->log($message);

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
    public function returnError(string $message = 'Unknown Error', bool $echo = false): array
    {
        $message = $message ?: 'Unknown Error';

        $this->log($message);

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
    public function pr($result)
    {
        if ($result instanceof \WP_Error) {
            $this->log($result->get_error_message());

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
    public function trim($var)
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
    public function getOutput(callable $func, array $args = []): string
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
    public function colorToRgb(string $hex): string
    {
        $pattern = strlen($hex) === 4 ? '#%1x%1x%1x' : '#%2x%2x%2x';
        return sscanf($hex, $pattern);
    }

    /**
     * Remove not empty directory.
     *
     * @param string $path
     */
    public function rmDir(string $path)
    {
        if (!is_dir($path)) {
            return;
        }

        if (substr($path, strlen($path) - 1, 1) != '/') {
            $path .= '/';
        }

        $files = glob($path . '*', GLOB_MARK);

        foreach ($files as $file) {
            is_dir($file) ? $this->rmDir($file) : unlink($file);
        }

        rmdir($path);
    }

    /**
     * Remove WP emojis.
     */
    public function removeEmojis()
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
    public function addImageSizes(array $sizes)
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
    public function addThemeSupport(array $features)
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
    public function addAdminPage(array $args): AdminPage
    {
        $adminPage = new AdminPage($args, $this);

        if (empty($args['tabs'])) {
            return $adminPage;
        }

        foreach ($args['tabs'] as $tabArgs) {
            $tab = new AdminPageTab($tabArgs, $this);

            $this->addFields($tab, $tabArgs);

            $this->addAssets($tab, $tabArgs);

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
    public function addMetaBox(array $args): Metabox
    {
        $metabox = new Metabox($args, $this);

        $this->addFields($metabox, $args);

        $this->addAssets($metabox, $args);

        return $metabox;
    }

    /**
     * Add Widget
     *
     * @param array $args
     * @return Widget
     */
    public function addWidget(array $args): Widget
    {
        $widget = new Widget($args, $this);

        $this->addFields($widget, $args);

        $this->addAssets($widget, $args);

        return $widget;
    }

    /**
     * Add Post Meta Box
     *
     * @param array $args
     * @return Shortcode
     */
    public function addShortcode(array $args): Shortcode
    {
        $shortcode = new Shortcode($args, $this);

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

        $this->addAssets($shortcode, $args);

        return $shortcode;
    }

    /**
     * Register global CSS
     *
     * @param array $args
     * @return Css
     * @throws AppException
     */
    public function addCss(array $args): Css
    {
        return new Css($args, $this);
    }

    /**
     * Register global JS
     *
     * @param array $args
     * @return Js
     * @throws AppException
     */
    public function addJs(array $args): Js
    {
        return new Js($args, $this);
    }

    /**
     * Add fields to admin page/metabox
     *
     * @param $object
     * @param array $args
     */
    private function addFields($object, array $args)
    {
        if (!empty($args['fields'])) {
            foreach ($args['fields'] as $fieldArgs) {
                $field = Field::getField($fieldArgs, $this);
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
    private function addAssets($object, array $args)
    {
        $assetsBaseProps = [
            'scope' => 'admin',
        ];

        if (!empty($args['css'])) {
            foreach ($args['css'] as $cssArgs) {
                $object->addAsset(new Css(array_merge($assetsBaseProps, $cssArgs), $this));
            }
        }

        if (!empty($args['js'])) {
            foreach ($args['js'] as $jsArgs) {
                $object->addAsset(new Js(array_merge($assetsBaseProps, $jsArgs), $this));
            }
        }
    }

    /**
     * Add Customizer Panel
     *
     * @param array $args
     * @return Panel
     */
    public function addCustomizerPanel(array $args): Panel
    {
        $panel = new Panel($args, $this);

        foreach ($args['sections'] as $sectionArgs) {
            $section = new Section($sectionArgs, $this);

            $section->setProp('panel', $panel->getProp('id'));

            foreach ($sectionArgs['settings'] as $settingArgs) {
                $setting = new Setting($settingArgs, $this);

                $setting->setProp('section', $section->getProp('id'));

                $section->addSetting($setting);
            }

            $panel->addSection($section);
        }

        return $panel;
    }

    /**
     * Get prefixed option
     *
     * @param string $name
     * @return mixed
     */
    public function getOption(string $name)
    {
        return get_option($this->prefixIt($name));
    }

    /**
     * Update prefixed option
     *
     * @param string $name
     * @param $value
     * @return bool
     */
    public function updateOption(string $name, $value): bool
    {
        return update_option($this->prefixIt($name), $value);
    }

    /**
     * Get prefixed transient
     *
     * @param string $name
     * @return mixed
     */
    public function getTransient(string $name)
    {
        return get_transient($this->prefixIt($name));
    }

    /**
     * Set prefixed transient
     *
     * @param string $name
     * @param $value
     * @param int $expiration
     * @return bool
     */
    public function setTransient(string $name, $value, int $expiration = 0): bool
    {
        return set_transient($this->prefixIt($name), $value, $expiration);
    }

    /**
     * Delete prefixed transient
     *
     * @param string $name
     * @return bool
     */
    public function deleteTransient(string $name): bool
    {
        return delete_transient($this->prefixIt($name));
    }

    /**
     * Get prefixed post meta
     *
     * @param int $postId
     * @param string $name
     * @return mixed
     */
    public function getPostMeta(int $postId, string $name)
    {
        return get_post_meta($postId, $this->prefixIt($name, '_', true), true);
    }

    /**
     * Update prefixed post meta
     *
     * @param int $postId
     * @param string $name
     * @param $value
     * @return bool|int
     */
    public function updatePostMeta(int $postId, string $name, $value)
    {
        return update_post_meta($postId, $this->prefixIt($name, '_', true), $value);
    }

    /**
     * Get prefixed term meta
     *
     * @param int $termId
     * @param string $name
     * @return mixed
     */
    public function getTermMeta(int $termId, string $name)
    {
        return get_term_meta($termId, $this->prefixIt($name, '_', true), true);
    }

    /**
     * Update prefixed term meta
     *
     * @param int $termId
     * @param string $name
     * @param $value
     * @return bool|int
     */
    public function updateTermMeta(int $termId, string $name, $value)
    {
        return update_term_meta($termId, $this->prefixIt($name, '_', true), $value);
    }

    /**
     * Get prefixed user meta
     *
     * @param int $userId
     * @param string $name
     * @return mixed
     */
    public function getUserMeta(int $userId, string $name)
    {
        return get_user_meta($userId, $this->prefixIt($name, '_', true), true);
    }

    /**
     * Update prefixed user meta
     *
     * @param int $userId
     * @param string $name
     * @param $value
     * @return bool|int
     */
    public function updateUserMeta(int $userId, string $name, $value)
    {
        return update_user_meta($userId, $this->prefixIt($name, '_', true), $value);
    }

    /**
     * Add REST API Endpoint
     *
     * @param array $args
     * @return Rest
     * @throws AppException
     */
    public function addRestEndpoint(array $args): Rest
    {
        return new Rest($args, $this);
    }

    /**
     * add Admin Ajax action
     *
     * @param array $args
     * @return AdminAjax
     * @throws AppException
     */
    public function addAjaxAction(array $args): AdminAjax
    {
        return new AdminAjax($args, $this);
    }

    /**
     * DB query
     *
     * @param string $table
     * @return Db
     */
    public function db(string $table = ''): Db
    {
        return new Db($table);
    }

    /**
     * Add theme sidebar
     *
     * @param array $args
     * @return Sidebar
     * @throws AppException
     */
    public function addSidebar(array $args): Sidebar
    {
        return new Sidebar($args, $this);
    }

    /**
     * Add Dashboard Widget
     *
     * @param array $args
     * @return DbWidget
     * @throws AppException
     */
    public function addDbWidget(array $args): DbWidget
    {
        return new DbWidget($args, $this);
    }

    /**
     * Add Cron Job
     *
     * @param array $args
     * @return CronJob
     * @throws AppException
     */
    public function addCronJob(array $args): CronJob
    {
        return new CronJob($args, $this);
    }

    /**
     * Add Admin Notice
     *
     * @param array $args
     * @return Notice
     * @throws AppException
     */
    public function addNotice(array $args): Notice
    {
        return new Notice($args, $this);
    }

    /**
     * Add Admin Bar Entry
     *
     * @param array $args
     * @return AdminBar
     * @throws AppException
     */
    public function addAdminBarEntry(array $args): AdminBar
    {
        return new AdminBar($args, $this);
    }

    /**
     * Add a log entry
     *
     * @param mixed $message Text or any other type including WP_Error.
     * @param array $values If passed, vsprintf() func is applied. Default [].
     */
    public function log($message, array $values = [])
    {
        $this->logger->log($message, $values);
    }
}
