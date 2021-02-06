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

        // Enqueue shortcode assets
        foreach ($assets as $index => $asset) {
            // Type here is CSS/JS
            $type = $asset['type'] ?? 'css';

            // Type for particular asset is admin/front
            $asset['type'] = 'front';

            $args = [
                'id' => sprintf('%s-%d', $tag, $index),
                'callback' => function () use ($tag) {
                    $post = get_queried_object();
                    return $post instanceof \WP_Post && has_shortcode($post->post_content, $tag);
                },
            ];

            $this->m('asset.' . $type, array_merge($args, $asset));
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

        try {
            return $this->getProp('callback')($args);
        } catch (\Exception $e) {
            $this->log('Exception in shortcode "%s": %s', [$this->getProp('tag'), $e->getMessage()]);
            return '';
        }
    }

    /**
     * Get Default prop values
     *
     * @return array
     */
    protected function defaults(): array
    {
        return [
            'assets' => [],
        ];
    }
}
