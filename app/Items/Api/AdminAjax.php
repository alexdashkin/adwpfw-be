<?php

namespace AlexDashkin\Adwpfw\Items\Api;

class AdminAjax extends Request
{
    /**
     * Init Module
     */
    public function init()
    {
        $actionName = $this->get('prefix') . '_' . $this->get('action');

        $this->hook('wp_ajax_' . $actionName, [$this, 'handle']);
        $this->hook('wp_ajax_nopriv_' . $actionName, [$this, 'handle']);
    }

    /**
     * Handle the Request
     */
    public function handle()
    {
        check_ajax_referer($this->get('prefix'));

        $result = $this->execute($_REQUEST['data'] ?? []);

        wp_send_json($result);
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
            'action' => [
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
