<?php

namespace AlexDashkin\Adwpfw\Items\Customizer;

use AlexDashkin\Adwpfw\App;
use AlexDashkin\Adwpfw\Exceptions\AdwpfwException;
use AlexDashkin\Adwpfw\Items\ItemWithItems;

/**
 * Customizer Section.
 */
class Section extends ItemWithItems
{
    /**
     * @var Setting[]
     */
    protected $items = [];

    /**
     * Constructor
     *
     * @param App $app
     * @param array $data {
     * @type string $id Default sanitized title.
     * @type string $panel Panel ID. Required.
     * @type string $title Required.
     * @type string $description Default empty.
     * @type int $priority Default 160.
     * @type array $settings
     * }
     *
     * @throws AdwpfwException
     */
    public function __construct(App $app, array $data)
    {
        $props = [
            'id' => [
                'default' => sanitize_key(str_replace(' ', '_', $data['title'])),
            ],
            'panel' => [
                'required' => true,
            ],
            'title' => [
                'required' => true,
            ],
            'description' => [
                'default' => '',
            ],
            'priority' => [
                'type' => 'int',
                'default' => 160,
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
     * @param array $data Data passed to the Setting Constructor.
     *
     * @throws AdwpfwException
     */
    public function add(array $data)
    {
        $data['section'] = $this->prefix . '_' . $this->data['id'];

        $this->items[] = new Setting($this->app, $data);
    }

    /**
     * Register Section.
     *
     * @param \WP_Customize_Manager $customizer
     */
    public function register(\WP_Customize_Manager $customizer)
    {
        $customizer->add_section($this->prefix . '_' . $this->data['id'], $this->data);

        foreach ($this->items as $setting) {
            $setting->register($customizer);
        }
    }
}
