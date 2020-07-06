<?php

namespace AlexDashkin\Adwpfw\Items\Api;

use AlexDashkin\Adwpfw\App;

class AdminAjax extends Request
{
    /**
     * Init Module
     */
    public function init()
    {
        $actionName = $this->get('prefix') . '_' . $this->get('action');

        App::get(
            'hook',
            [
                'tag' => 'wp_ajax_' . $actionName,
                'callback' => [$this, 'handle'],
            ]
        );

        App::get(
            'hook',
            [
                'tag' => 'wp_ajax_nopriv_' . $actionName,
                'callback' => [$this, 'handle'],
            ]
        );
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
