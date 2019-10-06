<?php

namespace AlexDashkin\Adwpfw\Modules;

/**
 * Manage settings in WP Customizer
 */
class Customizer extends Module
{
    private $panels = [];

    public function __construct($app)
    {
        parent::__construct($app);
    }

    public function run()
    {
        add_action('customize_register', [$this, 'register']);
    }

    /**
     * Add sections to the Customizer
     *
     * @param array $sections
     */
    public function add(array $sections)
    {
        $this->panels = array_merge($this->panels, $sections);
    }

    /**
     * Get a Customizer setting
     *
     * @param string $id Setting ID
     * @return mixed
     */
    public function get($id)
    {
        return get_theme_mod($this->config['prefix'] . '_' . $id);
    }

    public function register(\WP_Customize_Manager $customizer)
    {
        $prefix = $this->config['prefix'];

        foreach ($this->panels as $panel) {
            $defaults = [
                'priority' => 1000,
                'sections' => []
            ];

            $panel = array_merge($defaults, $panel);

            $panel['id'] = $prefix . '_' . $panel['id'];

            $customizer->add_panel($panel['id'], $panel);

            foreach ($panel['sections'] as $section) {
                $defaults = [
                    'panel' => $panel['id'],
//					'priority' => 1000,
                    'settings' => []
                ];

                $section = array_merge($defaults, $section);
                $section['id'] = $prefix . '_' . $section['id'];

                $customizer->add_section($section['id'], $section);

                foreach ($section['settings'] as $setting) {
                    $defaults = [
//					'setting' => $setting['id'],
                    ];
                    $setting = array_merge($defaults, $setting);

                    $setting['id'] = $prefix . '_' . $setting['id'];
                    $setting['section'] = $section['id'];

                    $customizer->add_setting($setting['id'], $setting);

                    if ('image' === $setting['control']) {
                        $setting['mime_type'] = 'image';
                        $customizer->add_control(new \WP_Customize_Media_Control($customizer, $setting['id'], $setting));
                    } elseif ('color' === $setting['control']) {
                        $customizer->add_control(new \WP_Customize_Color_Control($customizer, $setting['id'], $setting));
                    } elseif ('category_order' === $setting['control']) {
                        $customizer->add_control(new CatOrderControl($customizer, $setting['id'], $setting, $this->app));
                    } else {
                        $setting['type'] = $setting['control'];
                        $customizer->add_control($setting['id'], $setting);
                    }
                }
            }
        }
    }
}
