<?php

namespace AlexDashkin\Adwpfw\Modules;

use AlexDashkin\Adwpfw\Helpers;

/**
 * Admin Page with settings
 */
class AdminPage extends Module
{
    /**
     * @var AdminPageTab[]
     */
    protected $tabs = [];

    /**
     * Init Module
     */
    public function init()
    {
        $this->addHook('admin_menu', [$this, 'register']);
    }

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
     * Register the Page
     */
    public function register()
    {
        if ($parent = $this->getProp('parent')) {
            $suffix = add_submenu_page(
                $parent,
                $this->getProp('title'),
                $this->getProp('name'),
                $this->getProp('capability'),
                $this->getProp('slug'),
                [$this, 'render']
            );
        } else {
            $suffix = add_menu_page(
                $this->getProp('title'),
                $this->getProp('name'),
                $this->getProp('capability'),
                $this->getProp('slug'),
                [$this, 'render'],
                $this->getProp('icon'),
                $this->getProp('position')
            );
        }

        $this->setProp('suffix', $suffix);
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

        $currentTab = $this->getCurrentTab();

        foreach ($this->tabs as $tab) {
            $tabs[] = [
                'title' => $tab->getProp('title'),
                'link' => add_query_arg('tab', $tab->getProp('slug')),
                'current' => $tab === $currentTab,
            ];
        }

        $args = [
            'title' => $this->getProp('title'),
            'tabs' => $tabs,
            'content' => $currentTab->render(),
        ];

        echo Helpers::render('layouts/admin-page', $args);
    }

    /**
     * Get Current Tab
     *
     * @return AdminPageTab
     */
    private function getCurrentTab(): AdminPageTab
    {
        if (!empty($_GET['tab'])) {
            $tabName = sanitize_key($_GET['tab']);
            foreach ($this->tabs as $tab) {
                if ($tab->getProp('slug') === $tabName) {
                    return $tab;
                }
            }
        }

        return $this->tabs[0];
    }

    /**
     * Get prop definitions
     *
     * @return array
     */
    protected function getPropDefs(): array
    {
        return [
            'name' => [
                'type' => 'string',
                'required' => true,
            ],
            'baseFile' => [
                'type' => 'string',
//                'required' => true,
            ],
            'title' => [
                'type' => 'string',
                'default' => function () {
                    return $this->getProp('name');
                },
            ],
            'slug' => [
                'type' => 'string',
                'default' => function () {
                    return sanitize_key(str_replace(' ', '-', $this->getProp('name')));
                },
            ],
            'icon' => [
                'type' => 'string',
                'default' => 'dashicons-update',
            ],
            'position' => [
                'type' => 'int',
                'default' => 100,
            ],
            'capability' => [
                'type' => 'string',
                'default' => 'administrator',
            ],
            'parent' => [
                'type' => 'string',
                'default' => '',
            ],
        ];
    }
}
