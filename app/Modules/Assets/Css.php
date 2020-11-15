<?php

namespace AlexDashkin\Adwpfw\Modules\Assets;

/**
 * type*, url*, id, ver, deps, callback
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
        wp_register_style($this->getProp('handle'), $this->getProp('url'), $this->getProp('deps'), $this->getProp('ver'));
    }
}
