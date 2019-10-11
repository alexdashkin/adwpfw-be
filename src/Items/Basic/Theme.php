<?php

namespace AlexDashkin\Adwpfw\Items\Basic;

use AlexDashkin\Adwpfw\App;

/**
 * Theme with Self-Update feature
 */
class Theme extends Item
{
    /**
     * Constructor.
     *
     * @param array $data {
     * @type string $id ID for internal use. Defaults to sanitized $path.
     * @type string $slug Theme's directory name. Required.
     * @type string $package URL of the package. Required.
     * }
     * @throws \AlexDashkin\Adwpfw\Exceptions\AdwpfwException
     */
    public function __construct(array $data, App $app)
    {
        $props = [
            'id' => [
                'default' => $this->getDefaultId($data['path']),
            ],
            'slug' => [
                'required' => true,
            ],
            'package' => [
                'required' => true,
            ],
        ];

        parent::__construct($data, $app, $props);

        $newVer = '100.0.0';

        $slug = $this->data['slug'];

        if ($themeData = wp_get_theme($slug)) {
            $oldVer = $themeData->version;
            $last = (int)substr($oldVer, -1);
            $newVer = substr($oldVer, 0, strlen($oldVer) - 1) . ++$last;
        }

        $this->data = [
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
            $transient->response[$this->data['slug']] = $this->data;
        }

        return $transient;
    }
}
