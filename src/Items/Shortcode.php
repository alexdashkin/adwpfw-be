<?php

namespace AlexDashkin\Adwpfw\Items;

use AlexDashkin\Adwpfw\App;
use AlexDashkin\Adwpfw\Exceptions\AdwpfwException;

/**
 * Shortcode.
 */
class Shortcode extends Item
{
    /**
     * Constructor.
     *
     * @param App $app
     * @param array $data {
     * @type string $id Tag without prefix. Required.
     * @type callable $callback Render function. Gets $atts. Required.
     * @type array $atts Default atts (key-value pairs). Default [].
     * }
     *
     * @throws AdwpfwException
     */
    public function __construct(App $app, array $data)
    {
        $props = [
            'id' => [
                'required' => true,
            ],
            'callback' => [
                'type' => 'callable',
                'required' => true,
            ],
            'atts' => [
                'type' => 'array',
                'default' => [],
            ],
        ];

        parent::__construct($app, $data, $props);
    }

    /**
     * Register the Shortcode.
     */
    public function register()
    {
        add_shortcode($this->prefix . '_' . $this->data['id'], [$this, 'render']);
    }

    /**
     * Render the Shortcode.
     *
     * @param array|string $atts
     * @param string $content
     * @param string $tag
     * @return string
     */
    public function render($atts, $content, $tag)
    {
        return $this->data['callback'](shortcode_atts($this->data['atts'], $atts));
    }
}
