<?php

namespace AlexDashkin\Adwpfw\Common;

/**
 * Dynamic Helpers
 */
class Utils extends Base
{
    private $twig;
    private $cache;

    public function __construct($app)
    {
        parent::__construct($app);
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
        $notFound = [];

        foreach ($deps as $name => $dep) {
            if (('class' === $dep['type'] && !class_exists($dep['dep']))
                || ('function' === $dep['type'] && !function_exists($dep['dep']))) {
                $notFound[$name] = $dep['name'];
            }
        }

        if (!$notFound) {
            return true;
        }

        $message = "<b>$pluginName</b>: the following plugins are not active: " . implode(', ', $notFound) . ". $pluginName Disabled.";

        $this->m('Admin\Notices')->addNotice([
            'type' => 'error',
            'message' => $message,
        ]);

        return false;
    }

    /**
     * Render simple templates i.e. {{var}}
     *
     * @param string $tpl Template
     * @param array $args
     * @return string
     */
    public function renderTpl($tpl, array $args = [])
    {
        $from = $to = [];

        foreach ($args as $key => $value) {
            if (is_scalar($key) && is_scalar($value)) {
                $from[] = '{{' . $key . '}}';
                $to[] = $value;
            }
        }

        return str_replace($from, $to, $tpl);
    }

    /**
     * Render TWIG templates
     *
     * @param string $name Template file name
     * @param array $args
     * @return string
     */
    public function renderTwig($name, $args = [])
    {
        $twig = $this->getTwig();

        $args = array_merge([
            'prefix' => $this->config['prefix'],
            'id' => '',
            'classes' => '',
            'desc' => '',
        ], $args);

        return $twig->render($name . '.twig', $args);
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
        $prefix = $this->config['prefix'];

        $uploadDir = wp_upload_dir();
        $basePath = $uploadDir['basedir'] . '/' . $prefix . '/';
        $fullPath = $basePath . $path;

        if (!file_exists($fullPath)) {
            if (wp_mkdir_p($fullPath)) {
                $this->log("Dir $fullPath created successfully");
            } else {
                $this->log('Error while creating dir ' . $fullPath, 1);
            }
        }

        if ($getUrl) {
            $baseUrl = $uploadDir['baseurl'] . '/' . $prefix . '/';
            return $baseUrl . $path;
        }

        return $fullPath;
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
        $args = array_merge([
            'method' => 'get',
            'headers' => [],
            'data' => [],
            'timeout' => $this->config['apiTimeout'],
        ], $args);

        $url = $args['url'];
        $method = strtoupper($args['method']);
        $data = $args['data'];

        $requestArgs = [
            'method' => strtoupper($args['method']),
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
     * Return success response
     *
     * @param string $message
     * @param array $data
     * @param bool $echo
     * @return array
     */
    public function returnSuccess($message = 'Done', array $data = [], $echo = false)
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
     * Return error response
     *
     * @param string $message
     * @param bool $echo
     * @return array
     */
    public function returnError($message = 'Unknown Error', $echo = false)
    {
        $message = $message ?: 'Unknown Error';

        $this->log($message, 1);

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
    public function pr($result, $errorMessage = '')
    {
        if (!$result || is_wp_error($result)) {
            $message = $errorMessage ? 'Error: ' . $errorMessage : 'Error!';
            $this->log($message, 1);

            if ($result) {
                $this->log($result);
            }

            return false;
        }

        return $result;
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
        $cacheArgs = $args;

        foreach ($cacheArgs as $index => $arg) {
            if (!is_scalar($arg)) {
                $cacheArgs[$index] = maybe_serialize($arg);
            }
        }

        $funcName = is_array($callable) ? get_class($callable[0]) . $callable[1] : $callable;
        $cacheKey = md5($funcName . implode('', $cacheArgs));

        if (isset($this->cache[$cacheKey])) {
            return $this->cache[$cacheKey];
        }

        $result = call_user_func_array($callable, $args);

        $this->cache[$cacheKey] = $result;

        return $result;
    }

    private function getTwig()
    {
        if ($this->twig) {
            return $this->twig;
        }

        $paths = [
            $this->config['baseDir'] . '/tpl/adwpfw',
            $this->config['baseDir'] . '/tpl',
            __DIR__ . '/../../tpl',
        ];

        foreach ($paths as $index => $path) {
            if (!file_exists($path)) {
                unset($paths[$index]);
            }
        }

        if (class_exists('\Twig\Environment')) {
            $this->twig = new \Twig\Environment(new \Twig\Loader\FilesystemLoader($paths), ['autoescape' => false]);
            return $this->twig;
        }

        return false;
    }

}