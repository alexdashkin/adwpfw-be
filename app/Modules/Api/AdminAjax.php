<?php

namespace AlexDashkin\Adwpfw\Modules\Api;

class AdminAjax extends Request
{
    /**
     * Init Module
     */
    public function init()
    {
        $this->validateData();

        $actionName = $this->gp('prefix') . '_' . $this->gp('action');

        $this->hook('wp_ajax_' . $actionName, [$this, 'handle']);
        $this->hook('wp_ajax_nopriv_' . $actionName, [$this, 'handle']);
    }

    /**
     * Handle the Request
     */
    public function handle()
    {
        check_ajax_referer($this->gp('prefix'));

        $this->log('Ajax request: "%s_%s"', [$this->gp('prefix'), $this->gp('action')]);

        $result = $this->execute($_REQUEST['data'] ?? []);

        wp_send_json($result);
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
