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
        if ($parent = $this->getProp('parent')) {
            add_submenu_page(
                $parent,
                $this->getProp('title'),
                $this->getProp('name'),
                $this->getProp('capability'),
                $this->getProp('slug'),
                [$this, 'render']
            );
        } else {
            add_menu_page(
                $this->getProp('title'),
                $this->getProp('name'),
                $this->getProp('capability'),
                $this->getProp('slug'),
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
            'prefix' => $this->config('prefix'),
            'title' => $this->getProp('title'),
            'tabs' => $tabs,
        ];

        echo $this->app->main->render('templates/admin-page', $args);
    }

    /**
     * Get Default Prop value
     *
     * @param string $key
     * @return mixed
     */
    protected function getDefault(string $key)
    {
        $name = $this->getProp('name');

        switch ($key) {
            case 'title':
            case 'header':
                return $name;
            case 'slug':
                return sanitize_key(str_replace(' ', '-', $name));
            case 'icon':
                return 'dashicons-update';
            case 'capability':
                return 'manage_options';
        }

        return null;
    }
}
