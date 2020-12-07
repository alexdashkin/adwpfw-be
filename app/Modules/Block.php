<?php

namespace AlexDashkin\Adwpfw\Modules;

/**
 * Gutenberg Block
 */
class Block extends Module
{
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
        // Register Block Assets
        $this->registerAssets();

        // Register block
        register_block_type(
            $this->prefix . '/' . $this->getProp('name'),
            [
                'editor_script' => $this->getProp('editor_script'),
                'editor_style' => $this->getProp('editor_style'),
                'style' => $this->getProp('style'),
                // Loads on every page no matter if block presents or not
                // To load only if block presents - use "front_script"
                'script' => $this->getProp('script'),
                'render_callback' => [$this, 'render'],
                'supports' => $this->getProp('supports'),
            ]
        );
    }

    /**
     * Register Block Assets
     */
    public function registerAssets()
    {
        foreach (['editor_script', 'script', 'front_script', 'editor_style', 'style', 'scripts'] as $assetType) {
            if (!$assetData = $this->getProp($assetType)) {
                continue;
            }

            // Front-end scripts that load if the block presents on the page
            if ('scripts' === $assetType) {
                $handles = [];

                foreach ($assetData as $index => $assetItem) {
                    $args = [
                        'id' => sprintf('%s-%s-%d', $this->getProp('name'), 'front-script', $index + 1),
                        'type' => 'front',
                        'enqueue' => false,
                    ];

                    $asset = $this->m('asset.js', array_merge($assetItem, $args));

                    $handles[] = $asset->getProp('handle');
                }

                $this->setProp('scripts', $handles);
            } else {
                switch ($assetType) {
                    // Loads only in Gutenberg
                    case 'editor_script':
                        $type = 'js';
                        $af = 'block';
                        $suffix = 'editor-script';
                        break;

                    // Loads only in Gutenberg
                    case 'editor_style':
                        $type = 'css';
                        $af = 'block';
                        $suffix = 'editor-style';
                        break;

                    // Loads on every page no matter if the block presents or not
                    case 'script':
                        $type = 'js';
                        $af = 'front';
                        $suffix = 'front-script-all';
                        break;

                    // Loads if the block presents on the page
                    case 'front_script':
                        $type = 'js';
                        $af = 'front';
                        $suffix = 'front-script';
                        break;

                    // Loads on every page no matter if the block presents or not
                    case 'style':
                        $type = 'css';
                        $af = 'front';
                        $suffix = 'front-style';
                        break;
                }

                $args = [
                    'id' => sprintf('%s-%s', $this->getProp('name'), $suffix),
                    'type' => $af,
                    'enqueue' => false,
                ];

                $asset = $this->m('asset.' . $type, array_merge($assetData, $args));

                $this->setProp($assetType, $asset->getProp('handle'));
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
        // Add front script if set
        if ($this->getProp('front_script')) {
            wp_enqueue_script($this->getProp('front_script'));
        }

        // Add front scripts if set
        foreach ($this->getProp('scripts') ?: [] as $handle) {
            wp_enqueue_script($handle);
        }

        // Call the callback if set
        $callback = $this->getProp('render_callback');

        return $callback ? $callback($atts, $content) : '';
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
            'render_callback' => null,
        ];
    }
}
