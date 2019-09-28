<?php

namespace AlexDashkin\Adwpfw\Admin;

/**
 * Special Admin pages
 */
class AdminPage extends \AlexDashkin\Adwpfw\Common\Base
{
    private $pages = [];

    public function __construct($app)
    {
        parent::__construct($app);
    }

    public function run()
    {
        add_action('admin_menu', [$this, 'registerPages']);
    }

    /**
     * Add a special page
     *
     * @param array $page {
     * @type string $id
     * @type string $title
     * @type string $name
     * @type callable $callback Renders the page
     * }
     */
    public function addPage(array $page)
    {
        $page = array_merge([
            'id' => '',
            'title' => 'Page',
            'name' => 'Page',
            'callback' => '',
        ], $page);

        $page['id'] = $page['id'] ?: sanitize_title($page['title']);

        $this->pages[] = $page;
    }

    /**
     * Add multiple special pages
     *
     * @param array $pages
     *
     * @see AdminPage::addPage()
     */
    public function addPages(array $pages)
    {
        foreach ($pages as $page) {
            $this->addPage($page);
        }
    }

    public function registerPages()
    {
        foreach ($this->pages as $page) {
            add_dashboard_page($page['title'], $page['name'], 'read', $page['id'], $page['callback']);
        }
    }
}
