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
    public function addSection(Section $section)
    {
        $section->sp('panel', $this->config('prefix') . '_' . $this->getProp('id'));

        $this->sections[] = $section;
    }

    /**
     * Init Module
     */
    public function init()
    {
        $this->addHook('customize_register', [$this, 'register']);
    }

    /**
     * Register Panel.
     *
     * @param \WP_Customize_Manager $customizer
     */
    public function register(\WP_Customize_Manager $customizer)
    {
        $customizer->add_panel($this->config('prefix') . '_' . $this->getProp('id'), $this->getProps());

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
