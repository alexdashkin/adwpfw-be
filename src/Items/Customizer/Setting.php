<?php

namespace AlexDashkin\Adwpfw\Items\Customizer;

use AlexDashkin\Adwpfw\App;
use AlexDashkin\Adwpfw\Exceptions\AdwpfwException;
use AlexDashkin\Adwpfw\Items\Item;

/**
 * Customizer Field
 */
class Setting extends Item
{
    /**
     * Constructor
     *
     * @param App $app
     * @param array $data {
     * @type array $fields Fields: {
     * }
     *
     * @throws AdwpfwException
     */
    public function __construct(App $app, array $data)
    {
        $props = [
            'id' => [
                'default' => $this->getDefaultId($data['label']),
            ],
            'section' => [
                'required' => true,
            ],
            'priority' => [
                'type' => 'int',
                'default' => 10,
            ],
            'label' => [
                'required' => true,
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

        parent::__construct($app, $data, $props);
    }

    /**
     * Register Setting.
     */
    public function register(\WP_Customize_Manager $customizer)
    {
        $data = $this->data;

        $id = $this->prefix . '_' . $data['id'];

        $setting = [
            'default' => $data['default'],
            'sanitize_callback' => $data['sanitize_callback'],
        ];

        $control = [
            'label' => $data['label'],
            'description' => $data['description'],
            'section' => $data['section'],
            'priority' => $data['priority'],
            'type' => $data['type'],
            'input_attrs' => $data['input_attrs'],
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

    protected function getDefaultId($base)
    {
        return esc_attr(sanitize_key(str_replace(' ', '_', $base)));
    }
}
