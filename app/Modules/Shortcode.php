<?php

namespace AlexDashkin\Adwpfw\Modules;

use AlexDashkin\Adwpfw\Modules\Assets\Asset;

/**
 * Shortcode
 */
class Shortcode extends Module
{
    /**
     * @var Asset[]
     */
    protected $assets = [];

    /**
     * Init Module
     */
    public function init()
    {
        $this->addHook('template_redirect', [$this, 'register']);
    }

    /**
     * Add Asset
     *
     * @param Asset $asset
     */
    public function addAsset(Asset $asset)
    {
        $this->assets[] = $asset;
    }

    /**
     * Register the Shortcode
     */
    public function register()
    {
        add_shortcode($this->getProp('tag'), [$this, 'render']);
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
        // Enqueue assets
        foreach ($this->assets as $asset) {
            $asset->enqueue();
        }

        $args = array_merge($this->getProp('atts') ?: [], $atts ?: []);

        try {
            return $this->getProp('callback')($args);
        } catch (\Exception $e) {
            $this->log('Exception in shortcode "%s": %s', [$this->getProp('tag'), $e->getMessage()]);
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
            'tag' => [
                'type' => 'string',
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

        return array_merge($baseProps, $fieldProps);
    }
}
