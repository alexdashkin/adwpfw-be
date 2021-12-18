<?php

namespace AlexDashkin\Adwpfw\Modules;

/**
 * WP Cron Job
 */
class CronJob extends Module
{
    /**
     * Init Module.
     * Main App runs on "init" with 0 priority.
     * Run on "init" with the default 10 priority to run the Job after the App has fully constructed.
     */
    public function init()
    {
        $this->addHook('init', [$this, 'run']);
    }

    /**
     * Run Job
     */
    public function run()
    {
        // If we are not in WP Cron - return
        if (!defined('DOING_CRON') || !DOING_CRON) {
            return;
        }

        // Current Job Name
        $jobName = $this->getProp('name');

        // Get currently running and last run params
        $running = $this->getRunning();
        $lastRun = $this->getLastRun();

        // If interval is not expired - exit
        if ($lastRun && (time() - $this->getProp('interval')) < $lastRun) {
            return;
        }

        // Run the Job
        $this->log("Launching cron job $jobName");

        // If another process is running and parallel is disabled - abort
        if ($running && !$this->getProp('parallel')) {
            $this->log('Another instance is running, aborting');
            return;
        }

        // Set Running and Last Run
        $this->setRunning();
        $this->setLastRun();

        // Try to run the job
        try {
            $this->getProp('callback')();
        } catch (\Exception $e) {
            $msg = 'Exception: ' . $e->getMessage() . '. Execution aborted.';
            $this->log($msg);
        } finally {
            $this->deleteRunning();
        }

        $this->log('Done');
    }

    /**
     * Get job last run timestamp
     *
     * @return int
     */
    private function getLastRun(): int
    {
        return (int)get_option($this->getProp('optionName'));
    }

    /**
     * Set job last run timestamp
     *
     * @return bool
     */
    private function setLastRun(): bool
    {
        return update_option($this->getProp('optionName'), time());
    }

    /**
     * Get running job start timestamp
     *
     * @return int
     */
    private function getRunning(): int
    {
        return (int)get_transient($this->getProp('optionName'));
    }

    /**
     * Set running job start timestamp
     *
     * @return int
     */
    private function setRunning(): bool
    {
        return set_transient($this->getProp('optionName'), time(), 180);
    }

    /**
     * Delete running job timestamp
     *
     * @return bool
     */
    private function deleteRunning(): bool
    {
        return delete_transient($this->getProp('optionName'));
    }

    /**
     * Get prop definitions
     *
     * @return array
     */
    protected function getPropDefs(): array
    {
        $baseProps = parent::getPropDefs();

        $fieldProps = [
            'name' => [
                'type' => 'string',
                'required' => true,
            ],
            'callback' => [
                'type' => 'callable',
                'required' => true,
            ],
            'interval' => [
                'type' => 'int',
                'required' => true,
            ],
            'optionName' => [
                'type' => 'string',
                'default' => function () {
                    return sprintf('adwpfw_cron_job_%s', sanitize_title($this->getProp('name')));
                },
            ],
        ];

        return array_merge($baseProps, $fieldProps);
    }
}
