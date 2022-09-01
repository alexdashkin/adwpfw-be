<?php

namespace AlexDashkin\Adwpfw\Modules;

/**
 * WP Dashboard Widget
 */
class DbWidget extends Module
{
    /**
     * Init Module
     */
    public function init()
    {
        $this->addHook('wp_dashboard_setup', [$this, 'register']);
    }

    /**
     * Register Dashboard Widget
     */
    public function register()
    {
        wp_add_dashboard_widget($this->prefixIt($this->getProp('id')), $this->getProp('title'), $this->getProp('callback'));
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
            'title' => [
                'type' => 'string',
                'required' => true,
            ],
            'callback' => [
                'type' => 'callable',
                'required' => true,
            ],
            'id' => [
                'type' => 'string',
                'default' => function () {
                    return sanitize_key(str_replace(' ', '-', $this->getProp('title')));
                },
            ],
        ];

        return array_merge($baseProps, $fieldProps);
    }
}
