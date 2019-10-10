<?php

namespace AlexDashkin\Adwpfw\Modules;

/**
 * Static Helpers
 */
class Helpers
{
    /**
     * @var Logger
     */
    public static $logger;

    /**
     * Search in an array
     *
     * @param array $array
     * @param array $conditions
     * @param bool $single
     * @return mixed
     */
    public static function arraySearch(array $array, array $conditions, $single = false)
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
     * Filter an array
     *
     * @param array $array
     * @param array $conditions
     * @param bool $single
     * @return array
     */
    public static function arrayFilter(array $array, array $conditions, $single = false)
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
     * Remove duplicates by key
     *
     * @param array $array
     * @param string $key
     * @return array
     */
    public static function arrayUniqueByKey(array $array, $key)
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
     * Transform an array
     *
     * @param array $array
     * @param array $keys Keys to keep
     * @param null $index Key to be used as index
     * @param bool $sort
     * @return array
     */
    public static function arrayParse(array $array, array $keys, $index = null, $sort = false)
    {
        $new = [];

        foreach ($array as $item) {
            $row = [];

            if ($keys) {
                if (is_array($keys)) {
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
                    $row = $item[$keys];
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
            uasort($new, function ($a, $b) use ($sort) {
                return $a[$sort] > $b[$sort] ? 1 : -1;
            });
        }

        return $new;
    }

    /**
     * Sort an array by key
     *
     * @param array $array
     * @param $key
     * @param bool $keepKeys Keep key=>value links when sorting
     * @return array
     */
    public static function arraySortByKey(array $array, $key, $keepKeys = false)
    {
        $func = $keepKeys ? 'uasort' : 'usort';
        $func($array, function ($a, $b) use ($key) {
            return $a[$key] > $b[$key] ? 1 : -1;
        });

        return $array;
    }

    /**
     * Arrays deep merge
     *
     * @param array $arr1
     * @param array $arr2
     * @return array
     */
    public static function arrayMerge(array $arr1, array $arr2)
    {
        foreach ($arr2 as $key => $value) {
            if (!array_key_exists($key, $arr1)) {
                $arr1[$key] = $value;
                continue;
            };

            if (is_array($arr1[$key]) && is_array($value)) {
                $arr1[$key] = self::arrayMerge($arr1[$key], $value);
            } else {
                $arr1[$key] = $value;
            }
        }

        return $arr1;
    }

    /**
     * Add an element to an array if not exists
     *
     * @param array $where
     * @param array $what
     * @return array
     */
    public static function arrayAddNonExistent(array $where, array $what)
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
     * Recursive implode
     *
     * @param array $array
     * @param string $glue
     * @return string
     */
    public static function deepImplode(array $array, $glue = '')
    {
        $imploded = '';

        foreach ($array as $item) {
            $imploded = is_array($item) ? $imploded . self::deepImplode($item) : $imploded . $glue . $item;
        }

        return $imploded;
    }

    /**
     * Check plugin/theme existence
     *
     * @param array $items {
     * @type string $name Plugin or Theme name
     * @type string $type Type of the dep (class/function)
     * @type string $dep Class or function name
     * }
     * @return array Not found items
     */
    public static function checkDeps(array $items)
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
     * Get path to the WP Uploads dir
     *
     * @param string $dirName Dir name to be created in Uploads dir if not exists
     * @param string $path Path inside the uploads dir (will be created if not exists)
     * @return string
     */
    public static function getUploadsDir($dirName, $path = '')
    {
        return self::getUploads($dirName, $path);
    }

    /**
     * Get URL to the WP Uploads dir
     *
     * @param string $dirName Dir name to be created in Uploads dir if not exists
     * @param string $path Path inside the uploads dir (will be created if not exists)
     * @return string
     */
    public static function getUploadsUrl($dirName, $path = '')
    {
        return self::getUploads($dirName, $path, true);
    }

    /**
     * Get path/url to the WP Uploads dir
     *
     * @param string $dirName Dir name to be created in Uploads dir if not exists
     * @param string $path Path inside the uploads dir (will be created if not exists)
     * @param bool $getUrl Whether to get URL
     * @return string
     */
    private static function getUploads($dirName, $path = '', $getUrl = false)
    {
        $uploadDir = wp_upload_dir();
        $basePath = $uploadDir['basedir'] . '/' . $dirName . '/';
        $fullPath = $basePath . $path;

        if (!file_exists($fullPath)) {
            $message = wp_mkdir_p($fullPath) ? "Dir $fullPath created successfully" : 'Error while creating dir ' . $fullPath;
            self::$logger->log($message);
        }

        return $getUrl ? $uploadDir['baseurl'] . '/' . $dirName . '/' . $path : $fullPath;
    }

    /**
     * External API request helper
     *
     * @param array $args {
     * @type string $url
     * @type string $method Get/Post
     * @type array $headers
     * @type array $data Data to send
     * @type int $timeout
     * }
     *
     * @return mixed Response body or false on failure
     */
    public static function apiRequest(array $args)
    {
        $args = array_merge([
            'method' => 'get',
            'headers' => [],
            'data' => [],
            'timeout' => 0,
        ], $args);

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

        self::$logger->log('Performing api request...');
        $remoteResponse = wp_remote_request($url, $requestArgs);
        self::$logger->log('Response received');

        if (is_wp_error($remoteResponse)) {
            self::$logger->log(implode(' | ', $remoteResponse->get_error_messages()));
            return false;
        }

        if (200 !== ($code = wp_remote_retrieve_response_code($remoteResponse))) {
            self::$logger->log("Response code: $code");
            return false;

        }

        if (empty($remoteResponse['body'])) {
            self::$logger->log('Wrong response format');
            return false;
        }

        return $remoteResponse['body'];
    }

    /**
     * Return success response
     *
     * @param string $message
     * @param array $data
     * @param bool $echo
     * @return array
     */
    public static function returnSuccess($message = 'Done', array $data = [], $echo = false)
    {
        $message = $message ?: 'Done';

        self::$logger->log($message);

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
     * Return error response
     *
     * @param string $message
     * @param bool $echo
     * @return array
     */
    public static function returnError($message = 'Unknown Error', $echo = false)
    {
        $message = $message ?: 'Unknown Error';

        self::$logger->log($message, 1);

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
     * Handle false and \WP_Error returns
     *
     * @param mixed $result
     * @param string $errorMessage
     * @return bool
     */
    public static function pr($result, $errorMessage = '')
    {
        if (!$result || is_wp_error($result)) {
            $message = $errorMessage ? 'Error: ' . $errorMessage : 'Error!';
            self::$logger->log($message, 1);

            if ($result) {
                self::$logger->log($result);
            }

            return false;
        }

        return $result;
    }

    /**
     * Trim vars and arrays
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
            array_walk_recursive($var, function (&$value) {
                $value = trim($value);
            });
        }

        return $var;
    }

    /**
     * Get output of a function
     *
     * @param string|array $func Callable
     * @param array $args Function args
     * @return string Output
     */
    public static function getOutput($func, $args = [])
    {
        ob_start();
        call_user_func_array($func, $args);
        return ob_get_clean();
    }

    /**
     * Convert HEX color to RGB
     *
     * @param string $hex
     * @return string
     */
    public static function colorToRgb($hex)
    {
        $pattern = strlen($hex) === 4 ? '#%1x%1x%1x' : '#%2x%2x%2x';
        return sscanf($hex, $pattern);
    }

    /**
     * Disable WP emojis
     */
    public static function disableEmojis()
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
}