<?php

namespace AlexDashkin\Adwpfw\Front;

/**
 * Manage Shortcodes
 */
class Shortcodes extends \AlexDashkin\Adwpfw\Common\Base
{
    private $shortcodes = [];

    public function __construct($app)
    {
        parent::__construct($app);
    }

    public function run()
    {
        add_action('init', [$this, 'register'], 999);
    }

    /**
     * Add a Shortcode
     *
     * @param array $shortcode {
     * @type string $tag Tag without prefix
     * @type array $atts Default atts
     * @type callable $callable Render function
     * }
     */
    public function addShortcode(array $shortcode)
    {
        $shortcode = array_merge([
            'tag' => $this->config['prefix'],
            'atts' => [],
            'callable' => '',
        ], $shortcode);

        $this->shortcodes[] = $shortcode;
    }

    /**
     * Add multiple Shortcodes
     *
     * @param array $shortcodes
     *
     * @see Shortcodes::addShortcode()
     */
    public function addShortcodes(array $shortcodes)
    {
        foreach ($shortcodes as $shortcode) {
            $this->addShortcode($shortcode);
        }
    }

    public function register()
    {
        foreach ($this->shortcodes as $shortcode) {
            add_shortcode($this->config['prefix'] . '_' . $shortcode['tag'], function ($atts) use ($shortcode) {
                if (!is_callable($shortcode['callable'])) {
                    return '';
                }

                return $this->render((array)$atts, $shortcode['atts'], $shortcode['callable']);
            });
        }
    }

    public function render(array $atts, array $defaults, $callable)
    {
        $attributes = $defaults ? shortcode_atts($defaults, $atts) : $atts;
        return $callable($attributes);
    }
}
