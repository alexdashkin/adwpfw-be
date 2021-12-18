<?php

namespace AlexDashkin\Adwpfw\Modules\Assets;

use AlexDashkin\Adwpfw\Exceptions\AppException;
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
        $this->runAfterHook([$this, 'register']);

        // Enqueue if required
        if ($this->getProp('enqueue')) {
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

        $handle = sprintf('adwpfw-%s-%s-%s', $this->getProp('scope'), strpos(strtolower(get_called_class()), 'css') ? 'css' : 'js', mt_rand(1, 100));

        $this->setProp('handle', $handle);

        return $handle;
    }

    /**
     * Get or generate URL
     *
     * @return string
     * @throws AppException
     */
    protected function getUrl(): string
    {
        if ($url = $this->getProp('url')) {
            return $url;
        }

        if ((!$baseFile = $this->getProp('baseFile')) || (!$path = $this->getProp('path'))) {
            throw new AppException('Either URL or baseFile/path must be defined');
        }

        $path = trim($path, '/');

        $baseUrl = 'plugin' === $this->getProp('env') ? plugin_dir_url($baseFile) : get_stylesheet_directory_uri();

        return trailingslashit($baseUrl) . $path;
    }

    /**
     * Get or generate Version
     *
     * @return string
     */
    protected function getVer(): string
    {
        $ver = $this->getProp('ver');

        if ((!$baseFile = $this->getProp('baseFile')) || (!$path = $this->getProp('path'))) {
            return $ver;
        }

        $fullPath = dirname($baseFile) . '/' . trim($path, '/');

        return file_exists($fullPath) ? filemtime($fullPath) : $ver;
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
            'env' => [
                'type' => 'string',
                'default' => 'plugin',
            ],
            'baseFile' => [
                'type' => 'string',
                'default' => '',
            ],
            'path' => [
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
                'type' => 'bool',
                'default' => false,
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
