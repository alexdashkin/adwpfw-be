<?php

namespace AlexDashkin\Adwpfw\Modules;

/**
 * Cron Jobs
 */
class Cron extends \AlexDashkin\Adwpfw\Common\Base
{
    private $jobs = [];
    private $option = [];
    private $jobName;
    private $path;
    private $running;

    public function __construct($app)
    {
        parent::__construct($app);
    }

    public function run()
    {
        $prefix = $this->config['prefix'];
        $this->jobName = $prefix . '_heartbeat';
        $this->option = get_option($prefix . '_cron') ?: [];
        $this->path = $this->m('Utils')->getUploadsDir('cron') . '/';

        add_filter('cron_schedules', function ($schedules) {
            $prefix = $this->config['prefix'];

            $schedules[$prefix . '_heartbeat'] = [
                'interval' => !empty($this->config['cron']['interval']) ? $this->config['cron']['interval'] : 3600,
                'display' => $prefix . ' heartbeat'
            ];

            return $schedules;
        });

        if (!wp_next_scheduled($this->jobName)) {
            wp_schedule_event(time() + 60, $prefix . '_heartbeat', $this->jobName); // todo heartbeat interval to config
        }

        add_action($this->jobName, [$this, 'runJob']);
        add_action('shutdown', [$this, 'updateOption']);
    }

    /**
     * Add a Cron Job
     *
     * @param array $job {
     * @type string $id Job ID without prefix (will be added automatically)
     * @type callable $callback Handler
     * @type int $id Interval in seconds
     * @type bool $parallel Allow parallel execution
     * @type array $args Args to be passed to the handler
     * }
     */
    public function addJob(array $job)
    {
        $this->jobs[] = array_merge([
            'id' => 'job',
            'callback' => null,
            'interval' => 0,
            'parallel' => false,
            'args' => [],
        ], $job);
    }

    /**
     * Add multiple Cron Jobs
     *
     * @param array $jobs
     *
     * @see Cron::addJob()
     */
    public function addJobs(array $jobs)
    {
        foreach ($jobs as $job) {
            $this->addJob($job);
        }
    }

    /**
     * Remove main cron job from WP (used on plugin deactivation)
     */
    public function deactivate()
    {
        $this->jobs = [];
        if ($timestamp = wp_next_scheduled($this->jobName)) {
            wp_unschedule_event($timestamp, $this->jobName);
        }
    }

    public function runJob()
    {
        foreach ($this->jobs as $job) {
            $jobId = $job['id'];
            $lastRun = !empty($this->option[$jobId]) ? (int)$this->option[$jobId] : 0;

            if (!$lastRun || (time() - $job['interval']) > $lastRun) {
                $this->log("Launching cron job $jobId...");

                $path = $this->path . $jobId;
                if (!$job['parallel']) {
                    if (file_exists($path) && ($launched = (int)file_get_contents($path)) && $launched > time() - 3600) {
                        $time = date('Y-m-d H:i:s', $launched);
                        $this->log("Another instance is running since $time, aborting");
                        continue;
                    }
                }

                $job['args'] = !empty($job['args']) ? $job['args'] : [];

                $this->running = $jobId;

                file_put_contents($path, time());

                try {
                    call_user_func($job['callback'], $job['args']);
                } catch (\Exception $e) {
                    $msg = 'Exception: ' . $e->getMessage() . '. Execution aborted.';
                    $this->log($msg);
                } finally {
                    file_put_contents($path, 0);
                }

                $this->option[$jobId] = time();

                $this->log('Done');
            }
        }
    }

    public function updateOption()
    {
        update_option($this->config['prefix'] . '_cron', $this->option);
    }

    public function __destruct()
    {
        if (!$this->running) {
            return;
        }

        $path = $this->path . $this->running;

        if (!file_exists($path)) {
            touch($path);
        }

        file_put_contents($path, 0);
    }
}
