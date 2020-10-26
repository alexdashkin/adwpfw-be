<?php

namespace AlexDashkin\Adwpfw\Modules;

/**
 *
 */
class Block extends Module
{
    /**
     * Init Module
     */
    public function init()
    {
        // Register Block
        $this->addHook('acf/init', [$this, 'register']);
    }

    /**
     * Register Block
     */
    public function register()
    {
        // Check if function exists
        if (!function_exists('acf_register_block_type')) {
            return;
        }

        // Prefix slug
        $this->setProp('name', $this->prefix . '_' . $this->getProp('name'));

        // Set Assets callback
        $this->setProp('enqueue_assets', [$this, 'enqueueAssets']);

        // Register with ACF
        acf_register_block_type($this->getProps());
    }

    public function enqueueAssets()
    {
        // If no associated assets - return
        if (!$assets = $this->getProp('assets')) {
            return;
        }

        // Enqueue block assets
        foreach ($this->getProp('assets') as $asset) {
            // Type here is CSS/JS
            $type = $asset['type'] ?? 'css';

            // Enqueue both in Gutenberg and on front-end
            foreach (['block', 'front'] as $af) {
                $asset['type'] = $af;

                $args = [
                    'id' => sprintf('%s-%s-%s', $this->prefix, $asset['type'], $this->getProp('name')),
                ];

                $this->m('asset.' . $type, array_merge($args, $asset));
            }
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
            'assets' => [],
        ];
    }
}
