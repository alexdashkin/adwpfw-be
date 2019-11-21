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
                'type' => 'int',
                'required' => true,
            ],
            'priority' => [
                'type' => 'int',
                'default' => 160,
            ],
            'label' => [
                'required' => true,
            ],
            'description' => [
                'default' => '',
            ],
            'choices' => [
                'type' => 'array',
                'default' => [],
            ],
            'input_attrs' => [
                'type' => 'array',
                'default' => [],
            ],
            'allow_addition' => [
                'type' => 'bool',
                'default' => false,
            ],
            'capability' => [
                'default' => 'edit_theme_options',
            ],
            'type' => [
                'default' => 'text',
            ],
            'transport' => [
                'default' => 'refresh',
            ],
            'default' => [
                'default' => '',
            ],
            'active_callback' => [
                'type' => 'callable',
                'default' => null,
            ],
            'validate_callback' => [
                'type' => 'callable',
                'default' => null,
            ],
            'sanitize_callback' => [
                'type' => 'callable',
                'default' => null,
            ],
            'sanitize_js_callback' => [
                'type' => 'callable',
                'default' => null,
            ],
            'dirty' => [
                'type' => 'bool',
                'default' => true,
            ],
        ];

        parent::__construct($app, $data, $props);
    }

    /**
     * Register Setting.
     */
    public function register(\WP_Customize_Manager $customizer)
    {
        $customizer->add_setting($this->data['id'], $this->data);
        $customizer->add_control($this->data['id'], $this->data);
    }
}
