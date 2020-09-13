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
        $setting->setProp('section', $this->config('prefix') . '_' . $this->getProp('id'));

        $this->settings[] = $setting;
    }

    /**
     * Register Section
     *
     * @param \WP_Customize_Manager $customizer
     */
    public function register(\WP_Customize_Manager $customizer)
    {
        $customizer->add_section($this->config('prefix') . '_' . $this->getProp('id'), $this->getProps());

        foreach ($this->settings as $setting) {
            $setting->register($customizer);
        }
    }

    /**
     * Get Default Prop value
     *
     * @param string $key
     * @return mixed
     */
    protected function getDefault(string $key)
    {
        switch ($key) {
            case 'id':
                return sanitize_key(str_replace(' ', '_', $this->getProp('title')));
        }

        return null;
    }
}
