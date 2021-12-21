<?php

namespace AlexDashkin\Adwpfw;

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
     * Class instance
     *
     * @var static
     */
    private static $instance;

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
    private function __construct()
    {
    }

    /**
     * Init
     *
     * @param array $args
     * @throws AppException
     */
    public static function init(array $args)
    {
        if (!class_exists('\Twig\Environment')) {
            throw new AppException('Twig not found');
        }

        foreach (['env', 'paths', 'cachePath'] as $fieldName) {
            if (empty($args[$fieldName])) {
                throw new AppException(sprintf('Field "%s" is required', $fieldName));
            }
        }

        self::$instance = new self();

        // Set config
        $env = $args['env'];
        $paths = $args['paths'];
        $cachePath = $args['cachePath'];

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
        self::$instance->fsLoader = new FilesystemLoader($paths);
        self::$instance->twigFs = new Environment(self::$instance->fsLoader, $envArgs);
        self::$instance->arrayLoader = new ArrayLoader();
        self::$instance->twigArray = new Environment(self::$instance->arrayLoader, $envArgs);
    }

    /**
     * Render File Template.
     *
     * @param string $name Template file name without .twig.
     * @param array $args Args to be passed to the Template. Default [].
     * @return string Rendered Template.
     */
    public static function renderFile(string $name, array $args = []): string
    {
        if (empty(self::$instance)) {
            throw new AppException('Twig is not configured');
        }

        return self::$instance->render(self::$instance->twigFs, $name, $args);
    }

    /**
     * Render Array Template.
     *
     * @param string $name Template name.
     * @param array $args Args to be passed to the Template. Default [].
     * @return string Rendered Template.
     */
    public static function renderArray(string $name, array $args = []): string
    {
        if (empty(self::$instance)) {
            throw new AppException('Logger is not configured');
        }

        return self::$instance->render(self::$instance->twigArray, $name, $args);
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
