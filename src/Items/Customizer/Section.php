<?php

namespace AlexDashkin\Adwpfw\Items\Customizer;

use AlexDashkin\Adwpfw\App;
use AlexDashkin\Adwpfw\Exceptions\AdwpfwException;
use AlexDashkin\Adwpfw\Items\ItemWithItems;

/**
 * Customizer Panel
 */
class Section extends ItemWithItems
{
    /**
     * Constructor
     *
     * @param App $app
     * @param array $data {
     * @type array $settings Settings: {
     * }
     *
     * @throws AdwpfwException
     */
    public function __construct(App $app, array $data)
    {
        $props = [
            'id' => [
                'default' => $this->getDefaultId($data['title']),
            ],
            'panel' => [
                'type' => 'int',
                'required' => true,
            ],
            'title' => [
                'required' => true,
            ],
            'priority' => [
                'type' => 'int',
                'default' => 160,
            ],
            'description' => [
                'default' => '',
            ],
            'capability' => [
                'default' => 'edit_theme_options',
            ],
            'type' => [
                'default' => 'default',
            ],
            'active_callback' => [
                'type' => 'callable',
                'default' => null,
            ],
            'settings' => [
                'type' => 'array',
                'default' => [],
            ],
        ];

        parent::__construct($app, $data, $props);

        foreach ($this->data['settings'] as $setting) {
            $this->add($setting);
        }
    }

    /**
     * Add Setting.
     *
     * @param App $app
     * @param array $data Data passed to the Setting Constructor.
     *
     * @throws AdwpfwException
     */
    public function add(array $data)
    {
        $data['section'] = $this->data['id'];

        $this->items[] = new Setting($this->app, $data);
    }

    /**
     * Register Section.
     */
    public function register(\WP_Customize_Manager $customizer)
    {
        $customizer->add_section($this->data['id'], $this->data);
    }
}
