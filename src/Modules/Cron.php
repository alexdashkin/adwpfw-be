<?php

namespace AlexDashkin\Adwpfw\Modules;

use AlexDashkin\Adwpfw\App;
use AlexDashkin\Adwpfw\Items\CronJob;

/**
 * Cron Jobs
 */
class Cron extends ItemsModule
{
    private $jobName;
    private $interval;

    public function __construct($app)
    {
        parent::__construct($app);

        $this->init();
    }

    /**
     * Add an item
     *
     * @param array $data
     */
    public function add(array $data, App $app)
    {
        $this->items[] = new CronJob($data, $app);
    }

    private function init()
    {
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
     * Hooks to register Items in WP
     */
    protected function hooks()
    {
        add_action($this->jobName, [$this, 'run']);
    }

    public function run()
    {
        foreach ($this->items as $item) {
            try {
                $item->run();
            } catch (\Exception $e) {
                $this->app->log($e->getMessage());
            }
        }
    }

    /**
     * Add new WP Cron Schedule (Interval)
     *
     * @param array $intervals
     * @return array mixed
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
     * Remove main cron job from WP (to be used on plugin deactivation)
     */
    public function deactivate()
    {
        if ($timestamp = wp_next_scheduled($this->jobName)) {
            wp_unschedule_event($timestamp, $this->jobName);
        }
    }
}
