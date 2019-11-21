<?php

namespace AlexDashkin\Adwpfw\Items\Customizer;

use AlexDashkin\Adwpfw\App;
use AlexDashkin\Adwpfw\Exceptions\AdwpfwException;
use AlexDashkin\Adwpfw\Items\ItemWithItems;

/**
 * Customizer Panel
 */
class Panel extends ItemWithItems
{
    /**
     * Constructor
     *
     * @param App $app
     * @param array $data {
     * @type array $sections Sections: {
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
            'sections' => [
                'type' => 'array',
                'default' => [],
            ],
        ];

        parent::__construct($app, $data, $props);

        foreach ($this->data['sections'] as $section) {
            $this->add($section);
        }
    }

    /**
     * Add Section.
     *
     * @param App $app
     * @param array $data Data passed to the Section Constructor.
     *
     * @throws AdwpfwException
     */
    public function add(array $data)
    {
        $data['panel'] = $this->data['id'];

        $this->items[] = new Section($this->app, $data);
    }

    /**
     * Register Panel.
     */
    public function register(\WP_Customize_Manager $customizer)
    {
        $customizer->add_panel($this->data['id'], $this->data);
    }
}
