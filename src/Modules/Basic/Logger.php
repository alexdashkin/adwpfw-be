<?php

namespace AlexDashkin\Adwpfw\Modules\Basic;

use AlexDashkin\Adwpfw\App;

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
     * Logger constructor.
     * @param App $app
     */
    public function __construct(App $app)
    {
        parent::__construct($app);

        $this->init();
    }

    /**
     * Init Logger.
     */
    public function init()
    {
        $this->start = date('d.m.y H:i:s');

        $prefix = $this->config['prefix'];
        $suffix = function_exists('wp_hash') ? wp_hash($prefix) : md5($prefix);

        $basePath = Helpers::getUploadsDir($prefix, 'logs');

        $filename = '/' . $prefix . '-' . date('Y-m-d') . '-' . $suffix . '.log';
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
     * Add a log entry.
     *
     * @param mixed $message Text or any other type including WP_Error.
     * @param array $values If passed, vsprintf() func is applied.
     * @param int $type 1 = Error, 2 = Warning, 4 = Notice.
     */
    public function log($message, $values = [], $type = 4)
    {
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
        if (!$this->contents || empty($this->config['log'])) {
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