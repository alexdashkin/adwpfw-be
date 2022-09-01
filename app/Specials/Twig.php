<?php

namespace AlexDashkin\Adwpfw\Specials;

use AlexDashkin\Adwpfw\Exceptions\AppException;
use Twig\Environment;
use Twig\Loader\ArrayLoader;
use Twig\Loader\FilesystemLoader;

/**
 * Twig Template Engine
 */
class Twig
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
     * Protected constructor
     *
     * @throws AppException
     */
    public function __construct(array $config)
    {
        if (!class_exists('\Twig\Environment')) {
            throw new AppException('Twig not found');
        }

        foreach (['env', 'paths', 'cachePath'] as $fieldName) {
            if (empty($config[$fieldName])) {
                throw new AppException(sprintf('Field "%s" is required', $fieldName));
            }
        }

        // Set config
        $env = $config['env'];
        $paths = $config['paths'];
        $cachePath = $config['cachePath'];

        // Check paths existence
        foreach (array_merge($paths, [$cachePath]) as $path) {
            if (!file_exists($path)) {
                throw new AppException(sprintf('Path "%s" does not exist', $path));
            }
        }

        // Compose args
        $envArgs = [
            'debug' => 'dev' === $env,
            'cache' => $cachePath . '/twig',
            'autoescape' => false,
        ];

        // Init Twig
        $this->fsLoader = new FilesystemLoader($paths);
        $this->twigFs = new Environment($this->fsLoader, $envArgs);
        $this->arrayLoader = new ArrayLoader();
        $this->twigArray = new Environment($this->arrayLoader, $envArgs);
    }

    /**
     * Render File Template.
     *
     * @param string $name Template file name without .twig.
     * @param array $args Args to be passed to the Template. Default [].
     * @return string Rendered Template.
     */
    public function renderFile(string $name, array $args = []): string
    {
        return $this->render($this->twigFs, $name, $args);
    }

    /**
     * Add Array Template
     *
     * @param string $name
     * @param string $template
     */
    public function addTemplate(string $name, string $template)
    {
        $this->arrayLoader->setTemplate($name, $template);
    }

    /**
     * Render Array Template.
     *
     * @param string $name Template name.
     * @param array $args Args to be passed to the Template. Default [].
     * @return string Rendered Template.
     */
    public function renderArray(string $name, array $args = []): string
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
    private function render(Environment $twig, string $name, array $args = []): string
    {
        return $twig->render($name, $args);
    }
}
