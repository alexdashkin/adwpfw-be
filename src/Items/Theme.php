<?php

namespace AlexDashkin\Adwpfw\Items;

use AlexDashkin\Adwpfw\App;
use AlexDashkin\Adwpfw\Exceptions\AdwpfwException;

/**
 * Theme with Self-Update feature.
 */
class Theme extends Item
{
    /**
     * @var array Update transient data.
     */
    private $transient;

    /**
     * Constructor.
     *
     * @param App $app
     * @param array $data {
     * @type string $id ID for internal use. Defaults to sanitized $slug.
     * @type string $slug Theme's directory name. Defaults to current theme slug.
     * @type string $package URL of the package. Required.
     * @type callable $update_callback Function to call on theme update. Default empty.
     * }
     *
     * @throws AdwpfwException
     */
    public function __construct(App $app, array $data)
    {
        $slug = get_stylesheet();

        $props = [
            'id' => [
                'default' => $this->getDefaultId($slug),
            ],
            'slug' => [
                'default' => $slug,
            ],
            'package' => [
                'required' => true,
            ],
            'update_callback' => [
                'type' => 'callable',
                'default' => null,
            ],
        ];

        parent::__construct($app, $data, $props);

        $newVer = '100.0.0';

        $slug = $this->data['slug'];

        if ($themeData = wp_get_theme($slug)) {
            $oldVer = $themeData->version;
            $last = (int)substr($oldVer, -1);
            $newVer = substr($oldVer, 0, strlen($oldVer) - 1) . ++$last;
        }

        $this->transient = [
            'name' => $slug,
            'theme' => $slug,
            'new_version' => $newVer,
            'package' => $this->data['package'],
            'url' => '',
        ];
    }

    /**
     * Filter Update transient.
     *
     * @param object $transient Transient passed to the filter.
     * @return object Modified Transient.
     */
    public function register($transient)
    {
        if (!empty($transient->checked)) {
            $transient->response[$this->transient['name']] = $this->transient;
        }

        return $transient;
    }

    /**
     * Hooked into "upgrader_process_complete".
     *
     * @param array $data
     */
    public function onUpdate($data)
    {
        if (!$this->data['update_callback'] || $data['action'] !== 'update' || $data['type'] !== 'theme'
            || empty($data['themes']) || !in_array($this->transient['name'], $data['themes'])) {
            return;
        }

        $this->data['update_callback']();
    }
}
