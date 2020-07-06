<?php

namespace AlexDashkin\Adwpfw\Items\Customizer;

use AlexDashkin\Adwpfw\Abstracts\Module;

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
    public function addSetting($setting)
    {
        $setting->set('section', $this->get('prefix') . '_' . $this->get('id'));

        $this->settings[] = $setting;
    }

    /**
     * Register Section
     *
     * @param \WP_Customize_Manager $customizer
     */
    public function register(\WP_Customize_Manager $customizer)
    {
        $customizer->add_section($this->get('prefix') . '_' . $this->get('id'), $this->data);

        foreach ($this->settings as $setting) {
            $setting->register($customizer);
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
            'panel' => [
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
