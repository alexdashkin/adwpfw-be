<?php

namespace AlexDashkin\Adwpfw\Modules;

/**
 * Helpers
 */
class Helpers extends Module
{
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
    public function returnSuccess(string $message = 'Done', array $data = [], bool $echo = false): array
    {
        $message = $message ?: 'Done';

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
    public function returnError(string $message = 'Unknown Error', bool $echo = false)
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
        remove_action('admin_print_styles', 'print_emoji_styles');
        remove_action('wp_head', 'print_emoji_detection_script', 7);
        remove_action('admin_print_scripts', 'print_emoji_detection_script');
        remove_action('wp_print_styles', 'print_emoji_styles');
        remove_filter('wp_mail', 'wp_staticize_emoji_for_email');
        remove_filter('the_content_feed', 'wp_staticize_emoji');
        remove_filter('comment_text_rss', 'wp_staticize_emoji');
        add_filter('emoji_svg_url', '__return_false');

        if (function_exists('disable_emojicons_tinymce')) {
            add_filter('tiny_mce_plugins', 'disable_emojicons_tinymce');
        }
    }

    /**
     * Get prefixed post meta
     *
     * @param string $name
     * @param int $postId
     * @return mixed
     */
    public function getPostMeta(string $name, int $postId = 0)
    {
        return $this->getMeta($name, 'post', $postId);
    }

    /**
     * Get prefixed user meta
     *
     * @param string $name
     * @param int $userId
     * @return mixed
     */
    public function getUserMeta(string $name, int $userId = 0)
    {
        return $this->getMeta($name, 'user', $userId);
    }

    /**
     * Get prefixed term meta
     *
     * @param string $name
     * @param int $termId
     * @return mixed
     */
    public function getTermMeta(string $name, int $termId = 0)
    {
        return $this->getMeta($name, 'term', $termId);
    }

    /**
     * Update prefixed post meta
     *
     * @param string $name
     * @param mixed $value
     * @param int $postId
     * @return int|bool
     */
    public function updatePostMeta(string $name, $value, int $postId = 0)
    {
        return $this->setMeta($name, 'post', $value, $postId);
    }

    /**
     * Update prefixed user meta
     *
     * @param string $name
     * @param mixed $value
     * @param int $userId
     * @return int|bool
     */
    public function updateUserMeta(string $name, $value, int $userId = 0)
    {
        return $this->setMeta($name, 'user', $value, $userId);
    }

    /**
     * Update prefixed term meta
     *
     * @param string $name
     * @param mixed $value
     * @param int $termId
     * @return int|bool
     */
    public function updateTermMeta(string $name, $value, int $termId = 0)
    {
        return $this->setMeta($name, 'term', $value, $termId);
    }

    /**
     * Get prefixed option
     *
     * @param string $name
     * @return mixed
     */
    public function getOption(string $name)
    {
        return get_option($this->gp('prefix') . '_' . $name);
    }

    /**
     * Update prefixed option
     *
     * @param string $name
     * @param mixed $value
     * @return bool
     */
    public function updateOption(string $name, $value): bool
    {
        return update_option($this->gp('prefix') . '_' . $name, $value);
    }

    /**
     * Get Settings value
     *
     * @param string $key
     * @param string $optionName
     * @return mixed
     */
    public function setting(string $key, string $optionName = 'settings')
    {
        $settings = $this->getOption($optionName);

        return array_key_exists($key, $settings) ? $settings[$key] : null;
    }

    /**
     * Get prefixed cache entry
     *
     * @param string $name
     * @return mixed
     */
    public function cacheGet(string $name)
    {
        return wp_cache_get($name, $this->gp('prefix'));
    }

    /**
     * Update prefixed cache entry
     *
     * @param string $name
     * @param mixed $value
     * @return bool
     */
    public function cacheSet(string $name, $value)
    {
        return wp_cache_set($name, $value, $this->gp('prefix'));
    }

    /**
     * Get prefixed post/user meta
     *
     * @param string $name
     * @param string $type
     * @param int $objectId
     * @return mixed
     */
    private function getMeta(string $name, string $type, int $objectId = 0)
    {
        $objectId = $objectId ?: get_queried_object_id();

        $func = 'get_' . $type . '_meta';

        return $func($objectId, '_' . $this->gp('prefix') . '_' . $name, true);
    }

    /**
     * Update prefixed post/user meta
     *
     * @param string $name
     * @param string $type
     * @param int $objectId
     * @return int|bool
     */
    private function setMeta(string $name, string $type, $value, int $objectId = 0)
    {
        $objectId = $objectId ?: get_queried_object_id();

        $func = 'update_' . $type . '_meta';

        return $func($objectId, '_' . $this->gp('prefix') . '_' . $name, $value);
    }

    /**
     * Add a log entry
     *
     * @param mixed $message Text or any other type including WP_Error.
     * @param array $values If passed, vsprintf() func is applied. Default [].
     * @param int $level 1 = Error, 2 = Warning, 4 = Notice. Default 4.
     */
    protected function log($message, array $values = [], int $level = 4)
    {
        /**
         * @var Logger $logger
         */
        $logger = $this->m('logger');

        $logger->log($message, $values, $level);
    }

    protected function getInitialPropDefs(): array
    {
        return [
            'prefix' => [
                'required' => true,
            ],
        ];
    }
}
