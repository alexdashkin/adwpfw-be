<?php

namespace AlexDashkin\Adwpfw\Modules;

use AlexDashkin\Adwpfw\App;
use AlexDashkin\Adwpfw\Exceptions\AdwpfwException;
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
     * Twig constructor.
     *
     * @param App $app
     *
     * @throws AdwpfwException
     */
    public function __construct(App $app)
    {
        if (!class_exists('\Twig\Environment')) {
            throw new AdwpfwException('Twig not found');
        }

        parent::__construct($app);

        $paths = [
            $this->config['baseDir'] . 'tpl/adwpfw',
            $this->config['baseDir'] . 'tpl',
            __DIR__ . '/../../tpl',
        ];

        foreach ($paths as $index => $path) {
            if (!file_exists($path)) {
                unset($paths[$index]);
            }
        }

        $config = [
            'debug' => !empty($this->config['dev']),
            'cache' => Helpers::getUploadsDir($this->config['prefix'] . '/twig'),
            'autoescape' => false,
        ];

        $this->fsLoader = new FilesystemLoader($paths);

        $this->twigFs = new Environment($this->fsLoader, $config);

        $this->arrayLoader = new ArrayLoader();

        $this->twigArray = new Environment($this->arrayLoader, $config);
    }

    /**
     * Add file paths to search templates in.
     *
     * @param array $paths
     *
     * @throws AdwpfwException
     */
    public function addPaths(array $paths)
    {
        foreach ($paths as $path) {
            if (file_exists($path)) {
                $this->log('Path "%s" does not exist', [$path]);
                continue;
            }

            try {
                $this->fsLoader->addPath($path);
            } catch (Error $e) {
                throw new AdwpfwException($e->getMessage(), 0, $e);
            }
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
     * @param array $args Args to be passed to the Template.
     * @return string Rendered Template.
     *
     * @throws AdwpfwException
     */
    public function renderFile($name, $args = [])
    {
        return $this->render($this->twigFs, $name . '.twig', $args);
    }

    /**
     * Render Array Template.
     *
     * @param string $name Template name.
     * @param array $args Args to be passed to the Template.
     * @return string Rendered Template.
     *
     * @throws AdwpfwException
     */
    public function renderArray($name, $args = [])
    {
        return $this->render($this->twigArray, $name, $args);
    }

    /**
     * Render a Template.
     *
     * @param Environment $twig Array or FileSystem Environment
     * @param string $name Template name.
     * @param array $args Args to be passed to the Template.
     * @return string Rendered Template.
     */
    private function render($twig, $name, $args = [])
    {
        $args = array_merge([
            'prefix' => $this->config['prefix'],
        ], $args);

        try {
            return $twig->render($name, $args);

        } catch (Error $e) {
            $message = $e->getMessage();
            $this->log($message);
            return 'Unable to render Template: ' . $message;
        }
    }
}