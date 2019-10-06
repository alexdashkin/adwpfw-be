<?php

namespace AlexDashkin\Adwpfw\Modules;

/**
 * Logging
 */
class Logger extends Base
{
    private $start;
    private $contents;
    private $immediatePath;

    public function __construct($app)
    {
        parent::__construct($app);
    }

    public function run()
    {
        $this->start = date('d.m.y H:i:s');

        $basePath = $this->m('Utils')->getUploadsDir('logs');
        $prefix = $this->config['prefix'];
        $suffix = md5($prefix);

        $this->immediatePath = $basePath . '/' . time() . '-' . $suffix . '.log';
    }

    /**
     * Add a log entry
     *
     * @param mixed $message Text or any other type including \WP_Error
     * @param int $type 1 = Error, 2 = Warning, 4 = Notice
     */
    public function log($message, $type = 4) // todo sprintf()
    {
        if (is_wp_error($message)) {
            $message = implode(' | ', $message->get_error_messages());
        }

        $this->contents .= '[' . date('d.m.y H:i:s') . '] ' . print_r($message, true) . "\n";

        if ($this->immediatePath) {
            file_put_contents($this->immediatePath, $this->contents);
        }
    }

    public function __destruct()
    {
        if (!$this->contents || empty($this->config['log'])) {
            return;
        }

        $log = "Started: " . $this->start . "\n" . $this->contents . "\n";

        $basePath = $this->m('Utils')->getUploadsDir('logs');
        $prefix = $this->config['prefix'];
        $suffix = function_exists('wp_hash') ? wp_hash($prefix) : md5($prefix);
        $filename = '/' . $prefix . '-' . date('Y-m-d') . '-' . $suffix . '.log';
        $paths = [$basePath . $filename];

        if (defined('WC_LOG_DIR') && file_exists(WC_LOG_DIR)) {
            $paths[] = WC_LOG_DIR . $filename;
        }

        foreach ($paths as $path) {
            $logFile = fopen($path, 'a');
            fwrite($logFile, $log);
            fclose($logFile);
        }

        if (file_exists($this->immediatePath)) {
            unlink($this->immediatePath);
        }
    }
}