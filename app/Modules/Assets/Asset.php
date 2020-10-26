<?php

namespace AlexDashkin\Adwpfw\Modules\Assets;

use AlexDashkin\Adwpfw\Modules\Module;

abstract class Asset extends Module
{
    /**
     * Init Module
     */
    public function init()
    {
        // Prepare args
        $file = $this->getProp('file') . $this->getProp('min') . '.' . $this->getFileExt();
        $path = $file ? $this->getProp('base_dir') . '/' . $file : '';
        $url = $file ? $this->getProp('base_url') . '/' . $file : '';

        // Set URL
        if (!$this->getProp('url') && $url) {
            $this->setProp('url', $url);
        }

        // Set Version
        if (!$this->getProp('ver') && $path && file_exists($path)) {
            $this->setProp('ver', filemtime($path));
        }

        // Action name depends on assets type
        switch ($this->getProp('type')) {
            case 'admin':
                $action = 'admin_enqueue_scripts';
                break;

            case 'block':
                $action = 'enqueue_block_editor_assets';
                break;

            default:
                $action = 'wp_enqueue_scripts';
        }

        // Add action
        $this->addHook($action, [$this, 'enqueue'], 99);
    }

    /**
     * Get Default prop values
     *
     * @return array
     */
    protected function defaults(): array
    {
        $baseFile = $this->config('base_file');

        return [
            'id' => function () {
                return sanitize_key(str_replace(' ', '_', $this->getProp('type')));
            },
            'base_dir' => dirname($baseFile),
            'base_url' => 'theme' === $this->config('type') ? get_stylesheet_directory_uri() : plugin_dir_url($baseFile),
            'min' => defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.min',
        ];
    }

    /**
     * Get file extension
     *
     * @return string
     */
    abstract protected function getFileExt(): string;

    /**
     * Enqueue asset
     */
    abstract public function enqueue();
}
