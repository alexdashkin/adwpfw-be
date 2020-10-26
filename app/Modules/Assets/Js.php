<?php

namespace AlexDashkin\Adwpfw\Modules\Assets;

/**
 * type*, url*, id, ver, deps, callback, localize
 */
class Js extends Asset
{
    /**
     * Get file extension
     *
     * @return string
     */
    protected function getFileExt(): string
    {
        return 'js';
    }

    /**
     * Enqueue script
     */
    public function enqueue()
    {
        $callback = $this->getProp('callback');

        // Exit if callback returns false
        if ($callback && is_callable($callback) && !$callback()) {
            return;
        }

        $prefix = $this->prefix;

        $id = $prefix . '-' . $this->getProp('id');

        // Enqueue new script
        wp_enqueue_script($id, $this->getProp('url'), $this->getProp('deps'), $this->getProp('ver'), true);

        // Localize script
        $localize = array_merge(
            [
                'prefix' => $prefix,
                'nonce' => wp_create_nonce($prefix),
                'rest_nonce' => wp_create_nonce('wp_rest'),
                'ajax_url' => admin_url('admin-ajax.php'),
            ],
            $this->getProp('localize')
        );

        wp_localize_script($id, $prefix, $localize);
    }

    /**
     * Get Default prop values
     *
     * @return array
     */
    protected function defaults(): array
    {
        $defaults = [
            'deps' => ['jquery'],
            'localize' => [],
        ];

        return array_merge(parent::defaults(), $defaults);
    }
}
