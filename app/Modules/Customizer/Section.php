<?php

namespace AlexDashkin\Adwpfw\Modules\Customizer;

use AlexDashkin\Adwpfw\Modules\Module;

/**
 * title*, panel, id, description, priority
 */
class Section extends Module
{
    /**
     * @var Panel
     */
    private $panel;

    /**
     * @var Setting[]
     */
    private $settings;

    /**
     * Set parent panel
     *
     * @param Panel $panel
     */
    public function setPanel(Panel $panel)
    {
        $this->panel = $panel;
    }

    /**
     * Add Setting
     *
     * @param Setting $setting
     */
    public function addSetting(Setting $setting)
    {
        $setting->setSection($this);

        $this->settings[] = $setting;
    }

    /**
     * Register Section
     *
     * @param \WP_Customize_Manager $customizer
     */
    public function register(\WP_Customize_Manager $customizer)
    {
        $this->setProp('panel', $this->prefixIt($this->panel->getProp('id')));

        $customizer->add_section($this->prefixIt($this->getProp('id')), $this->getProps());

        foreach ($this->settings as $setting) {
            $setting->register($customizer);
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
