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

    public function run()
    {
        $prefix = $this->config['prefix'];

        $optionName = $prefix . '_cron';

        $option = get_option($optionName) ?: [];

        $data = $this->data;

        $jobName = $this->data['name'];

        $lastRun = !empty($option[$jobName]['started']) ? (int)$option[$jobName]['started'] : 0;

        if (!$lastRun || (time() - $this->data['interval']) > $lastRun) {
            $this->log("Launching cron job $jobName");

            $running = !empty($option[$jobName]['started']) && empty($option[$jobName]['finished']);

            if (!$data['parallel'] && $running) {
                $this->log('Another instance is running, aborting');
                return;
            }

            $option[$jobName] = [
                'started' => time(),
                'finished' => 0,
            ];

            update_option($optionName, $option);

            try {
                call_user_func($data['callback'], $data['args']);
            } catch (\Exception $e) {
                $msg = 'Exception: ' . $e->getMessage() . '. Execution aborted.';
                $this->log($msg);
            } finally {
                file_put_contents($path, 0);
            }

            $this->option[$jobName] = time();

            $this->log('Done');
        }
    }
}
