<?php

namespace AlexDashkin\Adwpfw\Modules;

class CronJob extends Module
{
    /**
     * Init Module
     */
    public function init()
    {
        // Validate Props before starting
        $this->validateData();

        // If we are not in WP Cron - return
        if (!defined('DOING_CRON') || !DOING_CRON) {
            return;
        }

        // Current Job Name
        $jobName = $this->gp('name');

        // Cron option value for current job
        $option = $this->getOption($jobName);

        // Get currently running and last run params
        $running = $option['running'] ?? [];
        $lastRun = $option['last'] ?? 0;

        // If interval is not expired - exit
        if ($lastRun && (time() - $this->gp('interval')) < $lastRun) {
            return;
        }

        // Run the Job
        $this->log("Launching cron job $jobName");

        $startTime = time();

        // Remove old possible dead jobs from "running"
        foreach ($running as $index => $ts) {
            if ($ts < ($startTime + 3600)) {
                unset($running[$index]);
            }
        }

        // If another process is running and parallel is disabled - abort
        if ($running && !$this->gp('parallel')) {
            $this->log('Another instance is running, aborting');
            return;
        }

        // Add current process to "running" array
        $running[] = $startTime;

        // Update Cron Option before launching the job
        $this->updateOption($jobName, [
            'last' => $startTime,
            'running' => $running
        ]);

        // Try to run the job
        try {
            $this->gp('callback')();
        } catch (\Exception $e) {
            $msg = 'Exception: ' . $e->getMessage() . '. Execution aborted.';
            $this->log($msg);
        } finally {
            $option = $this->getOption($jobName);
            $running = $option['running'] ?? [];

            // Remove the current process from "running"
            foreach ($running as $index => $time) {
                if ($time === $startTime) {
                    unset($running[$index]);
                }
            }

            // Update Cron Option
            $this->updateOption($jobName, [
                'last' => $startTime,
                'running' => $running
            ]);
        }

        $this->log('Done');
    }

    /**
     * Get Cron option
     *
     * @param string $name Param name
     * @return array
     */
    private function getOption(string $name): array
    {
        $optionName = $this->gp('prefix') . '_cron';

        $optionValue = get_option($optionName) ?: [];

        return $optionValue[$name] ?? [];
    }

    /**
     * Update Cron option.
     *
     * @param string $name Param name
     * @param array $value Value
     */
    private function updateOption(string $name, array $value)
    {
        $optionName = $this->gp('prefix') . '_cron';

        $optionValue = get_option($optionName) ?: [];

        $optionValue[$name] = $value;

        update_option($optionName, $optionValue);
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
            'name' => [
                'required' => true,
            ],
            'callback' => [
                'type' => 'callable',
                'required' => true,
            ],
            'interval' => [
                'type' => 'int',
                'default' => 0,
            ],
            'parallel' => [
                'type' => 'bool',
                'default' => false,
            ],
        ];
    }
}
