<?php

namespace AlexDashkin\Adwpfw\Modules\Customizer;

use AlexDashkin\Adwpfw\Modules\Module;

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
        $section->sp('panel', $this->gp('prefix') . '_' . $this->gp('id'));

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
        $customizer->add_panel($this->gp('prefix') . '_' . $this->gp('id'), $this->gp());

        foreach ($this->sections as $section) {
            $section->register($customizer);
        }
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
