<?php

namespace AlexDashkin\Adwpfw\Modules\Customizer;

use AlexDashkin\Adwpfw\Modules\Module;

/**
 * label*, section, id, type, description, priority
 */
class Setting extends Module
{
    /**
     * @var Section
     */
    private $section;

    /**
     * Set parent section
     *
     * @param Section $section
     */
    public function setSection(Section $section)
    {
        $this->section = $section;
    }

    /**
     * Register Setting
     *
     * @param \WP_Customize_Manager $customizer
     */
    public function register(\WP_Customize_Manager $customizer)
    {
        $id = $this->getProp('id');

        $control = [
            'section' => $this->section->getProp('id'),
            'type' => $this->getProp('type'),
            'label' => $this->getProp('label'),
            'description' => $this->getProp('description'),
            'priority' => $this->getProp('priority'),
            'input_attrs' => $this->getProp('input_attrs'),
        ];

        $customizer->add_setting($id, $this->getProps());

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
            'type' => [
                'type' => 'string',
                'required' => true,
            ],
            'label' => [
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
            'input_attrs' => [
                'type' => 'array',
                'default' => [],
            ],
        ];

        return array_merge($baseProps, $fieldProps);
    }
}
