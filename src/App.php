<?php

namespace AlexDashkin\Adwpfw;

use AlexDashkin\Adwpfw\Modules\Helpers;
use AlexDashkin\Adwpfw\Modules\Logger;
use AlexDashkin\Adwpfw\Modules\Module;

/**
 * Main App Class
 */
class App
{
    /**
     * @var array Config
     */
    public $config = [];

    /**
     * @var Module[] Modules
     */
    private $modules = [];

    /**
     * Constructor
     *
     * @param array $config Config
     */
    public function __construct(array $config)
    {
        $this->config = $config;

        Helpers::$logger = $this->m('Logger');
    }

    /**
     * Get Module
     *
     * If not exists, try to create
     *
     * @param string $moduleName
     * @return Module|Logger
     */
    public function m($moduleName)
    {
        if (array_key_exists($moduleName, $this->modules)) {
            return $this->modules[$moduleName];
        }

        $class = '\\' . __NAMESPACE__ . '\\Modules\\' . $moduleName;

/*        if (!class_exists($class)) {
            throw new AdwpfwException("Module $moduleName not found");
        }*/

        $this->modules[$moduleName] = new $class($this);

        return $this->modules[$moduleName];
    }

    /**
     * Perform a DB Query.
     *
     * @param string $query SQL Query.
     * @param array $values If passed, $wpdb->prepare() will be called first.
     * @return mixed
     */
    public function dbQuery($query, array $values = [])
    {
        return $this->m('Db')->query($query, $values);
    }

    /**
     * Insert Data into a table.
     *
     * @param string $table Table Name.
     * @param array $data Data to insert.
     * @param bool $own Is own table?
     * @return int|bool Insert ID or false if failed.
     */
    public function dbInsert($table, array $data, $own = true)
    {
        return $this->m('Db')->insert($table, $data, $own);
    }

    /**
     * Update Data in a table.
     *
     * @param string $table Table Name.
     * @param array $data Data to insert.
     * @param array $where Conditions.
     * @param bool $own Is own table?
     * @return int|bool Insert ID or false if failed.
     */
    public function dbUpdate($table, array $data, array $where, $own = true)
    {
        return $this->m('Db')->update($table, $data, $where, $own);
    }

    /**
     * Insert or Update Data if exists.
     *
     * @param string $table Table Name.
     * @param array $data Data to insert.
     * @param array $where Conditions.
     * @param bool $own Is own table?
     * @return int|bool Insert ID or false if failed.
     */
    public function dbInsertOrUpdate($table, array $data, array $where, $own = true)
    {
        return $this->m('Db')->insertOrUpdate($table, $data, $where, $own);
    }

    /**
     * Delete rows from a table.
     *
     * @param string $table Table Name.
     * @param array $where Conditions.
     * @param bool $own Is own table?
     * @return bool Succeed?
     */
    public function dbDelete($table, array $where, $own = true)
    {
        return $this->m('Db')->delete($table, $where, $own);
    }

    /**
     * Get Var.
     *
     * @param string $table Table Name.
     * @param string $var Field name.
     * @param array $where Conditions.
     * @param bool $own Is own table?
     * @return mixed
     */
    public function dbGetVar($table, $var, array $where, $own = true)
    {
        return $this->m('Db')->getVar($table, $var, $where, $own);
    }

    /**
     * Get Results.
     *
     * @param string $table Table Name.
     * @param array $fields List of Fields.
     * @param array $where Conditions.
     * @param bool $single Get single row?
     * @param bool $own Is own table?
     * @return mixed
     */
    public function dbGetResults($table, array $fields = [], array $where = [], $single = false, $own = true)
    {
        return $this->m('Db')->getResults($table, $fields, $where, $single, $own);
    }

    /**
     * Get Results with an arbitrary Query.
     *
     * @param string $query SQL query.
     * @param array $values If passed, $wpdb->prepare() will be executed first.
     * @return mixed
     */
    public function dbGetResultsQuery($query, array $values = [])
    {
        return $this->m('Db')->getResultsQuery($query, $values);
    }

    /**
     * Get Results Count.
     *
     * @param string $table Table Name.
     * @param array $where Conditions.
     * @param bool $own Is own table?
     * @return int
     */
    public function dbGetCount($table, array $where = [], $own = true)
    {
        return $this->m('Db')->getCount($table, $where, $own);
    }

    /**
     * Get Last Insert ID.
     *
     * @return int
     */
    public function dbInsertId()
    {
        return $this->m('Db')->insertId();
    }

    /**
     * Insert Multiple Rows with one query.
     *
     * @param string $table Table Name.
     * @param array $data Data to insert.
     * @param bool $own Is own table?
     * @return bool
     */
    public function dbInsertRows($table, array $data, $own = true)
    {
        return $this->m('Db')->insertRows($table, $data, $own);
    }

    /**
     * Truncate a table.
     *
     * @param string $table Table Name.
     * @param bool $own Is own table?
     * @return bool
     */
    public function dbTruncateTable($table, $own = true)
    {
        return $this->m('Db')->truncateTable($table, $own);
    }

    /**
     * Check own tables existence.
     *
     * @param array $tables List of own tables.
     * @return bool
     */
    public function dbCheckTables(array $tables)
    {
        return $this->m('Db')->checkTables($tables);
    }

    /**
     * Get table name with all prefixes.
     *
     * @param string $name Table Name.
     * @param bool $own Is own table?
     * @return string
     */
    public function dbGetTable($name, $own = true)
    {
        return $this->m('Db')->getTable($name, $own);
    }

    /**
     * Search in an array.
     *
     * @param array $array Array to parse.
     * @param array $conditions . Array of key-value pairs to compare with.
     * @param bool $single Whether to return a single item.
     * @return mixed
     */
    public function arraySearch(array $array, array $conditions, $single = false)
    {
        return Helpers::arraySearch($array, $conditions, $single);
    }

    /**
     * Filter an array.
     *
     * @param array $array Array to parse.
     * @param array $conditions Array of key-value pairs to compare with.
     * @param bool $single Whether to return a single item.
     * @return mixed
     */
    public function arrayFilter(array $array, array $conditions, $single = false)
    {
        return Helpers::arrayFilter($array, $conditions, $single);
    }

    /**
     * Remove duplicates by key.
     *
     * @param array $array Array to parse.
     * @param string $key Key to search duplicates by.
     * @return array Filtered array.
     */
    public function arrayUniqueByKey(array $array, $key)
    {
        return Helpers::arrayUniqueByKey($array, $key);
    }

    /**
     * Transform an array.
     *
     * @param array $array Array to parse.
     * @param array $keys Keys to keep.
     * @param null $index Key to be used as index.
     * @param bool $sort Key to sort by.
     * @return array
     */
    public function arrayParse(array $array, array $keys, $index = null, $sort = false)
    {
        return Helpers::arrayParse($array, $keys, $index, $sort);
    }

    /**
     * Sort an array by key.
     *
     * @param array $array Array to parse.
     * @param string $key Key to sort by.
     * @param bool $keepKeys Keep key=>value assigment when sorting
     * @return array Resulting array.
     */
    public function arraySortByKey(array $array, $key, $keepKeys = false)
    {
        return Helpers::arraySortByKey($array, $key, $keepKeys);
    }

    /**
     * Arrays deep merge.
     *
     * @param array $arr1
     * @param array $arr2
     * @return array
     */
    public function arrayMerge(array $arr1, array $arr2)
    {
        return Helpers::arrayMerge($arr1, $arr2);
    }

    /**
     * Add an element to an array if not exists.
     *
     * @param array $where Array to add to.
     * @param array $what Array to be added.
     * @return array
     */
    public function arrayAddNonExistent(array $where, array $what)
    {
        return Helpers::arrayAddNonExistent($where, $what);
    }

    /**
     * Recursive implode.
     *
     * @param array $array
     * @param string $glue
     * @return string
     */
    public function deepImplode(array $array, $glue = '')
    {
        return Helpers::deepImplode($array, $glue);
    }

    /**
     * Wrapper for false and \WP_Error returns.
     *
     * @param mixed $result Result of a function call.
     * @param string $errorMessage Message to log on error.
     * @return bool Whether the call succeeded.
     */
    public function pr($result, $errorMessage = '')
    {
        return Helpers::pr($result, $errorMessage);
    }

    /**
     * Check functions/classes existence.
     * Used to check if a plugin/theme is active before proceed.
     *
     * @param array $deps {
     * @type string $name Plugin or Theme name.
     * @type string $type Type of the dep (class/function).
     * @type string $dep Class or function name.
     * }
     * @return array Not found items.
     */
    public function checkDeps(array $deps)
    {
        return Helpers::checkDeps($deps);
    }

    /**
     * Trim vars and arrays.
     *
     * @param array|string $var
     * @return array|string
     */
    public function trim($var)
    {
        return Helpers::trim($var);
    }

    /**
     * Get output of a function.
     * Used to put output in a variable instead of echo.
     *
     * @param string|array $func Callable.
     * @param array $args Function args.
     * @return string Output
     */
    public function getOutput($func, $args = [])
    {
        return Helpers::getOutput($func, $args);
    }

    /**
     * Convert HEX color to RGB.
     *
     * @param string $hex
     * @return string
     */
    public function colorToRgb($hex)
    {
        return Helpers::colorToRgb($hex);
    }

    /**
     * Remove not empty directory.
     *
     * @param $path
     */
    public static function rmDir($path)
    {
        Helpers::rmDir($path);
    }

    /**
     * Get path to the WP Uploads dir with trailing slash.
     *
     * @param string $path Path inside the uploads dir (will be created if not exists).
     * @return string
     */
    public function getUploadsDir($path = '')
    {
        return Helpers::getUploadsDir($path);
    }

    /**
     * Get URL of the WP Uploads dir with trailing slash.
     *
     * @param string $path Path inside the uploads dir (will be created if not exists).
     * @return string
     */
    public function getUploadsUrl($path = '')
    {
        return Helpers::getUploadsUrl($path);
    }

    /**
     * External API request helper.
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
    public function apiRequest(array $args)
    {
        return Helpers::apiRequest($args);
    }

    /**
     * Return Success array.
     *
     * @param string $message Message.
     * @param array $data Data to return as JSON.
     * @param bool $echo Whether to echo Response right away without returning.
     * @return array
     */
    public function success($message = 'Done', array $data = [], $echo = false)
    {
        return Helpers::returnSuccess($message, $data, $echo);
    }

    /**
     * Return Error array.
     *
     * @param string $message Error message.
     * @param bool $echo Whether to echo Response right away without returning.
     * @return array
     */
    public function error($message = 'Unknown Error', $echo = false)
    {
        return Helpers::returnError($message, $echo);
    }

    /**
     * Add a log entry.
     *
     * @param mixed $message Text or any other type including WP_Error.
     * @param array $values If passed, vsprintf() func is applied.
     * @param int $type 1 = Error, 2 = Warning, 4 = Notice.
     */
    public function log($message, $values = [], $type = 4)
    {
        $this->m('Logger')->log($message, $values, $type);
    }

    /**
     * Add file paths to search Twig templates in.
     *
     * @param array $paths
     */
    public function addTwigPaths(array $paths)
    {
        $this->m('Twig')->addPaths($paths);
    }

    /**
     * Add string templates as key-value pairs.
     *
     * @param array $templates
     */
    public function addTwigTemplates(array $templates)
    {
        $this->m('Twig')->addTemplates($templates);
    }

    /**
     * Render File Template.
     *
     * @param string $name Template file name without .twig.
     * @param array $args Args to be passed to the Template.
     * @return string Rendered Template.
     */
    public function twigFile($name, array $args = [])
    {
        return $this->m('Twig')->renderFile($name, $args);
    }

    /**
     * Render Array Template.
     *
     * @param string $name Template name.
     * @param array $args Args to be passed to the Template.
     * @return string Rendered Template.
     */
    public function twigArray($name, array $args = [])
    {
        return $this->m('Twig')->renderArray($name, $args);
    }

    /**
     * Add an item to the Top Admin Bar.
     *
     * @param array $data {
     * @type string $id ID w/o prefix. Defaults to sanitized $title.
     * @type string $title Bar Title. Required.
     * @type string $parent Parent node ID. Default null.
     * @type string $capability Minimum capability. Default 'manage_options'.
     * @type string $href URL of the link.
     * @type bool $group Whether or not the node is a group. Default false.
     * @type array $meta Meta data including the following keys: 'html', 'class', 'rel', 'lang', 'dir', 'onclick', 'target', 'title', 'tabindex'. Default empty.
     * }
     */
    public function addAdminBar(array $data)
    {
        $this->m('AdminBars')->add($data);
    }

    /**
     * Add multiple items to the Top Admin Bar
     *
     * @param array $data {
     * @type string $id ID w/o prefix. Defaults to sanitized $title.
     * @type string $title Bar Title. Required.
     * @type string $parent Parent node ID. Default null.
     * @type string $capability Minimum capability. Default 'manage_options'.
     * @type string $href URL of the link.
     * @type bool $group Whether or not the node is a group. Default false.
     * @type array $meta Meta data including the following keys: 'html', 'class', 'rel', 'lang', 'dir', 'onclick', 'target', 'title', 'tabindex'. Default empty.
     * }
     */
    public function addAdminBars(array $data)
    {
        $this->m('AdminBars')->addMany($data);
    }

    /**
     * Add Admin Page to the left WP Admin Menu.
     *
     * @param array $data {
     * @type string $name Text for the left Menu. Required.
     * @type string $title Text for the 'title' tag. Defaults to $name.
     * @type string $header Page header without markup. Defaults to $name.
     * @type string $parent Parent Menu slug. If specified, a sub menu will be added.
     * @type int $position Position in the Menu. Default 0.
     * @type string $icon The dash icon name for the bar. Default 'dashicons-update'
     * @type string $capability Minimum capability. Default 'manage_options'.
     * @type array $tabs Tabs: {
     * @type string $title Tab Title.
     * @type array $fields Tab fields.
     * }
     */
    public function addAdminPage(array $data)
    {
        $this->m('AdminPages')->add($data);
    }

    /**
     * Add multiple Admin pages.
     *
     * @param array $data {
     * @type string $name Text for the left Menu. Required.
     * @type string $title Text for the <title> tag. Defaults to $name.
     * @type string $header Page header without markup. Defaults to $name.
     * @type string $parent Parent Menu slug. If specified, a sub menu will be added.
     * @type int $position Position in the Menu. Default 0.
     * @type string $icon The dash icon name for the bar. Default 'dashicons-update'
     * @type string $capability Minimum capability. Default 'manage_options'.
     * @type array $tabs Tabs: {
     * @type string $title Tab Title.
     * @type array $fields Tab fields.
     * }
     */
    public function addAdminPages(array $data)
    {
        $this->m('AdminPages')->addMany($data);
    }

    /**
     * Add an AJAX action (admin-ajax.php)
     *
     * @param array $data {
     * @type string $id ID for internal use. Defaults to sanitized $name.
     * @type string $name Action name without prefix (will be added automatically). Required.
     * @type array $fields Accepted params [type, required]
     * @type callable $callback Handler. Gets an array with $_REQUEST params. Required.
     * }
     */
    public function addAjaxAction(array $data)
    {
        $this->m('Ajax')->add($data);
    }

    /**
     * Add multiple AJAX actions (admin-ajax.php)
     *
     * @param array $data {
     * @type string $id ID for internal use. Defaults to sanitized $name.
     * @type string $name Action name without prefix (will be added automatically). Required.
     * @type array $fields Accepted params [type, required]
     * @type callable $callback Handler. Gets an array with $_REQUEST params. Required.
     * }
     */
    public function addAjaxActions(array $data)
    {
        $this->m('Ajax')->addMany($data);
    }

    /**
     * Add an REST API Endpoint (/wp-json/)
     *
     * @param array $data {
     * @type string $id ID for internal use. Defaults to sanitized 'route'.
     * @type string $namespace Namespace with trailing slash (i.e. prefix/v1/).
     * @type string $route Route without slashes (i.e. users).
     * @type string $method get/post.
     * @type bool $admin Whether available for admins only.
     * @type array $fields Accepted params [type, required].
     * @type callable $callback Handler. Gets an array with $_REQUEST params. Required.
     * }
     */
    public function addApiEndpoint(array $data)
    {
        $this->m('Rest')->add($data);
    }

    /**
     * Add multiple REST API Endpoints (/wp-json/)
     *
     * @param array $data {
     * @type string $id ID for internal use. Defaults to sanitized 'route'.
     * @type string $namespace Namespace with trailing slash (i.e. prefix/v1/).
     * @type string $route Route without slashes (i.e. users).
     * @type string $method get/post.
     * @type bool $admin Whether available for admins only.
     * @type array $fields Accepted params [type, required].
     * @type callable $callback Handler. Gets an array with $_REQUEST params. Required.
     * }
     */
    public function addApiEndpoints(array $data)
    {
        $this->m('Rest')->addMany($data);
    }

    /**
     * Add Asset.
     *
     * @param array $data {
     * @type string $id Asset ID. Defaults to sanitized $type. Must be unique.
     * @type string $type css/js. Required.
     * @type string $af admin/front. Required.
     * @type string $file Path relative to the Plugin root. Default null.
     * @type string $url Asset URL. Default null.
     * @type string $ver Version added as a query string param. Defaults to filemtime() if $file is specified.
     * @type array $deps List of Dependencies (slugs).
     * @type callable $callback Must return true to enqueue the Asset.
     * @type array $localize Key-value pairs to be passed to the script as an object with name equals to $prefix.
     * }
     */
    public function addAsset(array $data)
    {
        $this->m('Assets')->add($data);
    }

    /**
     * Add Assets.
     *
     * @param array $data {
     * @type string $id Asset ID. Defaults to sanitized $type. Must be unique.
     * @type string $type css/js. Required.
     * @type string $af admin/front. Required.
     * @type string $file Path relative to the Plugin root.
     * @type string $url Asset URL. Defaults to $file URL if $file is specified.
     * @type string $ver Version added as a query string param. Defaults to filemtime() if $file is specified.
     * @type array $deps List of Dependencies (slugs).
     * @type callable $callback Must return true to enqueue the Asset.
     * @type array $localize Key-value pairs to be passed to the script as an object with name equals to $prefix.
     * }
     */
    public function addAssets(array $data)
    {
        $this->m('Assets')->addMany($data);
    }

    /**
     * Enqueue registered assets.
     *
     * @param array $ids Registered assets IDs.
     */
    public function enqueueRegisteredAssets(array $ids)
    {
        $this->m('Assets')->addRegistered($ids);
    }

    /**
     * Remove registered assets.
     *
     * @param array $ids Registered assets IDs.
     */
    public function removeAssets(array $ids)
    {
        $this->m('Assets')->remove($ids);
    }

    /**
     * Add a Cron Job.
     *
     * @param array $data {
     * @type string $id Job ID. Defaults to sanitized $name.
     * @type string $name Job Name. Required.
     * @type callable $callback Handler. Gets $args. Required.
     * @type int $interval Interval in seconds. Default 0.
     * @type bool $parallel Whether to parallel execution. Default false.
     * @type array $args Args to be passed to the handler. Default empty.
     * }
     */
    public function addCronJob(array $data)
    {
        $this->m('Cron')->add($data);
    }

    /**
     * Add multiple Cron Jobs.
     *
     * @param array $data {
     * @type string $id Job ID. Defaults to sanitized $name.
     * @type string $name Job Name. Required.
     * @type callable $callback Handler. Gets $args. Required.
     * @type int $interval Interval in seconds. Default 0.
     * @type bool $parallel Whether to parallel execution. Default false.
     * @type array $args Args to be passed to the handler. Default empty.
     * }
     */
    public function addCronJobs(array $data)
    {
        $this->m('Cron')->addMany($data);
    }

    /**
     * Remove main cron job from WP (used on plugin deactivation)
     */
    public function deactivateCron()
    {
        $this->m('Cron')->deactivate();
    }

    /**
     * Add Customizer Panel
     *
     * @param array $data {
     * @type string $id Defaults to sanitized $title.
     * @type string $title Metabox title. Required.
     * }
     */
    public function addCustomizerPanel(array $data)
    {
        $this->m('Customizer')->add($data);
    }

    /**
     * Add a Metabox.
     *
     * @param array $data {
     * @type string $id Defaults to sanitized $title.
     * @type string $title Metabox title. Required.
     * @type array $screen For which Post Types to show.
     * @type string $context normal/side/advanced. Default 'normal'.
     * @type string $priority high/low/default. Default 'default'.
     * @type array $fields Metabox fields.
     * }
     */
    public function addMetabox(array $data)
    {
        $this->m('Metaboxes')->add($data);
    }

    /**
     * Add multiple Metaboxes
     *
     * @param array $data {
     * @type string $id Defaults to sanitized $title.
     * @type string $title Metabox title. Required.
     * @type array $screen For which Post Types to show.
     * @type string $context normal/side/advanced. Default 'normal'.
     * @type string $priority high/low/default. Default 'default'.
     * @type array $fields Metabox fields.
     * }
     */
    public function addMetaboxes(array $data)
    {
        $this->m('Metaboxes')->addMany($data);
    }

    /**
     * Get a Metabox Value
     *
     * @param string $id Metabox ID without prefix
     * @param int|null $post Post ID (defaults to the current post)
     * @return mixed
     */
    public function metaboxGet($id, $post = null)
    {
        return $this->m('Metaboxes')->get($id, $post);
    }

    /**
     * Set a Metabox Value
     *
     * @param string $id Metabox ID without prefix
     * @param mixed $value Value to set
     * @param int|null $post Post ID (defaults to the current post)
     * @return bool
     */
    public function metaboxSet($id, $value, $post = null)
    {
        return $this->m('Metaboxes')->set($id, $value, $post);
    }

    /**
     * Add Admin Notice
     *
     * @param array $data {
     * @type string $id Defaults to sanitized $tpl.
     * @type string $message Message to display (tpl will be ignored).
     * @type string $tpl Name of the notice Twig template.
     * @type string $type Notice type (success, error). Default 'success'.
     * @type bool $dismissible Whether can be dismissed. Default true.
     * @type int $days When to show again after dismissed. Default 0.
     * @type array $classes Container CSS classes. Default empty.
     * @type array $args Additional Twig args. Default empty.
     * @type callable $callback Must return true for the Notice to show.
     * }
     */
    public function addNotice(array $data)
    {
        $this->m('Notices')->add($data);
    }

    /**
     * Add multiple Admin Notices
     *
     * @param array $data {
     * @type string $id Defaults to sanitized $tpl.
     * @type string $message Message to display (tpl will be ignored).
     * @type string $tpl Name of the notice Twig template.
     * @type string $type Notice type (success, error). Default 'success'.
     * @type bool $dismissible Whether can be dismissed. Default true.
     * @type int $days When to show again after dismissed. Default 0.
     * @type array $classes Container CSS classes. Default empty.
     * @type array $args Additional Twig args. Default empty.
     * @type callable $callback Must return true for the Notice to show.
     * }
     */
    public function addNotices(array $data)
    {
        $this->m('Notices')->addMany($data);
    }

    /**
     * Show a notice
     *
     * @param string $id Notice ID
     */
    public function showNotice($id)
    {
        $this->m('Notices')->show($id);
    }

    /**
     * Stop showing a notice
     *
     * @param string $id Notice ID
     */
    public function stopNotice($id)
    {
        $this->m('Notices')->stop($id);
    }

    /**
     * Dismiss a notice
     *
     * @param string $id Notice ID
     */
    public function dismissNotice($id)
    {
        $this->m('Notices')->dismiss($id);
    }

    /**
     * Add a post state.
     *
     * @param array $data {
     * @param int $post_id Post ID.
     * @param string $state State text.
     * }
     */
    public function addPostState(array $data)
    {
        $this->m('PostStates')->add($data);
    }

    /**
     * Add post states.
     *
     * @param array $data {
     * @param int $post_id Post ID.
     * @param string $state State text.
     * }
     */
    public function addPostStates(array $data)
    {
        $this->m('PostStates')->add($data);
    }

    /**
     * Add a Custom Post Type
     *
     * @param array $data {
     * @param string $id ID. Defaults to sanitized $label.
     * @param string $label Name shown in the menu. Usually plural.
     * @param string $description A short descriptive summary of what the post type is.
     * @param array $labels $singular and $plural are required, rest are auto-populated.
     * @param bool $public Whether to show in Admin. Default true.
     * }
     *
     * @see register_post_type()
     */
    public function addPostType(array $data)
    {
        $this->m('PostTypes')->add($data);
    }

    /**
     * Add multiple Post Types
     *
     * @param array $data {
     * @param string $id ID. Defaults to sanitized $label.
     * @param string $label Name shown in the menu. Usually plural.
     * @param string $description A short descriptive summary of what the post type is.
     * @param array $labels $singular and $plural are required, rest are auto-populated.
     * @param bool $public Whether to show in Admin. Default true.
     * }
     */
    public function addPostTypes(array $data)
    {
        $this->m('PostTypes')->addMany($data);
    }

    /**
     * Set Profile Fields Group Heading.
     *
     * @param string $heading Heading Text
     */
    public function setProfileHeading($heading)
    {
        $this->m('Profile')->setHeading($heading);
    }

    /**
     * Add a User Profile Field
     *
     * @param array $data {
     * @type string $id Field Name used as a key in $prefix[] array on the Form. Required.
     * @type string $label Field Label. Required.
     * @type string $type Field Type. Default 'text'.
     * @type string $desc Field Description to be shown below the Field.
     * }
     */
    public function addProfileField(array $data)
    {
        $this->m('Profile')->add($data);
    }

    /**
     * Add multiple Profile Fields
     *
     * @param array $data {
     * @type string $id Field Name used as a key in $prefix[] array on the Form. Required.
     * @type string $label Field Label. Required.
     * @type string $type Field Type. Default 'text'.
     * @type string $desc Field Description to be shown below the Field.
     * }
     */
    public function addProfileFields(array $data)
    {
        $this->m('Profile')->addMany($data);
    }

    /**
     * Get Profile Field value
     *
     * @param string $id Field ID
     * @param int|null $userId User ID (defaults to the current user)
     * @return mixed
     */
    public function profileGet($id, $userId = null)
    {
        return $this->m('Profile')->get($id, $userId);
    }

    /**
     * Set Profile Field value
     *
     * @param string $id Field ID
     * @param mixed $value Value to set
     * @param int|null $userId User ID (defaults to the current user)
     * @return mixed
     */
    public function profileSet($id, $value, $userId = null)
    {
        return $this->m('Profile')->set($id, $value, $userId);
    }

    /**
     * Add a Shortcode
     *
     * @param array $data {
     * @type string $id Tag without prefix. Required.
     * @type callable $callable Render function. Gets $atts. Required.
     * @type array $atts Default atts.
     * }
     */
    public function addShortcode(array $data)
    {
        $this->m('Shortcodes')->add($data);
    }

    /**
     * Add multiple Shortcodes
     *
     * @param array $data {
     * @type string $id Tag without prefix. Required.
     * @type callable $callable Render function. Gets $atts. Required.
     * @type array $atts Default atts.
     * }
     */
    public function addShortcodes(array $data)
    {
        $this->m('Shortcodes')->addMany($data);
    }

    /**
     * Add a sidebar
     *
     * @param array $data {
     * @type string $id Defaults to sanitized $name.
     * @type string $name Sidebar Title. Required.
     * @type string $description
     * @type string $class CSS class for container.
     * }
     *
     * @see register_sidebar()
     */
    public function addSidebar(array $data)
    {
        $this->m('Sidebars')->add($data);
    }

    /**
     * Add multiple sidebars
     *
     * @param array $data {
     * @type string $id Defaults to sanitized $name.
     * @type string $name Sidebar Title. Required.
     * @type string $description
     * @type string $class CSS class for container.
     * }
     *
     * @see register_sidebar()
     */
    public function addSidebars(array $data)
    {
        $this->m('Sidebars')->addMany($data);
    }

    /**
     * Add Self-Update feature for plugin or theme
     *
     * @param array $data {
     * @type string $type plugin/theme.
     * @type string $id ID for internal use. Defaults to sanitized $path.
     * @type string $path Path to the plugin's main file. Required for plugins.
     * @type string $slug Theme's directory name. Required for themes.
     * @type string $package URL of the package.
     * }
     */
    public function updater(array $data)
    {
        $this->m('Updater')->add($data);
    }

    /**
     * Add Dashboard Widget
     *
     * @param array $data {
     * @type string $id Defaults to sanitized $title.
     * @type string $title Widget Title. Required.
     * @type callable $callback Renders the widget. Required.
     * @type string $capability Minimum capability. Default 'read'.
     * }
     */
    public function addDashboardWidget(array $data)
    {
        $this->m('Widgets')->add($data);
    }

    /**
     * Add Dashboard Widgets
     *
     * @param array $data {
     * @type string $id Defaults to sanitized $title.
     * @type string $title Widget Title. Required.
     * @type callable $callback Renders the widget. Required.
     * @type string $capability Minimum capability. Default 'read'.
     * }
     */
    public function addDashboardWidgets(array $data)
    {
        $this->m('Widgets')->addMany($data);
    }
}