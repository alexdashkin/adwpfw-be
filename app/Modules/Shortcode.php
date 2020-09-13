<?php

namespace AlexDashkin\Adwpfw\Modules;

/**
 * tag*, callback*, atts
 */
class Shortcode extends Module
{
    /**
     * Init Module
     */
    public function init()
    {
        $this->addHook('init', [$this, 'register']);
    }

    /**
     * Register the Shortcode
     */
    public function register()
    {
        add_shortcode($this->prefix . '_' . $this->getProp('tag'), [$this, 'render']);
    }

    /**
     * Render the Shortcode
     *
     * @param array|string $atts
     * @param string $content
     * @param string $tag
     * @return string
     */
    public function render($atts, string $content, string $tag): string
    {
        $args = array_merge($this->getProp('atts') ?: [], $atts ?: []);

        return $this->getProp('callback')($args);
    }
}
