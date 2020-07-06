<?php

namespace AlexDashkin\Adwpfw\Items;

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
        $data = $this->data;
        $data['id'] = $this->get('prefix') . '_' . $this->get('id');

        register_sidebar($data);
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
