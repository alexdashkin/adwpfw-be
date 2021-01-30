<?php

namespace AlexDashkin\Adwpfw\Core;

use AlexDashkin\Adwpfw\Exceptions\AppException;
use Twig\Environment;
use Twig\Error\Error;
use Twig\Loader\ArrayLoader;
use Twig\Loader\FilesystemLoader;

/**
 * Twig Template Engine
 */
class Twig
{
    /**
     * @var App
     */
    private $app;

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
     * @param App $app
     * @throws AppException
     */
    public function __construct(App $app)
    {
        $this->app = $app;

        if (!class_exists('\Twig\Environment')) {
            throw new AppException('Twig not found');
        }

        // Get tpl files paths
        $paths = [];

        // Single tpl path provided by the client app
        $appPath = $this->app->config('template_path');

        if ($appPath) {
            $paths[] = $appPath;
        }

        // Array of tpl paths provided by the client app
        $appPaths = $this->app->config('template_paths');

        if (!empty($appPaths) && is_array($appPaths)) {
            $paths = array_merge($paths, array_values($appPaths));
        }

        // FW tpls path
        $paths[] = __DIR__ . '/../../tpl';

        // Check paths existence
        foreach ($paths as $index => $path) {
            if (!file_exists($path)) {
                $this->app->getLogger()->log('Path "%s" does not exist', [$path]);
                unset($paths[$index]);
            }
        }

        // Compose args
        $envArgs = [
            'debug' => 'dev' === $this->app->config('env'),
            'cache' => $this->app->getMain()->getUploadsDir($this->app->config('prefix') . '/twig'),
            'autoescape' => false,
        ];

        $this->fsLoader = new FilesystemLoader($paths);

        $this->twigFs = new Environment($this->fsLoader, $envArgs);

        $this->arrayLoader = new ArrayLoader();

        $this->twigArray = new Environment($this->arrayLoader, $envArgs);
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
                $this->app->getLogger()->log('Path "%s" does not exist', [$path]);
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
    public function renderFile(string $name, array $args = []): string
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
    public function renderString(string $tpl, array $args = []): string
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
        $args = array_merge(
            [
                'prefix' => $this->app->config('prefix'),
            ],
            $args
        );

        try {
            return $twig->render($name, $args);
        } catch (Error $e) {
            $message = $e->getMessage();
            $this->app->getLogger()->log($message);
            return 'Unable to render Template: ' . $message;
        }
    }
}
