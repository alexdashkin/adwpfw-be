<?php

namespace AlexDashkin\Adwpfw\Modules\RestApi;

/**
 * admin-ajax.php endpoint
 */
class AdminAjax extends Request
{
    /**
     * Init Module
     */
    public function init()
    {
        $actionName = $this->prefixIt($this->getProp('action'));

        $this->addHook('wp_ajax_' . $actionName, [$this, 'handle']);
    }

    /**
     * Handle the Request
     */
    public function handle()
    {
        if (!check_ajax_referer('adwpfw', 'nonce', false)) {
            wp_send_json(parent::error('Invalid nonce'));
        }

        $this->log('Ajax request, action "%s"', [$this->getProp('action')]);

        $result = $this->execute($_REQUEST);

        wp_send_json($result);
    }

    /**
     * Get prop definitions
     *
     * @return array
     */
    protected function getPropDefs(): array
    {
        return [
            'action' => [
                'type' => 'string',
                'required' => true,
            ],
            'callback' => [
                'type' => 'callable',
                'required' => true,
            ],
            'fields' => [
                'type' => 'array',
                'default' => [],
            ],
        ];
    }
}
