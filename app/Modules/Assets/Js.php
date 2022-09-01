<?php

namespace AlexDashkin\Adwpfw\Modules\Assets;

/**
 * JS
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
        $handle = $this->getHandle();

        wp_register_script($handle, $this->getUrl(), $this->getProp('deps'), $this->getVer(), true);

        // Data for front-end var
        $data = array_merge(
            [
                'nonce' => wp_create_nonce('adwpfw'),
                'restNonce' => wp_create_nonce('wp_rest'),
                'ajaxUrl' => admin_url('admin-ajax.php'),
            ],
            $this->getProp('data')
        );

        wp_localize_script($handle, $this->getProp('var'), $data);
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
            'var' => [
                'type' => 'string',
                'default' => function () {
                    return sprintf('%s_config', str_replace('-', '_', $this->getHandle()));
                },
            ],
            'data' => [
                'type' => 'array',
                'default' => [],
            ],
        ];

        return array_merge($baseProps, $fieldProps);
    }
}
