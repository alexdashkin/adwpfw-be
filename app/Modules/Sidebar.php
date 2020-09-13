<?php

namespace AlexDashkin\Adwpfw\Modules;

/**
 * name*, id, description, class
 */
class Sidebar extends Module
{
    /**
     * Init Module
     */
    public function init()
    {
        $this->addHook('widgets_init', [$this, 'register']);
    }

    /**
     * Register Sidebar
     */
    public function register()
    {
        $this->setProp('id', $this->config('prefix') . '_' . $this->getProp('id'));

        register_sidebar($this->getProps());
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
                return sanitize_key(str_replace(' ', '_', $this->getProp('name')));
        }

        return null;
    }
}
