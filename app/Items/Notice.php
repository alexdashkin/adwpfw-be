<?php

namespace AlexDashkin\Adwpfw\Items;

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
        if ($this->get('dismissible')) {
            App::get(
                'admin_ajax',
                [
                    'prefix' => $this->get('prefix'),
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
        if ($this->get('callback') && !$this->get('callback')()) {
            return;
        }

        // If dismissed but days have not yet passed - do not show
        // To show again immediately - leave days as 0
        // To stop forever - set days as highest possible value
        if ($this->getDismissed() > time() - $this->get('days') * DAY_IN_SECONDS) {
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
        if ($data['id'] === $this->get('id')) {
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
        return $this->twig($this->get('tpl'), $this->data);
    }

    /**
     * Get Notices option
     *
     * @param string $name Param name
     * @return int Dismissed timestamp
     */
    private function getDismissed()
    {
        $optionName = $this->get('prefix') . '_notices';

        $option = get_option($optionName) ?: [];

        return $option[$this->get('id')] ?? 0;
    }

    /**
     * Update Notices option
     *
     * @param string $key Param
     * @param int $timestamp Dismissed timestamp
     */
    private function setDismissed($timestamp)
    {
        $optionName = $this->get('prefix') . '_notices';

        $optionValue = get_option($optionName) ?: [];

        $optionValue[$this->get('id')] = $timestamp;

        update_option($optionName, $optionValue);
    }

    /**
     * Get Class props
     *
     * @return array
     */
    protected function props(): array
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
