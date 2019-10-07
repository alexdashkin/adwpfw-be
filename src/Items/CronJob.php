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
     * Add hooks
     */
    protected function hooks()
    {
    }

    public function register()
    {
    }
}
