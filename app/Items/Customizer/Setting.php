<?php

namespace AlexDashkin\Adwpfw\Items\Customizer;

use AlexDashkin\Adwpfw\Abstracts\Module;

class Setting extends Module
{
    /**
     * Register Setting
     *
     * @param \WP_Customize_Manager $customizer
     */
    public function register(\WP_Customize_Manager $customizer)
    {
        $id = $this->get('prefix') . '_' . $this->get('id');

        $setting = [
            'default' => $this->get('default'),
            'sanitize_callback' => $this->get('sanitize_callback'),
        ];

        $control = [
            'label' => $this->get('label'),
            'description' => $this->get('description'),
            'section' => $this->get('section'),
            'priority' => $this->get('priority'),
            'type' => $this->get('type'),
            'input_attrs' => $this->get('input_attrs'),
        ];

        $customizer->add_setting($id, $setting);

        switch ($control['type']) {
            case 'image':
                unset($control['type']);
                $control['mime_type'] = 'image';
                $customizer->add_control(new \WP_Customize_Media_Control($customizer, $id, $control));
                break;

            case 'color':
                $customizer->add_control(new \WP_Customize_Color_Control($customizer, $id, $control));
                break;

            default:
                $customizer->add_control($id, $control);
        }
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
            'section' => [
                'required' => true,
            ],
            'label' => [
                'required' => true,
            ],
            'id' => [
                'default' => function ($data) {
                    return sanitize_key(str_replace(' ', '_', $data['label']));
                },
            ],
            'priority' => [
                'type' => 'int',
                'default' => 10,
            ],
            'type' => [
                'default' => 'text',
            ],
            'description' => [
                'default' => '',
            ],
            'input_attrs' => [
                'type' => 'array',
                'default' => [],
            ],
            'default' => [
                'default' => '',
            ],
            'sanitize_callback' => [
                'type' => 'callable',
                'default' => null,
            ],
        ];
    }
}
