<?php

namespace AlexDashkin\Adwpfw\Modules;

/**
 * Static Helpers
 */
class Helpers
{
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