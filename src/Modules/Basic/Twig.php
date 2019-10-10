<?php

namespace AlexDashkin\Adwpfw\Modules\Basic;

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
    private $fsLoader;
    private $arrayLoader;
    private $twigFs;
    private $twigArray;

    public function __construct(App $app)
    {
        if (!class_exists('\Twig\Environment')) {
            throw new AdwpfwException('Twig not found');
        }

        parent::__construct($app);

        $paths = [
            $this->config['baseDir'] . 'tpl/adwpfw',
            $this->config['baseDir'] . 'tpl',
            __DIR__ . '/../../Templates',
        ];

        foreach ($paths as $index => $path) {
            if (!file_exists($path)) {
                unset($paths[$index]);
            }
        }

        $config = [
            'debug' => (bool)$this->config['dev'],
            'cache' => Helpers::getUploadsDir($this->config['prefix'], 'twig'),
            'autoescape' => false,
        ];

        $this->fsLoader = new FilesystemLoader($paths);

        $this->twigFs = new Environment($this->fsLoader, $config);

        $this->arrayLoader = new ArrayLoader();

        $this->twigArray = new Environment($this->arrayLoader, $config);
    }

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

    public function addTemplates(array $templates)
    {
        foreach ($templates as $name => $template) {
            $this->arrayLoader->setTemplate($name, $template);
        }
    }

    public function renderFile($name, $args = [])
    {
        return $this->render($this->twigFs, $name . '.twig', $args);
    }

    public function renderTpl($name, $args = [])
    {
        return $this->render($this->twigArray, $name, $args);
    }

    /**
     * Render
     *
     * @param Environment $twig
     * @param string $name Template file name
     * @param array $args
     * @return string
     * @throws AdwpfwException
     */
    public function render($twig, $name, $args = [])
    {
        $args = array_merge([
            'prefix' => $this->config['prefix'],
        ], $args);

        try {
            return $twig->render($name, $args);
        } catch (Error $e) {
            throw new AdwpfwException($e->getMessage(), 0, $e);
        }
    }
}