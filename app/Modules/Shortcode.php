<?php

namespace AlexDashkin\Adwpfw\Modules;

/**
 * tag*, callback*, atts, assets[]
 */
class Shortcode extends Module
{
    /**
     * Init Module
     */
    public function init()
    {
        $this->addHook('template_redirect', [$this, 'register']);
    }

    /**
     * Register the Shortcode
     */
    public function register()
    {
        // Add shortcode
        $tag = $this->prefix . '_' . $this->getProp('tag');
        add_shortcode($tag, [$this, 'render']);

        // If no associated assets - return
        if (!$assets = $this->getProp('assets')) {
            return;
        }

        // Get current post and ensure it's a post
        $post = get_queried_object();
        if (!$post instanceof \WP_Post) {
            return;
        }

        // If our shortcode is not used - do nothing
        if (!has_shortcode($post->post_content, $tag)) {
            return;
        }

        // Enqueue shortcode assets
        foreach ($assets as $asset) {
            $this->m('asset.' . $asset['type'], array_merge(['type' => 'front'], $asset));
        }
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
