<?php

namespace AlexDashkin\Adwpfw;

use AlexDashkin\Adwpfw\Modules\Basic\Helpers;
use AlexDashkin\Adwpfw\Modules\Basic\Module;

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
     * @return Module
     */
    public function m($moduleName)
    {
        if (array_key_exists($moduleName, $this->modules)) {
            return $this->modules[$moduleName];
        }

        $class = '\\' . __NAMESPACE__ . '\\Modules\\' . $moduleName;

        $this->modules[$moduleName] = new $class($this);

        return $this->modules[$moduleName];
    }

    /**
     * Add an AJAX action (admin-ajax.php)
     *
     * @param array $action {
     * @type string $id Action ID without prefix (that will be added automatically)
     * @type array $fields Accepted params [type, required]
     * @type callable $callback Handler
     * }
     */
    public function addAjaxAction(array $action)
    {
        $this->m('Ajax')->add($action);
    }

    /**
     * Add multiple AJAX actions (admin-ajax.php)
     *
     * @param array $actions
     *
     * @see Ajax::addAction()
     */
    public function addAjaxActions(array $actions)
    {
        $this->m('Ajax')->addMany($actions);
    }

    /**
     * Add an REST API Endpoint (/wp-json/)
     *
     * @param array $endpoint {
     * @type string $namespace Namespace with trailing slash (i.e. prefix/v1/)
     * @type string $route Route without slashes (i.e. users)
     * @type string $method get/post. Default "post".
     * @type bool $admin Whether available for admins only. Default false.
     * @type array $fields Accepted params [type, required]
     * @type callable $callback Handler
     * }
     */
    public function addApiEndpoint(array $endpoint)
    {
        $this->m('Rest')->add($endpoint);
    }

    /**
     * Add multiple REST API Endpoints (/wp-json/)
     *
     * @param array $endpoints
     *
     * @see Ajax::addEndpoint()
     */
    public function addApiEndpoints(array $endpoints)
    {
        $this->m('Rest')->addMany($endpoints);
    }

    /**
     * Add a Cron Job
     *
     * @param array $job {
     * @type string $id Job ID without prefix (that will be added automatically)
     * @type callable $callback Handler
     * @type int $interval Interval in seconds. Default 0.
     * @type bool $parallel Allow parallel execution. Default false.
     * @type array $args Args to be passed to the handler
     * }
     */
    public function addCronJob(array $job)
    {
        $this->m('Cron')->add($job);
    }

    /**
     * Add multiple Cron Jobs
     *
     * @param array $jobs
     *
     * @see Cron::addJob()
     */
    public function addCronJobs(array $jobs)
    {
        $this->m('Cron')->addMany($jobs);
    }

    /**
     * Remove main cron job from WP (used on plugin deactivation)
     */
    public function deactivateCron()
    {
        $this->m('Cron')->deactivate();
    }

    /**
     * Add a Settings Page to the left WP Admin Menu
     *
     * @param array $menu {
     * @type string $parent Parent Menu slug. If specified, a sub menu will be added.
     * @type string $id Menu slug. Defaults to sanitized Title.
     * @type string $prefix Prefix for slugs. Default config prefix.
     * @type string $name Text for the left Menu. Default "Settings".
     * @type string $title Text for the <title> tag. Defaults to $name.
     * @type string $header Page header. Defaults to $name.
     * @type string $icon The dash icon name for the bar
     * @type int $position Position in the Menu. Default 100.
     * @type string $option WP Option name to store the data (if $values isn't passed by reference)
     * @type array $values Data to fill out the form and to be modified (normally passed by reference)
     * @type string $capability Capability level to see the Page. Default "administrator"
     * @type array $tabs Tabs: {
     * @type string $name Tab Name
     * @type bool $form Whether to wrap content with the <form> tag
     * @type array $options Tab fields
     * @type array $buttins Buttons at the bottom of the Tab
     * }
     * @type string $callback Render function
     * }
     */
    public function addAdminPage(array $menu)
    {
        $this->m('AdminPages')->add($menu);
    }

    /**
     * Add multiple Settings pages
     *
     * @param array $menus
     *
     * @see AdminPages::addMenu()
     */
    public function addAdminPages(array $menus)
    {
        $this->m('AdminPages')->addMany($menus);
    }

    /**
     * Add an item to the Top Admin Bar
     *
     * @param array $bar {
     * @type string $id
     * @type string $title
     * @type string $capability Who can see the Bar
     * @type string $href URL of the link
     * @type array $meta
     * }
     */
    public function addAdminBar(array $bar)
    {
        $this->m('AdminBar')->add($bar);
    }

    /**
     * Add multiple items to the Top Admin Bar
     *
     * @param array $bars
     *
     * @see AdminBars::addBar()
     */
    public function addAdminBars(array $bars)
    {
        $this->m('AdminBar')->addMany($bars);
    }

    /**
     * Add a Metabox
     *
     * @param array $metabox {
     * @type string $id
     * @type string $prefix
     * @type string $title
     * @type array $screen For which Post Types to show
     * @type string $context
     * @type string $priority
     * @type array $options Fields to be printed
     * }
     */
    public function addMetabox(array $metabox)
    {
        $this->m('Metaboxes')->add($metabox);
    }

    /**
     * Add multiple Metaboxes
     *
     * @param array $metaboxes
     *
     * @see Metaboxes::addMetabox()
     */
    public function addMetaboxes(array $metaboxes)
    {
        $this->m('Metaboxes')->addMany($metaboxes);
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
        return $this->m('Metaboxes')->get($id, $post); // todo implement
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
        return $this->m('Metaboxes')->set($id, $value, $post); // todo implement
    }

    /**
     * Add Admin Notice
     *
     * @param array $notice {
     * @type string $id
     * @type string $message Message to display (tpl will be ignored)
     * @type string $tpl Name of the notice TWIG template
     * @type string $type Notice type (success, error)
     * @type bool $dismissible Whether can be dismissed
     * @type bool $once Don't show after dismissed
     * @type string $classes Container classes
     * @type array $args Additional TWIG Args
     * }
     */
    public function addNotice(array $notice)
    {
        $this->m('Notices')->add($notice);
    }

    /**
     * Add multiple Admin Notices
     *
     * @param array $notices
     *
     * @see Notices::addNotice()
     */
    public function addNotices(array $notices)
    {
        $this->m('Notices')->addMany($notices);
    }

    /**
     * Show a notice
     *
     * @param string $id Notice ID
     */
    public function showNotice($id)
    {
        $this->m('Notices')->show($id); // todo implement
    }

    /**
     * Stop showing a notice
     *
     * @param string $id Notice ID
     */
    public function stopNotice($id)
    {
        $this->m('Notices')->stop($id); // todo implement
    }

    /**
     * Dismiss a notice
     *
     * @param string $id Notice ID
     */
    public function dismissNotice($id)
    {
        $this->m('Notices')->dismiss($id); // todo implement
    }

    /**
     * Add a Custom Post Type
     *
     * @param array $postType
     *
     * @see register_post_type()
     */
    public function addPostType(array $postType)
    {
        $this->m('PostTypes')->add($postType);
    }

    /**
     * Add multiple Post Types
     *
     * @param array $postTypes
     *
     * @see PostTypes::addPostType()
     */
    public function addPostTypes(array $postTypes)
    {
        $this->m('PostTypes')->addMany($postTypes);
    }

    /**
     * Set User Profile Fields Group Heading
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
     * @param array $field {
     * @type string $id
     * @type string $type
     * @type string $name
     * @type string $desc
     * }
     */
    public function addProfileField(array $field)
    {
        $this->m('Profile')->add($field);
    }

    /**
     * Add multiple Profile Fields
     *
     * @param array $fields
     *
     * @see Profile::addField()
     */
    public function addProfileFields(array $fields)
    {
        $this->m('Profile')->addMany($fields);
    }

    /**
     * Get a field value
     *
     * @param string $id Field ID
     * @param int|null $userId User ID (defaults to the current user)
     * @return mixed
     */
    public function profileGet($id, $userId = null)
    {
        return $this->m('Profile')->get($id, $userId); // todo implement
    }

    /**
     * Add a post state
     *
     * @param int $postId
     * @param string $state State text
     */
    public function addPostState($postId, $state)
    {
        $this->m('PostStates')->add($postId, $state);
    }

    /**
     * Add a Widget
     *
     * @param array $widget {
     * @type string $id
     * @type string $title
     * @type string $capability
     * @type callable $callback Renders the widget
     * }
     */
    public function addWidget(array $widget)
    {
        $this->m('Widget')->add($widget);
    }

    /**
     * Add multiple Widgets
     *
     * @param array $widgets
     *
     * @see Widgets::addWidget()
     */
    public function addWidgets(array $widgets)
    {
        $this->m('Widget')->addMany($widgets);
    }

    /**
     * Add Self-Update feature for a plugin
     *
     * @param array $plugin {
     * @type string $path Path to the plugin's main file
     * @type string $package URL of the package
     * }
     */
    public function updater(array $plugin)
    {
        $this->m('Updater')->add($plugin);
    }

    /**
     * Add Admin CSS items
     *
     * @param array $args {
     * @type bool $own Whether it's the own asset stored in the assets folder
     * @type string $url
     * @type string $ver Version
     * @type array $deps Dependencies slugs
     * }
     */
    public function addAsset(array $args)
    {
        $this->m('Assets')->add($args);
    }

    /**
     * Remove assets
     *
     * @param array $ids Registered assets IDs to be removed
     */
    public function removeAssets(array $ids)
    {
        $this->m('Assets')->remove($ids);
    }

    /**
     * Add sections to the Customizer
     *
     * @param array $sections
     */
    public function customizerAdd(array $sections)
    {
        $this->m('Customizer')->add($sections);
    }

    /**
     * Get a Customizer setting
     *
     * @param string $id Setting ID
     * @return mixed
     */
    public function customizerGet($id)
    {
        return $this->m('Customizer')->get($id);
    }

    /**
     * Add a Shortcode
     *
     * @param array $shortcode {
     * @type string $tag Tag without prefix
     * @type array $atts Default atts
     * @type callable $callable Render function
     * }
     */
    public function addShortcode(array $shortcode)
    {
        $this->m('Shortcodes')->add($shortcode);
    }

    /**
     * Add multiple Shortcodes
     *
     * @param array $shortcodes
     *
     * @see Shortcodes::addShortcode()
     */
    public function addShortcodes(array $shortcodes)
    {
        $this->m('Shortcodes')->addMany($shortcodes);
    }

    /**
     * Add a sidebar
     *
     * @param array $sidebar
     *
     * @see register_sidebar()
     */
    public function addSidebar(array $sidebar)
    {
        $this->m('Sidebar')->add($sidebar);
    }

    /**
     * Perform a DB Query
     *
     * @param string $query SQL Query
     * @param array $values If passed, $wpdb->prepare() will be executed first
     * @return mixed
     */
    public function dbQuery($query, array $values = [])
    {
        return $this->m('Db')->query($query, $values);
    }

    /**
     * Insert Data into a table
     *
     * @param string $table Table Name
     * @param array $data Data to insert
     * @param bool $own Is own table?
     * @return int|bool Insert ID or false if failed
     */
    public function dbInsert($table, array $data, $own = true)
    {
        return $this->m('Db')->insert($table, $data, $own);
    }

    /**
     * Update Data in a table
     *
     * @param string $table Table Name
     * @param array $data Data to insert
     * @param array $where Conditions
     * @param bool $own Is own table?
     * @return int|bool Insert ID or false if failed
     */
    public function dbUpdate($table, array $data, array $where, $own = true)
    {
        return $this->m('Db')->update($table, $data, $where, $own);
    }

    /**
     * Insert or Update Data if exists
     *
     * @param string $table Table Name
     * @param array $data Data to insert
     * @param array $where Conditions
     * @param bool $own Is own table?
     * @return int|bool Insert ID or false if failed
     */
    public function dbInsertOrUpdate($table, array $data, array $where, $own = true)
    {
        return $this->m('Db')->insertOrUpdate($table, $data, $where, $own);
    }

    /**
     * Delete rows from a table
     *
     * @param string $table Table Name
     * @param array $where Conditions
     * @param bool $own Is own table?
     * @return bool Succeed?
     */
    public function dbDelete($table, array $where, $own = true)
    {
        return $this->m('Db')->delete($table, $where, $own);
    }

    /**
     * Get Var
     *
     * @param string $table Table Name
     * @param string $var Field name
     * @param array $where Conditions
     * @param bool $own Is own table?
     * @return mixed
     */
    public function dbGetVar($table, $var, array $where, $own = true)
    {
        return $this->m('Db')->getVar($table, $var, $where, $own);
    }

    /**
     * Get Results
     *
     * @param string $table Table Name
     * @param array $fields List of Fields
     * @param array $where Conditions
     * @param bool $single Get single row?
     * @param bool $own Is own table?
     * @return mixed
     */
    public function dbGetResults($table, array $fields = [], array $where = [], $single = false, $own = true)
    {
        return $this->m('Db')->getResults($table, $fields, $where, $single, $own);
    }

    /**
     * Get Results with an arbitrary Query
     *
     * @param string $query SQL query
     * @param array $values If passed, $wpdb->prepare() will be executed first
     * @return mixed
     */
    public function dbGetResultsQuery($query, array $values = [])
    {
        return $this->m('Db')->getResultsQuery($query, $values);
    }

    /**
     * Get Results Count
     *
     * @param string $table Table Name
     * @param array $where Conditions
     * @param bool $own Is own table?
     * @return int
     */
    public function dbGetCount($table, array $where = [], $own = true)
    {
        return $this->m('Db')->getCount($table, $where, $own);
    }

    /**
     * Get Last Insert ID
     *
     * @return int
     */
    public function dbInsertId()
    {
        return $this->m('Db')->insertId();
    }

    /**
     * Insert Multiple Rows with one query
     *
     * @param string $table Table Name
     * @param array $data Data to insert
     * @param bool $own Is own table?
     * @return bool
     */
    public function dbInsertRows($table, array $data, $own = true)
    {
        return $this->m('Db')->insertRows($table, $data, $own);
    }

    /**
     * Truncate a table
     *
     * @param $table
     * @param bool $own
     * @return bool
     */
    public function dbTruncateTable($table, $own = true)
    {
        return $this->m('Db')->truncateTable($table, $own);
    }

    /**
     * Check own tables existence
     *
     * @param array $tables List of own tables
     * @return bool
     */
    public function dbCheckTables(array $tables)
    {
        return $this->m('Db')->checkTables($tables);
    }

    /**
     * Get table name with all prefixes
     *
     * @param string $name
     * @param bool $own
     * @return string
     */
    public function dbGetTable($name, $own = true)
    {
        return $this->m('Db')->getTable($name, $own);
    }

    /**
     * Render TWIG templates
     *
     * @param string $name Template file name
     * @param array $args
     * @return string
     */
    public function renderTwig($name, array $args = [])
    {
        return $this->m('Twig')->renderFile($name, $args);
    }

    /**
     * Get path/url to the WP Uploads dir
     *
     * @param string $path Path inside the uploads dir (will be created if not exists)
     * @param bool $getUrl Whether to get URL instead of the path
     * @return string
     */
    public function getUploadsDir($path = '', $getUrl = false)
    {
        return $this->m('Utils')->getUploadsDir($path, $getUrl);
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
    public function apiRequest(array $args)
    {
        return $this->m('Utils')->apiRequest($args);
    }

    /**
     * Return success response
     *
     * @param string $message
     * @param array $data
     * @param bool $echo
     * @return array
     */
    public function success($message = 'Done', array $data = [], $echo = false)
    {
        return $this->m('Utils')->returnSuccess($message, $data, $echo);
    }

    /**
     * Return error response
     *
     * @param string $message
     * @param bool $echo
     * @return array
     */
    public function error($message = 'Unknown Error', $echo = false)
    {
        return $this->m('Utils')->returnError($message, $echo);
    }

    /**
     * Add a log entry
     *
     * @param mixed $message Text or any other type including \WP_Error
     * @param int $type 1 = Error, 2 = Warning, 4 = Notice @deprecated
     */
    public function log($message, $values = [], $type = 4)
    {
        $this->m('Logger')->log($message, $values, $type);
    }

    /**
     * Handle false and \WP_Error returns
     *
     * @param mixed $result
     * @param string $errorMessage
     * @return bool
     */
    public function pr($result, $errorMessage = '')
    {
        return $this->m('Utils')->pr($result, $errorMessage);
    }

    /**
     * Simple cache
     *
     * @param callable $callable
     * @param array $args
     * @return mixed
     */
    public function cache($callable, $args = [])
    {
        return $this->m('Utils')->cache($callable, $args);
    }

    /**
     * Search in an array
     *
     * @param array $array
     * @param array $conditions
     * @param bool $single
     * @return mixed
     */
    public function arraySearch(array $array, array $conditions, $single = false)
    {
        return Helpers::arraySearch($array, $conditions, $single);
    }

    /**
     * Filter an array
     *
     * @param array $array
     * @param array $conditions
     * @param bool $single
     * @return array
     */
    public function arrayFilter(array $array, array $conditions, $single = false)
    {
        return Helpers::arrayFilter($array, $conditions, $single);
    }

    /**
     * Remove duplicates by key
     *
     * @param array $array
     * @param string $key
     * @return array
     */
    public function arrayUniqueByKey(array $array, $key)
    {
        return Helpers::arrayUniqueByKey($array, $key);
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
    public function arrayParse(array $array, array $keys, $index = null, $sort = false)
    {
        return Helpers::arrayParse($array, $keys, $index, $sort);
    }

    /**
     * Sort an array by key
     *
     * @param array $array
     * @param $key
     * @param bool $keepKeys Keep key=>value links when sorting
     * @return array
     */
    public function arraySortByKey(array $array, $key, $keepKeys = false)
    {
        return Helpers::arraySortByKey($array, $key, $keepKeys);
    }

    /**
     * Arrays deep merge
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
     * Add an element to an array if not exists
     *
     * @param array $where
     * @param array $what
     * @return array
     */
    public function arrayAddNonExistent(array $where, array $what)
    {
        return Helpers::arrayAddNonExistent($where, $what);
    }

    /**
     * Recursive implode
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
     * Check plugin/theme dependencies before start
     *
     * @param string $pluginName Name of calling plugin to display in Notice
     * @param array $deps {
     * @type string $name Plugin or Theme name
     * @type string $type Type of the dep (class/function)
     * @type string $dep Class or function name
     * }
     * @return bool Passed?
     */
    public function checkDeps($pluginName, array $deps)
    {
        return $this->m('Utils')->checkDeps($pluginName, $deps);
    }

    /**
     * Trim vars and arrays
     *
     * @param array|string $var
     * @return array|string
     */
    public function trim($var)
    {
        return Helpers::trim($var);
    }

    /**
     * Get output of a function
     *
     * @param string|array $func Callable
     * @param array $args Function args
     * @return string Output
     */
    public function getOutput($func, $args = [])
    {
        return Helpers::getOutput($func, $args);
    }

    /**
     * Convert HEX color to RGB
     *
     * @param string $hex
     * @return string
     */
    public function colorToRgb($hex)
    {
        return Helpers::colorToRgb($hex);
    }
}