<?php

namespace AlexDashkin\Adwpfw\Modules;

class Notice extends Module
{
    /**
     * Init Module
     */
    public function init()
    {
        $this->addHook('admin_notices', [$this, 'process']);

        // Add Ajax action to dismiss notice
        if ($this->getProp('dismissible')) {
            $this->m(
                'admin_ajax',
                [
                    'prefix' => $this->config('prefix'),
                    'action' => 'notice_dismiss',
                    'fields' => [
                        'id' => [
                            'type' => 'text',
                            'required' => true,
                        ],
                    ],
                    'callback' => [$this, 'ajaxDismiss'],
                ]
            );
        }
    }

    /**
     * Process the Notice
     */
    public function process()
    {
        // Do not show if callback returns false
        if ($this->getProp('callback') && !$this->getProp('callback')()) {
            return;
        }

        // If dismissed but days have not yet passed - do not show
        // To show again immediately - leave days as 0
        // To stop forever - set days as highest possible value
        if ($this->getDismissed() > time() - $this->getProp('days') * DAY_IN_SECONDS) {
            return;
        }

        // Show notice
        echo $this->render();
    }

    /**
     * Ajax dismiss handler
     *
     * @param array $data
     * @return array
     */
    public function ajaxDismiss(array $data): array
    {
        if ($data['id'] === $this->getProp('id')) {
            $this->dismiss();
        }

        return ['success' => true];
    }

    /**
     * Show the Notice (clear dismiss date)
     */
    public function show()
    {
        $this->setDismissed(0);
    }

    /**
     * Stop Showing the Notice (dismiss in future)
     */
    public function stop()
    {
        $this->setDismissed(PHP_INT_MAX);
    }

    /**
     * Dismiss now
     */
    public function dismiss()
    {
        $this->setDismissed(time());
    }

    /**
     * Render the Notice
     *
     * @return string
     */
    private function render(): string
    {
        $args = $this->getProp('args');

        $args['id'] = $this->getProp('id');

        $isDismissible = $this->getProp('dismissible') ? 'is-dismissible' : '';

        $args['classes'] = sprintf('notice notice-%s notice-%s %s %s', $this->getProp('type'), $this->config('prefix'), $isDismissible, $this->getProp('classes'));

        return $this->twig($this->getProp('tpl'), $args);
    }

    /**
     * Get Notices option
     *
     * @return int Dismissed timestamp
     */
    private function getDismissed(): int
    {
        $optionName = $this->config('prefix') . '_notices';

        $option = get_option($optionName) ?: [];

        return $option[$this->getProp('id')] ?? 0;
    }

    /**
     * Update Notices option
     *
     * @param int $timestamp Dismissed timestamp
     */
    private function setDismissed(int $timestamp)
    {
        $optionName = $this->config('prefix') . '_notices';

        $optionValue = get_option($optionName) ?: [];

        $optionValue[$this->getProp('id')] = $timestamp;

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
            'id' => [
                'required' => true,
            ],
            'tpl' => [
                'default' => function ($data) {
                    return 'notices/' . $data['id'];
                },
            ],
            'content' => [
                'default' => '',
            ],
            'type' => [
                'default' => 'success'
            ],
            'dismissible' => [
                'type' => 'bool',
                'default' => true,
            ],
            'days' => [
                'type' => 'int',
                'default' => 0,
            ],
            'classes' => [
                'default' => '',
            ],
            'args' => [
                'type' => 'array',
                'default' => [],
            ],
            'callback' => [
                'type' => 'callable',
                'default' => null,
            ],
        ];
    }
}
