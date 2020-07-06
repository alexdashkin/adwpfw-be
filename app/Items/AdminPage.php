<?php

namespace AlexDashkin\Adwpfw\Items;

use AlexDashkin\Adwpfw\Abstracts\Module;

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
        $this->tabs[] = $tab;
    }

    /**
     * Init Module
     */
    public function init()
    {
        $this->hook('admin_menu', [$this, 'register']);
    }

    /**
     * Register the Page
     */
    public function register()
    {
        if ($this->get('parent')) {
            add_submenu_page(
                $this->get('parent'),
                $this->get('title'),
                $this->get('name'),
                $this->get('capability'),
                $this->get('slug'),
                [$this, 'render']
            );
        } else {
            add_menu_page(
                $this->get('title'),
                $this->get('name'),
                $this->get('capability'),
                $this->get('slug'),
                [$this, 'render'],
                $this->get('icon'),
                $this->get('position')
            );
        }
    }

    /**
     * Render the Page
     */
    public function render()
    {
        $tabs = [];

        foreach ($this->tabs as $tab) {
            $tabs[] = $tab->getTwigArgs();
        }

        $args = [
            'prefix' => $this->get('prefix'),
            'title' => $this->get('title'),
            'tabs' => $tabs,
        ];

        echo $this->twig('templates/admin-page', $args);
    }

    /**
     * Get Class props
     *
     * @return array
     */
    protected function props(): array
    {
        return [
            'prefix' => [
                'required' => true,
            ],
            'name' => [
                'required' => true,
            ],
            'slug' => [
                'default' => function ($data) {
                    return sanitize_key(str_replace(' ', '-', $data['name']));
                },
            ],
            'title' => [
                'default' => function ($data) {
                    return $data['name'];
                },
            ],
            'header' => [
                'default' => function ($data) {
                    return $data['name'];
                },
            ],
            'parent' => [
                'default' => null,
            ],
            'position' => [
                'type' => 'int',
                'default' => 0,
            ],
            'icon' => [
                'default' => 'dashicons-update',
            ],
            'capability' => [
                'default' => 'manage_options'
            ],
        ];
    }
}
