<?php

namespace AlexDashkin\Adwpfw\Items;

use AlexDashkin\Adwpfw\Abstracts\Module;

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
        add_shortcode($this->get('prefix') . '_' . $this->get('tag'), [$this, 'render']);
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
        $args = array_merge($this->get('atts'), $atts ?: []);

        return $this->get('callback')($args);
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
