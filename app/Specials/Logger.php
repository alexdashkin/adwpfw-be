<?php

namespace AlexDashkin\Adwpfw\Specials;

use AlexDashkin\Adwpfw\Exceptions\AppException;

/**
 * Logger
 */
class Logger
{
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
    private $tmpPath;

    /**
     * Constructor
     */
    public function __construct(array $config)
    {
        foreach (['prefix', 'maxLogSize', 'path'] as $fieldName) {
            if (empty($config[$fieldName])) {
                throw new AppException(sprintf('Field "%s" is required', $fieldName));
            }
        }

        // Set config
        $prefix = $config['prefix'];
        $maxLogSize = $config['maxLogSize'];
        $path = $config['path'];

        // Prepare vars
        $this->start = date('d.m.y H:i:s');
        $suffix = function_exists('wp_hash') ? wp_hash($prefix) : md5($prefix);
        $filename = $this->getLogFilename($path, $prefix, $suffix, $maxLogSize);
        $tmpName = sprintf('%s-temp-%s.log', $prefix, date('Y-m-d-H:i:s'));

        // Add paths
        $this->paths[] = $path . $filename;
        $this->tmpPath = $path . $tmpName;

        // Add WC Log path
        if (defined('WC_LOG_DIR') && file_exists(WC_LOG_DIR)) {
            $this->paths[] = WC_LOG_DIR . $filename;
        }
    }

    /**
     * Iterate existing files and find the proper one
     *
     * @param string $path
     * @param string $prefix
     * @param string $suffix
     * @param int $maxSize
     * @param int $counter
     * @return string
     */
    private function getLogFilename(string $path, string $prefix, string $suffix, int $maxSize, int $counter = 1): string
    {
        $filename = sprintf('%s-%s-%s-%s.log', $prefix, date('Y-m-d'), $counter, $suffix);
        $filePath = trailingslashit($path) . $filename;

        if (file_exists($filePath) && filesize($filePath) > $maxSize) {
            return $this->getLogFilename($path, $prefix, $suffix, $maxSize, ++$counter);
        }

        return $filename;
    }

    /**
     * Add a log entry
     *
     * @param mixed $message Text or any other type including WP_Error.
     * @param array $values If passed, vsprintf() func is applied. Default [].
     */
    public function log($message, array $values = [])
    {
        // WP_Error to string
        if (is_wp_error($message)) {
            $message = 'WP_Error: ' . implode(' | ', $message->get_error_messages());
        }

        // Populate args if any
        if (is_string($message)) {
            $message = $values ? vsprintf($message, $values) : $message;
        }

        // Build log entry
        $this->contents .= '[' . date('d.m.y H:i:s') . '] ' . print_r($message, true) . "\n";

        // Write to immediate log
        if ($this->tmpPath) {
            file_put_contents($this->tmpPath, $this->contents);
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
            file_put_contents($path, $log, FILE_APPEND);
        }

        // Delete immediate log file
        if (file_exists($this->tmpPath)) {
            unlink($this->tmpPath);
        }
    }
}
