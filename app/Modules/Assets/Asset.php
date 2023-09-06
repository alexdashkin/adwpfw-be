<?php

namespace AlexDashkin\Adwpfw\Modules\Assets;

use AlexDashkin\Adwpfw\Modules\Module;

/**
 * CSS/JS
 */
abstract class Asset extends Module
{
    /**
     * Init Module
     */
    public function init()
    {
        // Register
        $this->runAfterHook([$this, 'process']);
    }

    public function process()
    {
        $this->register();

        if ($this->getProp('enqueue')($this)) {
            $this->enqueue();
        }
    }

    /**
     * Enqueue
     */
    public function enqueue()
    {
        $this->runAfterHook([$this, 'enqueueAsset']);
    }

    /**
     * Enqueue asset
     */
    public function enqueueAsset()
    {
        $func = $this->getEnqueueFuncName();

        $func($this->getHandle());
    }

    /**
     * Hook name depends on scope
     *
     * @return string
     */
    protected function getHookName(): string
    {
        switch ($this->getProp('scope')) {
            case 'admin':
                $hookName = 'admin_enqueue_scripts';
                break;

            case 'block':
                $hookName = 'enqueue_block_editor_assets';
                break;

            default:
                $hookName = 'wp_enqueue_scripts';
        }

        return $hookName;
    }

    /**
     * Run either on hook or immediately if hook has been fired
     *
     * @param callable $callback
     */
    protected function runAfterHook(callable $callback)
    {
        $hookName = $this->getHookName();

        if (did_action($hookName)) {
            $callback();
        } else {
            $this->addHook($hookName, $callback);
        }
    }

    /**
     * Get or generate Handle
     *
     * @return string
     */
    protected function getHandle(): string
    {
        if ($handle = $this->getProp('handle')) {
            return $handle;
        }

        $handle = sprintf('%s-%s-%s-%s', $this->config('prefix'), $this->getProp('scope'), strpos(strtolower(get_called_class()), 'css') ? 'css' : 'js', wp_generate_password(6, false));

        $this->setProp('handle', $handle);

        return $handle;
    }

    /**
     * Get URL
     *
     * @return string
     */
    protected function getUrl(): string
    {
        if ($url = $this->getProp('url')) {
            return $url;
        }

        return sprintf('%s%s', 'theme' === $this->config('type') ? get_stylesheet_directory_uri() . '/' : plugin_dir_url($this->config('baseFile')), $this->getProp('file'));
    }

    /**
     * Get or generate Version
     *
     * @return string
     */
    protected function getVer(): string
    {
        if ($ver = $this->getProp('ver')) {
            return $ver;
        }

        $path = dirname($this->config('baseFile')) . '/' . $this->getProp('file');

        return file_exists($path) ? filemtime($path) : $ver;
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
            'handle' => [
                'type' => 'string',
                'default' => '',
            ],
            'scope' => [
                'type' => 'string',
                'default' => 'front',
            ],
            'file' => [
                'type' => 'string',
                'default' => '',
            ],
            'url' => [
                'type' => 'string',
                'default' => '',
            ],
            'deps' => [
                'type' => 'array',
                'default' => [],
            ],
            'ver' => [
                'type' => 'string',
                'default' => '',
            ],
            'enqueue' => [
                'type' => 'callable',
                'default' => '__return_true',
            ],
        ];

        return array_merge($baseProps, $fieldProps);
    }

    /**
     * Get enqueue func name
     *
     * @return string
     */
    abstract protected function getEnqueueFuncName(): string;

    /**
     * Register asset
     */
    abstract protected function register();
}
