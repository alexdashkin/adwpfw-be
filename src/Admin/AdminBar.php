<?php

namespace AlexDashkin\Adwpfw\Admin;

/**
 * Top Admin Bar
 */
class AdminBar extends \AlexDashkin\Adwpfw\Common\Base
{
    private $bars = [];

    public function __construct($app)
    {
        parent::__construct($app);
    }

    public function run()
    {
        add_action('admin_bar_menu', [$this, 'registerBar'], 999);
    }

    /**
     * Add an item to the Top Admin Bar
     *
     * @param array $bar {
     * @type string $id
     * @type string $title
     * @type string $capability Who can see the Bar
     * @type string $href URL of the link
     * @type array $meta
     * }
     */
    public function addBar(array $bar)
    {
        $bar = array_merge([
            'id' => '',
            'title' => 'Bar',
            'capability' => 'manage_options',
            'href' => '',
            'meta' => [],
        ], $bar);

        $bar['id'] = $bar['id'] ?: sanitize_title($bar['title']);

        $this->bars[] = $bar;
    }

    /**
     * Add multiple items to the Top Admin Bar
     *
     * @param array $bars
     *
     * @see AdminBar::addBar()
     */
    public function addBars(array $bars)
    {
        foreach ($bars as $bar) {
            $this->addBar($bar);
        }
    }

    /**
     * @param \WP_Admin_Bar $wpAdminBar
     */
    public function registerBar($wpAdminBar)
    {
        foreach ($this->bars as $bar) {
            if (!current_user_can($bar['capability'])) {
                continue;
            }

            foreach ($bar as $key => &$arg) {
                if ('meta' === $key) {
                    continue;
                }
                if (is_array($arg)) {
                    $arg = $this->m('Common\Utils')->renderTwig($arg[0], $arg[1]);
                }
            }

            $wpAdminBar->add_node($bar);
        }
    }
}
