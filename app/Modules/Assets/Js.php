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
     * Get enqueue func name
     *
     * @return string
     */
    protected function getEnqueueFuncName(): string
    {
        return 'wp_enqueue_script';
    }

    /**
     * Register script
     */
    public function register()
    {
        // Register script
        $handle = $this->getProp('handle');

        wp_register_script($handle, $this->getProp('url'), $this->getProp('deps'), $this->getProp('ver'), true);

        // Data for front-end var
        $data = array_merge(
            [
                'prefix' => $this->prefix,
                'nonce' => wp_create_nonce($this->prefix),
                'rest_nonce' => wp_create_nonce('wp_rest'),
                'ajax_url' => admin_url('admin-ajax.php'),
            ],
            $this->getProp('data')
        );

        wp_localize_script($handle, $this->getProp('var'), $data);
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
            'var' => function () {
                return sprintf('%s_%s_config', $this->prefix, str_replace('-', '_', $this->getProp('id')));
            },
            'data' => [],
        ];

        return array_merge(parent::defaults(), $defaults);
    }
}
