<?php

namespace AlexDashkin\Adwpfw\Items;

use AlexDashkin\Adwpfw\App;

/**
 * Cron Job
 */
class CronJob extends Item
{
    /**
     * Constructor
     *
     * @param array $data {
     * @type string $name Job Name. Required.
     * @type callable $callback Handler. Required.
     * @type int $id Interval in seconds
     * @type bool $parallel Allow parallel execution
     * @type array $args Args to be passed to the handler
     * }
     */
    public function __construct(array $data, App $app)
    {
        $this->props = [
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
            'args' => [
                'type' => 'array',
                'default' => [],
            ],
        ];

        parent::__construct($data, $app);
    }

    /**
     * Run the Job
     */
    public function run()
    {
        $prefix = $this->config['prefix'];

        $jobName = $this->data['name'];

        $optionName = $prefix . '_cron';

        $optionValue = get_option($optionName) ?: [];

        $running = !empty($optionValue[$jobName]['running']) ? $optionValue[$jobName]['running'] : [];

        $lastRun = !empty($optionValue[$jobName]['last']) ? (int)$optionValue[$jobName]['last'] : 0;

        $data = $this->data;

        if (!$lastRun || (time() - $data['interval']) > $lastRun) {
            $this->log("Launching cron job $jobName");

            if ($running && !$data['parallel']) {
                $this->log('Another instance is running, aborting');
                return;
            }

            $started = time();

            $running[] = $started;

            $opt = [
                'last' => $lastRun,
                'running' => $running
            ];

            $this->updateOption($jobName, $opt);

            try {
                call_user_func($data['callback'], $data['args']);

            } catch (\Exception $e) {
                $msg = 'Exception: ' . $e->getMessage() . '. Execution aborted.';
                $this->log($msg);

            } finally {
                $optionValue = get_option($optionName) ?: [];
                $running = !empty($optionValue[$jobName]['running']) ? $optionValue[$jobName]['running'] : [];

                foreach ($running as $index => $time) {
                    if ($time === $started) {
                        unset($running[$index]);
                    }
                }

                $opt = [
                    'last' => time(),
                    'running' => $running
                ];

                $this->updateOption($jobName, $opt);
            }

            $this->log('Done');
        }
    }

    private function updateOption($name, $value)
    {
        $optionName = $this->config['prefix'] . '_cron';

        $optionValue = get_option($optionName) ?: [];

        $optionValue[$name] = $value;

        update_option($optionName, $optionValue);
    }
}
