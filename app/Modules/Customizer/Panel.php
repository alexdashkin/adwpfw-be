<?php

namespace AlexDashkin\Adwpfw\Modules\Customizer;

use AlexDashkin\Adwpfw\Modules\Module;

/**
 * Customizer panel
 */
class Panel extends Module
{
    /**
     * @var Section[]
     */
    private $sections;

    /**
     * Init Module
     */
    public function init()
    {
        $this->addHook('customize_register', [$this, 'register']);
    }

    /**
     * Add Section
     *
     * @param Section $section
     */
    public function addSection(Section $section)
    {
        $section->setPanel($this);

        $this->sections[] = $section;
    }

    /**
     * Register Panel
     *
     * @param \WP_Customize_Manager $customizer
     */
    public function register(\WP_Customize_Manager $customizer)
    {
        $customizer->add_panel($this->prefixIt($this->getProp('id')), $this->getProps());

        foreach ($this->sections as $section) {
            $section->register($customizer);
        }
    }

    /**
     * Get prop definitions
     *
     * @return array
     */
    protected function getPropDefs(): array
    {
        $baseProps = parent::getPropDefs();

        $fieldProps = [
            'id' => [
                'type' => 'string',
                'required' => true,
            ],
            'title' => [
                'type' => 'string',
                'required' => true,
            ],
            'description' => [
                'type' => 'string',
                'default' => '',
            ],
            'priority' => [
                'type' => 'int',
                'default' => 160,
            ],
        ];

        return array_merge($baseProps, $fieldProps);
    }
}
