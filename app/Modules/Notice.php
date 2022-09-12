<?php

namespace AlexDashkin\Adwpfw\Modules;

use AlexDashkin\Adwpfw\Modules\RestApi\AdminAjax;

/**
 * WP Admin Notice
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
            new AdminAjax([
                'action' => 'dismiss_notice_' . $this->getProp('id'),
                'callback' => [$this, 'ajaxDismiss'],
            ], $this->app);
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
        $this->setDismissed();

        return ['success' => true];
    }

    /**
     * Render the Notice
     *
     * @return string
     */
    private function render(): string
    {
        $isDismissible = $this->getProp('dismissible') ? 'is-dismissible' : '';

        $args = [
            'id' => $this->getProp('id'),
            'content' => $this->getProp('content'),
            'classes' => sprintf('notice notice-%s %s adwpfw-notice %s', $this->getProp('type'), $isDismissible, $this->getProp('classes')),
        ];

        return $this->app->render('notice', $args);
    }

    /**
     * Get dismissed timestamp
     *
     * @return int Dismissed timestamp
     */
    private function getDismissed(): int
    {
        return (int)$this->getTransient($this->getProp('optionName'));
    }

    /**
     * Set dismissed timestamp
     *
     * @return bool
     */
    private function setDismissed(): bool
    {
        return $this->setTransient($this->getProp('optionName'), time(), $this->getProp('days') * DAY_IN_SECONDS);
    }

    /**
     * Delete dismissed timestamp
     *
     * @return bool
     */
    private function deleteDismissed(): bool
    {
        return $this->deleteTransient($this->getProp('optionName'));
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
            'id' => [
                'type' => 'string',
                'required' => true,
            ],
            'type' => [
                'type' => 'string',
                'default' => 'success',
            ],
            'content' => [
                'type' => 'string',
                'required' => true,
            ],
            'classes' => [
                'type' => 'string',
                'default' => '',
            ],
            'dismissible' => [
                'type' => 'bool',
                'default' => false,
            ],
            'days' => [
                'type' => 'int',
                'default' => 0,
            ],
            'callback' => [
                'type' => 'callable',
            ],
            'optionName' => [
                'type' => 'string',
                'default' => function () {
                    return sprintf('notice_%s', sanitize_title($this->getProp('name')));
                },
            ],
        ];

        return array_merge($baseProps, $fieldProps);
    }
}
