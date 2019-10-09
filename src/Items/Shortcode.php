<?php

namespace AlexDashkin\Adwpfw\Items;

use AlexDashkin\Adwpfw\App;

/**
 * Shortcode
 */
class Shortcode extends Item
{
    /**
     * Constructor
     *
     * @param array $data {
     * @type string $tag Tag without prefix. Required.
     * @type callable $callable Render function. Required.
     * @type array $atts Default atts
     * }
     */
    public function __construct(array $data, App $app)
    {
        $this->props = [
            'tag' => [
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

        parent::__construct($data, $app);
    }

    /**
     * Register Items in WP
     */
    public function register()
    {
        add_shortcode($this->config['prefix'] . '_' . $this->data['tag'], [$this, 'render']);
    }

    /**
     * Render the Shortcode
     *
     * @param array $atts
     * @param string $content
     * @param string $tag
     * @return string
     */
    public function render(array $atts, $content, $tag)
    {
        return $this->data['callback'](shortcode_atts($this->data['atts'], $atts));
    }
}
