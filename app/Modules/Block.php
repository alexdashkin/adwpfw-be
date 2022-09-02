<?php

namespace AlexDashkin\Adwpfw\Modules;

use AlexDashkin\Adwpfw\Modules\{Assets\Css, Assets\Js};

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
        register_block_type($this->getProp('name'), $this->getProps());

        // Enqueue assets
        foreach ($this->getProp('assets') as $index => $asset) {
            // Type here is CSS/JS
            $type = $asset['type'] ?? 'css';

            // Type for particular asset is block/front
            $af = $asset['af'] ?: 'block';
            $handle = sprintf('%s-%s-%s-%d', $this->getProp('name'), $type, $af, $index);
            $asset['type'] = $af;
            $enqueue = 'front' !== $af;

            $args = [
                'handle' => $handle,

                // Do not enqueue front assets (to be done in render_callback)
                'enqueue' => $enqueue ? '__return_true' : '__return_false',
            ];

            // Add asset
            $assetProps = array_merge($args, $asset);

            switch ($type) {
                case 'css':
                    new Css($assetProps, $this->app);
                    break;
                case 'js':
                    new Js($assetProps, $this->app);
                    break;
            }

            // Add handle to the list for front scripts to enqueue in render_callback
            if (!$enqueue) {
                $this->frontHandles[$type][] = $handle;
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
            'title' => [
                'type' => 'string',
                'required' => true,
            ],
            'supports' => [
                'type' => 'array',
                'default' => [],
            ],
            'assets' => [
                'type' => 'array',
                'default' => [],
            ],
            'render_callback' => [
                'type' => 'callable',
            ],
        ];

        return array_merge($baseProps, $fieldProps);
    }
}
