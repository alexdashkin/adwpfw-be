<?php

namespace AlexDashkin\Adwpfw\Modules;

/**
 * Logger
 */
class Logger extends Module
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
     * @var array Paths tp log files
     */
    private $paths = [];

    /**
     * @var string Path to the file where logs are written immediately
     */
    private $immediatePath;

    /**
     * Init Module
     */
    public function init()
    {
        $this->validateData();

        // Prepare vars
        $prefix = $this->gp('prefix');
        $maxLogSize = $this->gp('size');
        $this->start = date('d.m.y H:i:s');
        $suffix = function_exists('wp_hash') ? wp_hash($prefix) : md5($prefix);
        $basePath = $this->m('helpers')->getUploadsDir($prefix . '/logs');
        $filename = $this->getLogFilename($basePath, $prefix, $suffix, $maxLogSize);
        $immediateName = uniqid() . '-' . $suffix . '.log';

        // Add paths
        $this->paths[] = $basePath . $filename;
        $this->immediatePath = $basePath . $immediateName;

        // Add WC Log path
        if (defined('WC_LOG_DIR') && file_exists(WC_LOG_DIR)) {
            $this->paths[] = WC_LOG_DIR . $filename;
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
    private function getLogFilename($basePath, $prefix, $suffix, $maxSize, $counter = 1)
    {
        $filename = $prefix . '-' . date('Y-m-d') . '-' . $suffix . '-' . $counter . '.log';
        $filePath = $basePath . $filename;

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
     * @param int $level 1 = Error, 2 = Warning, 4 = Notice. Default 4.
     */
    public function log($message, array $values = [], int $level = 4)
    {
        // Skip if message level is lower than defined in config
        if (!($level & $this->gp('level'))) {
            return;
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

    /**
     * Get Class props
     *
     * @return array
     */
    protected function getInitialPropDefs(): array
    {
        return [
            'prefix' => [
                'required' => true,
            ],
            'level' => [
                'default' => 7,
            ],
            'size' => [
                'default' => 1000000,
            ],
        ];
    }
}