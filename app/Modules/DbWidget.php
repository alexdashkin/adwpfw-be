<?php

namespace AlexDashkin\Adwpfw\Modules;

class DbWidget extends Module
{
    /**
     * Init Module
     */
    public function init()
    {
        $this->hook('wp_dashboard_setup', [$this, 'register']);
    }

    /**
     * Register Dashboard Widget
     */
    public function register()
    {
        if (!current_user_can($this->gp('capability'))) {
            return;
        }

        wp_add_dashboard_widget($this->gp('id'), $this->gp('title'), $this->gp('callback'));
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
            'title' => [
                'required' => true,
            ],
            'callback' => [
                'type' => 'callable',
                'required' => true,
            ],
            'id' => [
                'default' => function ($data) {
                    return sanitize_key(str_replace(' ', '-', $data['title']));
                },
            ],
            'capability' => [
                'default' => 'read',
            ],
        ];
    }
}
