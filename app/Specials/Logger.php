<?php

namespace AlexDashkin\Adwpfw\Specials;

use AlexDashkin\Adwpfw\Exceptions\AppException;

/**
 * Logger
 */
class Logger
{
    /**
     * @var \DateTime Start
     */
    private $start;

    /**
     * @var \DateTime Our first entry
     */
    private $first;

    /**
     * @var \DateTime Previous entry
     */
    private $prev;

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
        $this->start = $this->prev = \DateTime::createFromFormat('U.u', sprintf('%.6F', microtime(true)));
        $suffix = function_exists('wp_hash') ? wp_hash($prefix) : md5($prefix);
        $filename = $this->getLogFilename($path, $prefix, $suffix, $maxLogSize);
        $tmpName = sprintf('%s-temp-%s.log', $prefix, date('Y-m-d-H-i-s'));

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
            $message = sprintf("WP_Error: %s", implode(' | ', $message->get_error_messages()));
        }

        // Populate args if any
        if (is_string($message)) {
            $message = $values ? vsprintf($message, $values) : $message;
        }

        // Build log entry
        $time = \DateTime::createFromFormat('U.u', sprintf('%.6F', microtime(true)));

        if (!$this->first) {
            $this->first = $time;
        }

        $diff = $this->prev->diff($time);
        $total = $this->first->diff($time);
        $this->contents .= sprintf("[%s +%s %s] %s\n", $time->format('H:i:s.u'), $this->getDiffInSeconds($diff), $this->getDiffInSeconds($total), print_r($message, true));
        $this->prev = $time;

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

        // Wrap with start/finish time
        $finish = \DateTime::createFromFormat('U.u', sprintf('%.6F', microtime(true)));
        $started = $this->start->format('Y-m-d H:i:s.u');
        $finished = $finish->format('Y-m-d H:i:s.u');
        $our = $this->first->diff($this->prev);
        $total = $this->start->diff($finish);
        $log = sprintf("Started: %s\n%sFinished: %s, Our Time: %ss, Total Time: %ss\n\n", $started, $this->contents, $finished, $this->getDiffInSeconds($our), $this->getDiffInSeconds($total));

        // Write to each path
        foreach ($this->paths as $path) {
            file_put_contents($path, $log, FILE_APPEND);
        }

        // Delete immediate log file
        if (file_exists($this->tmpPath)) {
            unlink($this->tmpPath);
        }
    }

    /**
     * Get diff in seconds.ms
     *
     * @param \DateInterval $interval
     * @return string
     */
    private function getDiffInSeconds(\DateInterval $interval): string
    {
        return sprintf('%05.3f', $interval->s + $interval->i * 60 + $interval->h * 3600 + $interval->days * 86400 + $interval->format('%F') / 1000000);
    }
}
