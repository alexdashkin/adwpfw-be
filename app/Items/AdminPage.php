<?php

namespace AlexDashkin\Adwpfw\Items;

use AlexDashkin\Adwpfw\Abstracts\Module;
use AlexDashkin\Adwpfw\App;

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

        App::get(
            'admin_ajax',
            [
                'prefix' => $this->get('prefix'),
                'action' => 'save',
                'fields' => [
                    'form' => [
                        'type' => 'form',
                        'required' => true,
                    ],
                ],
                'callback' => [$this, 'save'],
            ]
        );
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
     * Save the posted data
     *
     * @param array $request
     * @return array
     */
    public function save(array $request)
    {
        $helpers = App::get('helpers');
        $form = $request['form'];

        if (empty($form[$this->get('prefix')])) {
            return $helpers->returnError('Form is empty');
        }

        $data = $form[$this->get('prefix')];

        $saved = false;

        foreach ($this->tabs as $tab) {
            $saved = $saved || $tab->save($data);
        }

        return $saved ? $helpers->returnSuccess('Saved') : $helpers->returnError('Nothing to save');
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
