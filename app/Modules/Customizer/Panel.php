<?php

namespace AlexDashkin\Adwpfw\Modules\Customizer;

use AlexDashkin\Adwpfw\Modules\Module;

/**
 * title*, id, description, priority
 */
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
        $section->setProp('panel', $this->config('prefix') . '_' . $this->getProp('id'));

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
