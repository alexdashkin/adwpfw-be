<?php

namespace AlexDashkin\Adwpfw\Modules\Customizer;

use AlexDashkin\Adwpfw\Modules\Module;

/**
 * label*, section, id, type, description, priority
 */
class Setting extends Module
{
    /**
     * Register Setting
     *
     * @param \WP_Customize_Manager $customizer
     */
    public function register(\WP_Customize_Manager $customizer)
    {
        $id = $this->config('prefix') . '_' . $this->getProp('id');

        $setting = [
            'default' => $this->getProp('default'),
            'sanitize_callback' => $this->getProp('sanitize_callback'),
        ];

        $control = [
            'label' => $this->getProp('label'),
            'description' => $this->getProp('description'),
            'section' => $this->getProp('section'),
            'priority' => $this->getProp('priority'),
            'type' => $this->getProp('type'),
            'input_attrs' => $this->getProp('input_attrs'),
        ];

        $customizer->add_setting($id, $setting);

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
     * Get Default Prop value
     *
     * @param string $key
     * @return mixed
     */
    protected function getDefault(string $key)
    {
        switch ($key) {
            case 'id':
                return sanitize_key(str_replace(' ', '_', $this->getProp('label')));
            case 'type':
                return 'text';
        }

        return null;
    }
}
