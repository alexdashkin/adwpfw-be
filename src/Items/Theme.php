<?php

namespace AlexDashkin\Adwpfw\Items;

use AlexDashkin\Adwpfw\App;

/**
 * Theme with Self-Update feature
 */
class Theme extends Item
{
    /**
     * Constructor
     *
     * @param array $data {
     * @type string $slug Theme's directory name
     * @type string $package URL of the package
     * }
     */
    public function __construct(array $data, App $app)
    {
        $this->props = [
            'slug' => [
                'required' => true,
            ],
            'package' => [
                'required' => true,
            ],
        ];

        parent::__construct($data, $app);

        $newVer = '100.0.0';

        $slug = $this->data['slug'];

        if ($themeData = wp_get_theme($slug)) {
            $oldVer = $themeData->version;
            $last = (int)substr($oldVer, -1);
            $newVer = substr($oldVer, 0, strlen($oldVer) - 1) . ++$last;
        }

        $data = [
            'name' => $slug,
            'theme' => $slug,
            'new_version' => $newVer,
            'package' => $this->data['package'],
            'url' => '',
        ];

        $this->data = $data;
    }

    /**
     * Filter Update transient
     */
    public function register($transient)
    {
        if (!empty($transient->checked)) {
            $transient->response[$this->data['slug']] = $this->data;
        }

        return $transient;
    }
}
