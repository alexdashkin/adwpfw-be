<?php

namespace AlexDashkin\Adwpfw\Modules;

use AlexDashkin\Adwpfw\App;

/**
 * Logger.
 */
class Logger
{
    /**
     * @var bool Logger enabled?
     */
    private $enabled = true;

    /**
     * @var int Start Timestamp
     */
    private $start;

    /**
     * @var string Log contents
     */
    private $contents;

    /**
     * @var array Paths tp log files
     */
    private $paths = [];

    /**
     * @var string Path to the file where logs are written immediately
     */
    private $immediatePath;

    /**
     * Logger constructor.
     * @param App $app
     */
    public function __construct(App $app)
    {
        $config = $app->config;
        $this->enabled = !empty($config['log']);

        if (!$this->enabled) {
            return;
        }

        $prefix = $config['prefix'];
        $maxLogSize = !empty($config['log_size']) ? $config['log_size'] : 1000000;
        $this->start = date('d.m.y H:i:s');
        $suffix = function_exists('wp_hash') ? wp_hash($prefix) : md5($prefix);
        $basePath = Helpers::getUploadsDir($prefix . '/logs');
        $filename = $this->getLogFilename($basePath, $prefix, $suffix, $maxLogSize);
        $immediateName = '/' . time() . '-' . $suffix . '.log';

        $this->paths[] = $basePath . $filename;
        $this->immediatePath = $basePath . $immediateName;

        add_action('init', function () use ($filename) {
            if (defined('WC_LOG_DIR') && file_exists(WC_LOG_DIR)) {
                $this->paths[] = WC_LOG_DIR . $filename;
            }
        });
    }

    /**
     * Iterate existing files and find not full one.
     *
     * @param string $basePath
     * @param string $prefix
     * @param string $suffix
     * @param int $maxSize
     * @param int $counter
     * @return string
     */
    private function getLogFilename($basePath, $prefix, $suffix, $maxSize, $counter = 1)
    {
        $filename = '/' . $prefix . '-' . date('Y-m-d') . '-' . $suffix . '-' . $counter . '.log';
        $filePath = $basePath . $filename;

        if (file_exists($filePath) && filesize($filePath) > $maxSize) {
            return $this->getLogFilename($basePath, $prefix, $suffix, $maxSize, ++$counter);
        }

        return $filename;
    }

    /**
     * Add a log entry.
     *
     * @param mixed $message Text or any other type including WP_Error.
     * @param array $values If passed, vsprintf() func is applied. Default [].
     * @param int $type 1 = Error, 2 = Warning, 4 = Notice. Default 4.
     */
    public function log($message, $values = [], $type = 4)
    {
        if (!$this->enabled) {
            return;
        }

        if (is_wp_error($message)) {
            $message = implode(' | ', $message->get_error_messages());
        }

        if (is_string($message)) {
            $message = vsprintf($message, $values);
        }

        $this->contents .= '[' . date('d.m.y H:i:s') . '] ' . print_r($message, true) . "\n";

        if ($this->immediatePath) {
            file_put_contents($this->immediatePath, $this->contents);
        }
    }

    /**
     * Flush log content to the files.
     */
    public function __destruct()
    {
        if (!$this->enabled || !$this->contents) {
            return;
        }

        $log = 'Started: ' . $this->start . "\n" . $this->contents . "\n";

        foreach ($this->paths as $path) {
            $logFile = fopen($path, 'a');
            fwrite($logFile, $log);
            fclose($logFile);
        }

        if (file_exists($this->immediatePath)) {
            unlink($this->immediatePath);
        }
    }
}