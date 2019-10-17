<?php

namespace AlexDashkin\Adwpfw\Items\Basic;

use AlexDashkin\Adwpfw\App;

/**
 * Shortcode
 */
class Shortcode extends Item
{
    /**
     * Constructor.
     *
     * @param array $data {
     * @type string $id Tag without prefix. Required.
     * @type callable $callback Render function. Gets $atts. Required.
     * @type array $atts Default atts (key-value pairs).
     * }
     * @throws \AlexDashkin\Adwpfw\Exceptions\AdwpfwException
     */
    public function __construct(array $data, App $app)
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

        parent::__construct($data, $app, $props);
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
