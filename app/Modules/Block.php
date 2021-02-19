<?php

namespace AlexDashkin\Adwpfw\Modules;

/**
 * Gutenberg Block
 */
class Block extends Module
{
    private $frontHandles = [];
    private $frontCss = [];

    /**
     * Init Module
     */
    public function init()
    {
        // Register Block
        $this->addHook('init', [$this, 'register']);
    }

    /**
     * Register Block
     */
    public function register()
    {
        // Register block
        register_block_type(
            $this->prefix . '/' . $this->getProp('name'),
            [
                'render_callback' => [$this, 'render'],
                'supports' => $this->getProp('supports'),
            ]
        );

        // Enqueue assets
        foreach ($this->getProp('assets') as $index => $asset) {
            // Type here is CSS/JS
            $type = $asset['type'] ?? 'css';

            // Type for particular asset is block/front
            $af = $asset['af'] ?: 'block';
            $asset['type'] = $af;

            $args = [
                'id' => sprintf('%s-%s-%s-%d', $this->getProp('name'), $type, $af, $index),

                // Do not enqueue front assets (to be done in render_callback)
                'enqueue' => 'front' !== $af,
            ];

            // Add asset
            $asset = $this->m('asset.' . $type, array_merge($args, $asset));

            // Add handle to the list for front scripts to enqueue in render_callback
            if (!$args['enqueue']) {
                $this->frontHandles[$type][] = $asset->getProp('handle');
            }
        }
    }

    /**
     * Render wrapper to add front script
     *
     * @param array $atts
     * @param string $content
     * @return string
     */
    public function render(array $atts, string $content): string
    {
        // Enqueue front assets
        if (!is_admin()) {
            if (!empty($this->frontHandles['css'])) {
                foreach ($this->frontHandles['css'] as $handle) {
                    wp_enqueue_style($handle);
                }
            }

            if (!empty($this->frontHandles['js'])) {
                foreach ($this->frontHandles['js'] as $handle) {
                    wp_enqueue_script($handle);
                }
            }
        }

        // Call the callback
        $callback = $this->getProp('render_callback');

        try {
            return is_callable($callback) ? $callback($atts, $content) : $content;
        } catch (\Exception $e) {
            $this->log('Exception in block "%s": %s', [$this->getProp('name'), $e->getMessage()]);
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
            'name' => function () {
                return sanitize_key(str_replace(' ', '-', $this->getProp('title')));
            },
        ];
    }
}
