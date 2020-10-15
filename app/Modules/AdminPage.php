<?php

namespace AlexDashkin\Adwpfw\Modules;

/**
 * name*, title, header, capability, slug, icon, position, parent
 */
class AdminPage extends Module
{
    /**
     * @var AdminPageTab[]
     */
    protected $tabs = [];

    /**
     * Add Tab
     *
     * @param AdminPageTab $tab
     */
    public function addTab(AdminPageTab $tab)
    {
        $tab->setParent($this);

        $this->tabs[] = $tab;
    }

    /**
     * Init Module
     */
    public function init()
    {
        $this->addHook('admin_menu', [$this, 'register']);
    }

    /**
     * Register the Page
     */
    public function register()
    {
        $slug = sprintf('%s-%s', $this->prefix, $this->getProp('slug'));

        if ($parent = $this->getProp('parent')) {
            add_submenu_page(
                $parent,
                $this->getProp('title'),
                $this->getProp('name'),
                $this->getProp('capability'),
                $slug,
                [$this, 'render']
            );
        } else {
            add_menu_page(
                $this->getProp('title'),
                $this->getProp('name'),
                $this->getProp('capability'),
                $slug,
                [$this, 'render'],
                $this->getProp('icon'),
                $this->getProp('position')
            );
        }
    }

    /**
     * Render the Page
     */
    public function render()
    {
        if (!$this->tabs) {
            return;
        }

        $tabs = [];

        foreach ($this->tabs as $tab) {
            $tabs[] = [
                'title' => $tab->getProp('title'),
                'content' => $tab->render(),
            ];
        }

        $args = [
            'prefix' => $this->prefix,
            'title' => $this->getProp('title'),
            'tabs' => $tabs,
        ];

        echo $this->main->render('templates/admin-page', $args);
    }

    /**
     * Get Default prop values
     *
     * @return array
     */
    protected function defaults(): array
    {
        $name = $this->getProp('name');

        return [
            'name' => 'Admin Page',
            'title' => $name,
            'header' => $name,
            'slug' => function () {
                return sanitize_key(str_replace(' ', '-', $this->getProp('name')));
            },
            'icon' => 'dashicons-update',
            'position' => 100,
            'capability' => 'manage_options',
        ];
    }
}
