<?php

namespace AlexDashkin\Adwpfw\Modules;

/**
 * id*, content*, type, dismissible, days, classes, args, show_callback
 */
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
                'api.ajax',
                [
                    'prefix' => $this->prefix,
                    'action' => 'dismiss_notice_' . $this->getProp('id'),
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
        if ($this->getProp('callback') && !$this->getProp('callback')($this)) {
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
     * @return array
     */
    public function ajaxDismiss(): array
    {
        $this->dismiss();

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

        $args['classes'] = sprintf('notice notice-%s %s adwpfw-notice %s-notice %s', $this->getProp('type'), $isDismissible, $this->prefix, $this->getProp('classes'));

        return $this->app->main->render(__DIR__ . '/../../tpl/notice.php', $args);
    }

    /**
     * Get Notices option
     *
     * @return int Dismissed timestamp
     */
    private function getDismissed(): int
    {
        $optionName = $this->prefix . '_notices';

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
        $optionName = $this->prefix . '_notices';

        $optionValue = get_option($optionName) ?: [];

        $optionValue[$this->getProp('id')] = $timestamp;

        update_option($optionName, $optionValue);
    }

    /**
     * Get Default prop values
     *
     * @return array
     */
    protected function defaults(): array
    {
        return [
            'type' => 'success',
            'args' => [],
        ];
    }
}
