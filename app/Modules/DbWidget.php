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
     * Get Default Prop value
     *
     * @param string $key
     * @return mixed
     */
    protected function getDefault(string $key)
    {
        switch ($key) {
            case 'id':
                return sanitize_key(str_replace(' ', '-', $this->getProp('title')));
            case 'capability':
                return 'read';
        }

        return null;
    }
}
