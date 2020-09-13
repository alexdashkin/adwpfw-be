<?php

namespace AlexDashkin\Adwpfw\Modules\Customizer;

use AlexDashkin\Adwpfw\Modules\Module;

/**
 * title*, panel, id, description, priority
 */
class Section extends Module
{
    /**
     * @var Setting[]
     */
    private $settings;

    /**
     * Add Setting
     *
     * @param Setting $setting
     */
    public function addSetting(Setting $setting)
    {
        $setting->setProp('section', $this->prefix . '_' . $this->getProp('id'));

        $this->settings[] = $setting;
    }

    /**
     * Register Section
     *
     * @param \WP_Customize_Manager $customizer
     */
    public function register(\WP_Customize_Manager $customizer)
    {
        $customizer->add_section($this->prefix . '_' . $this->getProp('id'), $this->getProps());

        foreach ($this->settings as $setting) {
            $setting->register($customizer);
        }
    }

    /**
     * Get Default prop values
     *
     * @return array
     */
    protected function defaults(): array
    {
        return [
            'id' => function () {
                return sanitize_key(str_replace(' ', '_', $this->getProp('title')));
            },
        ];
    }
}
