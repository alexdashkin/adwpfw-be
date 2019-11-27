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
     * @var Setting[]
     */
    protected $items = [];

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
     * @param App $app
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
     */
    public function register(\WP_Customize_Manager $customizer)
    {
        $customizer->add_section($this->prefix . '_' . $this->data['id'], $this->data);

        foreach ($this->items as $setting) {
            $setting->register($customizer);
        }
    }

    /**
     * Get default Section ID.
     * Not working with uniqid() as on subsequent calls it's different
     * and the Panel disappears from Customizer on page load
     *
     * @param string $base
     * @return string
     */
    protected function getDefaultId($base)
    {
        return esc_attr(sanitize_key(str_replace(' ', '_', $base)));
    }
}
