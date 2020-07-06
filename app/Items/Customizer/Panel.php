<?php

namespace AlexDashkin\Adwpfw\Items\Customizer;

use AlexDashkin\Adwpfw\Abstracts\Module;

class Panel extends Module
{
    /**
     * @var Section[]
     */
    private $sections;

    /**
     * Add Section
     *
     * @param Section $section
     */
    public function addSection($section)
    {
        $section->set('panel', $this->get('prefix') . '_' . $this->get('id'));

        $this->sections[] = $section;
    }

    /**
     * Init Module
     */
    public function init()
    {
        $this->hook('customize_register', [$this, 'register']);
    }

    /**
     * Register Panel.
     *
     * @param \WP_Customize_Manager $customizer
     */
    public function register(\WP_Customize_Manager $customizer)
    {
        $customizer->add_panel($this->get('prefix') . '_' . $this->get('id'), $this->data);

        foreach ($this->sections as $section) {
            $section->register($customizer);
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
            'title' => [
                'required' => true,
            ],
            'id' => [
                'default' => function ($data) {
                    return sanitize_key(str_replace(' ', '_', $data['title']));
                },
            ],
            'description' => [
                'default' => '',
            ],
            'priority' => [
                'type' => 'int',
                'default' => 160,
            ],
        ];
    }
}
