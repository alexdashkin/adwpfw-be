<?php

namespace AlexDashkin\Adwpfw\Items;

use AlexDashkin\Adwpfw\App;
use AlexDashkin\Adwpfw\Exceptions\AdwpfwException;

/**
 * Theme Widget
 */
class Widget extends Item
{
    /**
     * Constructor.
     *
     * @param App $app
     * @param array $data {
     * @type string $id Defaults to sanitized $title.
     * @type string $title Widget Title. Required.
     * @type callable $callback Renders the widget. Required.
     * @type string $capability Minimum capability. Default 'read'.
     * }
     *
     * @throws AdwpfwException
     * @see wp_add_dashboard_widget()
     *
     */
    public function __construct(App $app, array $data)
    {
        $props = [
            'id' => [
                'default' => $this->getDefaultId($data['name']),
            ],
            'name' => [
                'required' => true,
            ],
            'callback' => [
                'type' => 'callable',
                'required' => true,
            ],
            'options' => [
                'type' => 'array',
                'default' => [],
            ],
        ];

        parent::__construct($app, $data, $props);
    }

    /**
     * Register the Widget.
     */
    public function register()
    {
        $id = $this->prefix . '_' . $this->data['id'];

        $class = sprintf('namespace {class %s extends \AlexDashkin\Adwpfw\Items\WpWidget {public function __construct() {parent::__construct();}}}', $id);

        eval($class);

        register_widget($this->prefix . '_' . $this->data['id']);
    }

    protected function getDefaultId($base)
    {
        return uniqid(esc_attr(sanitize_key(str_replace(' ', '_', $base))) . '_');
    }
}
