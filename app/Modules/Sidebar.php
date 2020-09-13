<?php

namespace AlexDashkin\Adwpfw\Modules;

/**
 * name*, id
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
        $this->setProp('id', $this->prefix . '_' . $this->getProp('id'));

        register_sidebar($this->getProps());
    }

    /**
     * Get Default prop values
     *
     * @return array
     */
    protected function defaults(): array
    {
        return [
            'name' => 'Sidebar',
            'id' => function () {
                return sanitize_key(str_replace(' ', '_', $this->getProp('name')));
            },
        ];
    }
}
