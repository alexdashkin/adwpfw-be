<?php

namespace AlexDashkin\Adwpfw\Modules;

/**
 * name*, id
 */
class Sidebar extends Module
{
    /**
     * Init Module
     */
    public function init()
    {
        $this->addHook('widgets_init', [$this, 'register']);
    }

    /**
     * Register Sidebar
     */
    public function register()
    {
        register_sidebar($this->getProps());
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
            'name' => [
                'type' => 'string',
                'required' => true,
            ],
            'id' => [
                'type' => 'string',
                'default' => function () {
                    return sanitize_key(str_replace(' ', '_', $this->getProp('name')));
                },
            ],
        ];

        return array_merge($baseProps, $fieldProps);
    }
}
