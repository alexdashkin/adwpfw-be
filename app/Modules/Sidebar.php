<?php

namespace AlexDashkin\Adwpfw\Modules;

use AlexDashkin\Adwpfw\Abstracts\Module;

class Sidebar extends Module
{
    /**
     * Init Module
     */
    public function init()
    {
        $this->hook('widgets_init', [$this, 'register']);
    }

    /**
     * Register Sidebar
     */
    public function register()
    {
        $this->sp('id', $this->gp('prefix') . '_' . $this->gp('id'));

        register_sidebar($this->gp());
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
            'name' => [
                'required' => true,
            ],
            'id' => [
                'default' => function ($data) {
                    return sanitize_key(str_replace(' ', '_', $data['name']));
                },
            ],
            'description' => [
                'default' => null,
            ],
            'class' => [
                'default' => null,
            ],
        ];
    }
}
