<?php

namespace AlexDashkin\Adwpfw\Modules;

/**
 * Gutenberg Block
 */
class Block extends Module
{
    private $frontHandles = [];

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
                'id' => sprintf('%s-%d', $this->getProp('name'), $index),

                // Do not enqueue front JS (to be done in render_callback)
                'enqueue' => !('js' === $type && 'front' === $af),
            ];

            // Add asset
            $asset = $this->m('asset.' . $type, array_merge($args, $asset));

            // Add handle to the list for front scripts to enqueue in render_callback
            if ('front' === $af) {
                $this->frontHandles[] = $asset->getProp('handle');
            }
        }
    }

    /**
     * Render wrapper to add front script
     *
     * @param array $atts
     * @return string
     */
    public function render(array $atts, string $content): string
    {
        // Enqueue front scripts
        foreach ($this->frontHandles as $handle) {
            wp_enqueue_script($handle);
        }

        // Call the callback
        $callback = $this->getProp('render_callback');

        return is_callable($callback) ? $callback($atts, $content) : $content;
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
