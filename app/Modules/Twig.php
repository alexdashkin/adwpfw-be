<?php

namespace AlexDashkin\Adwpfw\Modules;

use AlexDashkin\Adwpfw\Abstracts\Module;
use AlexDashkin\Adwpfw\Exceptions\AppException;
use Twig\Environment;
use Twig\Error\Error;
use Twig\Loader\ArrayLoader;
use Twig\Loader\FilesystemLoader;

/**
 * Twig Template Engine
 */
class Twig extends Module
{
    /**
     * @var FilesystemLoader
     */
    private $fsLoader;

    /**
     * @var ArrayLoader
     */
    private $arrayLoader;

    /**
     * @var Environment File System Environment
     */
    private $twigFs;

    /**
     * @var Environment Array Environment
     */
    private $twigArray;

    /**
     * Init Module
     *
     * @throws AppException
     */
    public function init()
    {
        if (!class_exists('\Twig\Environment')) {
            throw new AppException('Twig not found');
        }

        $data = $this->data;

        $paths = $this->get('paths');
        $paths[] = __DIR__ . '/../../tpl';

        foreach ($paths as $index => $path) {
            if (!file_exists($path)) {
                $this->log('Path "%s" does not exist', [$path]);
                unset($paths[$index]);
            }
        }

        $this->fsLoader = new FilesystemLoader($paths);

        $this->twigFs = new Environment($this->fsLoader, $data);

        $this->arrayLoader = new ArrayLoader();

        $this->twigArray = new Environment($this->arrayLoader, $data);
    }

    /**
     * Add file paths to search templates in
     *
     * @param array $paths
     */
    public function addPaths(array $paths)
    {
        foreach ($paths as $path) {
            if (file_exists($path)) {
                $this->log('Path "%s" does not exist', [$path]);
                continue;
            }

            $this->fsLoader->addPath($path);
        }
    }

    /**
     * Add string templates as key-value pairs.
     *
     * @param array $templates
     */
    public function addTemplates(array $templates)
    {
        foreach ($templates as $name => $template) {
            $this->arrayLoader->setTemplate($name, $template);
        }
    }

    /**
     * Render File Template.
     *
     * @param string $name Template file name without .twig.
     * @param array $args Args to be passed to the Template. Default [].
     * @return string Rendered Template.
     */
    public function renderFile($name, $args = []): string
    {
        return $this->render($this->twigFs, $name . '.twig', $args);
    }

    /**
     * Render String Template
     *
     * @param string $tpl Template
     * @param array $args Args to be passed to the Template. Default [].
     * @return string Rendered Template.
     */
    public function renderString($tpl, $args = []): string
    {
        $this->addTemplates(['temp' => $tpl]);

        return $this->render($this->twigArray, 'temp', $args);
    }

    /**
     * Render Array Template.
     *
     * @param string $name Template name.
     * @param array $args Args to be passed to the Template. Default [].
     * @return string Rendered Template.
     */
    public function renderArray($name, $args = []): string
    {
        return $this->render($this->twigArray, $name, $args);
    }

    /**
     * Render a Template.
     *
     * @param Environment $twig Array or FileSystem Environment
     * @param string $name Template name.
     * @param array $args Args to be passed to the Template. Default [].
     * @return string Rendered Template.
     */
    private function render($twig, $name, $args = []): string
    {
        $args = array_merge(
            [
                'prefix' => $this->get('prefix'),
            ],
            $args
        );

        try {
            return $twig->render($name, $args);
        } catch (Error $e) {
            $message = $e->getMessage();
            $this->log($message);
            return 'Unable to render Template: ' . $message;
        }
    }

    /**
     * Get Class props
     *
     * @return array
     */
    protected function props(): array
    {
        return [
            'prefix' => [
                'required' => true,
            ],
            'cache_path' => [
                'required' => true,
            ],
            'paths' => [
                'type' => 'array',
                'default' => [],
            ],
            'debug' => [
                'default' => false,
            ],
            'autoescape' => [
                'default' => false,
            ],
        ];
    }
}