<?php

namespace AlexDashkin\Adwpfw\Modules\Assets;

/**
 * CSS
 */
class Css extends Asset
{
    /**
     * Get file extension
     *
     * @return string
     */
    protected function getFileExt(): string
    {
        return 'css';
    }

    /**
     * Get enqueue func name
     *
     * @return string
     */
    protected function getEnqueueFuncName(): string
    {
        return 'wp_enqueue_style';
    }

    /**
     * Register style
     */
    public function register()
    {
        wp_register_style($this->getHandle(), $this->getUrl(), $this->getProp('deps'), $this->getVer());
    }
}
