<?php

namespace AlexDashkin\Adwpfw\Modules;

/**
 * title*, callback*, id, capability
 */
class DbWidget extends Module
{
    /**
     * Init Module
     */
    public function init()
    {
        $this->addHook('wp_dashboard_setup', [$this, 'register']);
    }

    /**
     * Register Dashboard Widget
     */
    public function register()
    {
        if (!current_user_can($this->getProp('capability'))) {
            return;
        }

        wp_add_dashboard_widget($this->getProp('id'), $this->getProp('title'), $this->getProp('callback'));
    }

    /**
     * Get Default prop values
     *
     * @return array
     */
    protected function defaults(): array
    {
        return [
            'title' => 'Dashboard Widget',
            'id' => function () {
                return sanitize_key(str_replace(' ', '-', $this->getProp('title')));
            },
            'capability' => 'read',
        ];
    }
}
