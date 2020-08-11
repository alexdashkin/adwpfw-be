<?php

namespace AlexDashkin\Adwpfw\Modules;

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

        $this->m( // todo it's impossible to add more than one AdminPage because AJAX action name is the same for all pages
            'admin_ajax',
            [
                'prefix' => $this->gp('prefix'),
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
        if ($this->gp('parent')) {
            add_submenu_page(
                $this->gp('parent'),
                $this->gp('title'),
                $this->gp('name'),
                $this->gp('capability'),
                $this->gp('slug'),
                [$this, 'render']
            );
        } else {
            add_menu_page(
                $this->gp('title'),
                $this->gp('name'),
                $this->gp('capability'),
                $this->gp('slug'),
                [$this, 'render'],
                $this->gp('icon'),
                $this->gp('position')
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
            $tabs[] = $tab->getTwigArgs();
        }

        $args = [
            'prefix' => $this->gp('prefix'),
            'title' => $this->gp('title'),
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
    public function save(array $request): array
    {
        $helpers = $this->m('helpers');
        $form = $request['form'];

        if (empty($form[$this->gp('prefix')])) {
            return $helpers->returnError('Form is empty');
        }

        $data = $form[$this->gp('prefix')];

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
    protected function getInitialPropDefs(): array
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
