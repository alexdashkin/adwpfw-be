<?php

namespace AlexDashkin\Adwpfw\Modules;

use AlexDashkin\Adwpfw\Abstracts\Module;
use AlexDashkin\Adwpfw\App;

class Notice extends Module
{
    /**
     * Init Module
     */
    public function init()
    {
        $this->hook('admin_notices', [$this, 'process']);

        // Add Ajax action to dismiss notice
        if ($this->gp('dismissible')) {
            App::get(
                'admin_ajax',
                [
                    'prefix' => $this->gp('prefix'),
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
        if ($this->gp('callback') && !$this->gp('callback')()) {
            return;
        }

        // If dismissed but days have not yet passed - do not show
        // To show again immediately - leave days as 0
        // To stop forever - set days as highest possible value
        if ($this->getDismissed() > time() - $this->gp('days') * DAY_IN_SECONDS) {
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
        if ($data['id'] === $this->gp('id')) {
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
    private function render()
    {
        return $this->twig($this->gp('tpl'), $this->gp());
    }

    /**
     * Get Notices option
     *
     * @param string $name Param name
     * @return int Dismissed timestamp
     */
    private function getDismissed()
    {
        $optionName = $this->gp('prefix') . '_notices';

        $option = get_option($optionName) ?: [];

        return $option[$this->gp('id')] ?? 0;
    }

    /**
     * Update Notices option
     *
     * @param string $key Param
     * @param int $timestamp Dismissed timestamp
     */
    private function setDismissed($timestamp)
    {
        $optionName = $this->gp('prefix') . '_notices';

        $optionValue = get_option($optionName) ?: [];

        $optionValue[$this->gp('id')] = $timestamp;

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
                'default' => 'notice',
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
            'class' => [
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
