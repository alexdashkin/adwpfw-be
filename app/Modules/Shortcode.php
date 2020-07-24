<?php

namespace AlexDashkin\Adwpfw\Modules;

class Shortcode extends Module
{
    /**
     * Init Module
     */
    public function init()
    {
        $this->hook('init', [$this, 'register']);
    }

    /**
     * Register the Shortcode
     */
    public function register()
    {
        add_shortcode($this->gp('prefix') . '_' . $this->gp('tag'), [$this, 'render']);
    }

    /**
     * Render the Shortcode
     *
     * @param array|string $atts
     * @param string $content
     * @param string $tag
     * @return string
     */
    public function render($atts, $content, $tag): string
    {
        $args = array_merge($this->gp('atts'), $atts ?: []);

        return $this->gp('callback')($args);
    }

    /**
     * Get Class props
     *
     * @return array
     */
    protected function getInitialPropDefs(): array
    {
        return [
            'prefix' => [
                'required' => true,
            ],
            'tag' => [
                'required' => true,
            ],
            'callback' => [
                'type' => 'callable',
                'required' => true,
            ],
            'atts' => [
                'type' => 'array',
                'default' => [],
            ],
        ];
    }
}
