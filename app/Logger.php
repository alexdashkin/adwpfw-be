<?php

namespace AlexDashkin\Adwpfw;

use AlexDashkin\Adwpfw\Exceptions\AppException;

/**
 * Logger
 */
class Logger
{
    /**
     * Class instance
     *
     * @var static
     */
    private static $instance;

    /**
     * @var int Start Timestamp
     */
    private $start;

    /**
     * @var string Log contents
     */
    private $contents;

    /**
     * @var array Paths to log files
     */
    private $paths = [];

    /**
     * @var string Path to the file where logs are written immediately
     */
    private $immediatePath;

    /**
     * Protect instance constructor
     */
    private function __construct()
    {
    }

    /**
     * Add a log entry
     *
     * @param mixed $message Text or any other type including WP_Error.
     * @param array $values If passed, vsprintf() func is applied. Default [].
     */
    public static function log($message, array $values = [])
    {
        if (empty(self::$instance)) {
            throw new AppException('Logger is not configured');
        }

        self::$instance->addEntry($message, $values);
    }

    /**
     * Set config and init
     *
     * @param array $args
     */
    public static function init(array $args)
    {
        foreach (['prefix', 'maxLogSize', 'basePath'] as $fieldName) {
            if (empty($args[$fieldName])) {
                throw new AppException(sprintf('Field "%s" is required', $fieldName));
            }
        }

        self::$instance = new self();

        // Set config
        $prefix = $args['prefix'];
        $maxLogSize = $args['maxLogSize'];
        $basePath = $args['basePath'];

        // Prepare vars
        self::$instance->start = date('d.m.y H:i:s');
        $suffix = function_exists('wp_hash') ? wp_hash($prefix) : md5($prefix);
        $filename = self::$instance->getLogFilename($basePath, $prefix, $suffix, $maxLogSize);
        $immediateName = uniqid() . '-' . $suffix . '.log';

        // Add paths
        self::$instance->paths[] = $basePath . $filename;
        self::$instance->immediatePath = $basePath . $immediateName;

        // Add WC Log path
        if (defined('WC_LOG_DIR') && file_exists(WC_LOG_DIR)) {
            self::$instance->paths[] = WC_LOG_DIR . $filename;
        }
    }

    /**
     * Iterate existing files and find the proper one
     *
     * @param string $basePath
     * @param string $prefix
     * @param string $suffix
     * @param int $maxSize
     * @param int $counter
     * @return string
     */
    private function getLogFilename(string $basePath, string $prefix, string $suffix, int $maxSize, int $counter = 1): string
    {
        $filename = sprintf('%s-%s-%s-%s.log', $prefix, date('Y-m-d'), $counter, $suffix);
        $filePath = trailingslashit($basePath) . $filename;

        if (file_exists($filePath) && filesize($filePath) > $maxSize) {
            return $this->getLogFilename($basePath, $prefix, $suffix, $maxSize, ++$counter);
        }

        return $filename;
    }

    /**
     * Add a log entry
     *
     * @param mixed $message Text or any other type including WP_Error.
     * @param array $values If passed, vsprintf() func is applied. Default [].
     */
    private function addEntry($message, array $values = [])
    {
        // Error if not configured
        if (empty($this->start)) {
            throw new AppException('Logger is not configured');
        }

        // WP_Error to string
        if (is_wp_error($message)) {
            $message = 'WP_Error: ' . implode(' | ', $message->get_error_messages());
        }

        // Populate args if any
        if (is_string($message)) {
            $message = vsprintf($message, $values);
        }

        // Build log entry
        $this->contents .= '[' . date('d.m.y H:i:s') . '] ' . print_r($message, true) . "\n";

        // Write to immediate log
        if ($this->immediatePath) {
            file_put_contents($this->immediatePath, $this->contents);
        }
    }

    /**
     * Flush log content to the files
     */
    public function __destruct()
    {
        // Return if nothing to output
        if (!$this->contents) {
            return;
        }

        // Starting line
        $log = 'Started: ' . $this->start . "\n" . $this->contents . "\n";

        // Write to each path
        foreach ($this->paths as $path) {
            $logFile = fopen($path, 'a');
            fwrite($logFile, $log);
            fclose($logFile);
        }

        // Delete immediate log file
        if (file_exists($this->immediatePath)) {
            unlink($this->immediatePath);
        }
    }
}
