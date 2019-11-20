<?php

namespace AlexDashkin\Adwpfw\Modules;

use AlexDashkin\Adwpfw\App;
use AlexDashkin\Adwpfw\Exceptions\AdwpfwException;
use AlexDashkin\Adwpfw\Items\CronJob;

/**
 * Cron Jobs.
 */
class Cron extends ModuleWithItems
{
    /**
     * @var CronJob[]
     */
    protected $items = [];

    /**
     * @var string Name of the main Cron job (Heartbeat).
     */
    private $jobName;

    /**
     * @var int Interval of the main Cron job (Heartbeat).
     */
    private $interval;

    /**
     * Cron constructor.
     *
     * @param App $app
     */
    public function __construct(App $app)
    {
        parent::__construct($app);

        $prefix = $this->config['prefix'];
        $this->jobName = $prefix . '_heartbeat';
        $this->interval = !empty($this->config['cron']['interval']) ? $this->config['cron']['interval'] : 3600;

        add_filter('cron_schedules', [$this, 'addInterval']);

        if (!wp_next_scheduled($this->jobName)) {
            wp_schedule_event(time() + $this->interval, $this->jobName, $this->jobName);
        }

        add_action($this->jobName, [$this, 'run']);
    }

    /**
     * Add Cron Job.
     *
     * @param array $data
     *
     * @throws AdwpfwException
     * @see CronJob::__construct()
     *
     */
    public function add(array $data)
    {
        $this->items[] = new CronJob($this->app, $data);
    }

    /**
     * Run Jobs
     */
    public function run()
    {
        foreach ($this->items as $item) {
            try {
                $item->run();
            } catch (\Exception $e) {
                $this->log($e->getMessage());
            }
        }
    }

    /**
     * Add new WP Cron Schedule (Interval).
     *
     * @param array $intervals
     * @return array Modified Intervals.
     */
    public function addInterval($intervals)
    {
        $intervals[$this->jobName] = [
            'interval' => $this->interval,
            'display' => $this->jobName,
        ];

        return $intervals;
    }

    /**
     * Remove main cron job from WP (to be used on plugin deactivation).
     */
    public function deactivate()
    {
        if ($timestamp = wp_next_scheduled($this->jobName)) {
            wp_unschedule_event($timestamp, $this->jobName);
        }
    }
}
