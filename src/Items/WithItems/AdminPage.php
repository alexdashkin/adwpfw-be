<?php

namespace AlexDashkin\Adwpfw\Items\WithItems;

use AlexDashkin\Adwpfw\App;

/**
 * Menu Page
 */
class AdminPage extends ItemWithItems
{
    /**
     * Constructor
     *
     * @param array $data {
     * @type string $name Text for the left Menu. Required.
     * @type string $title Text for the <title> tag. Defaults to $name.
     * @type string $header Page header without markup. Defaults to $name.
     * @type string $parent Parent Menu slug. If specified, a sub menu will be added.
     * @type int $position Position in the Menu. Default 0.
     * @type string $icon The dash icon name for the bar. Default 'dashicons-update'
     * @type string $capability Minimum capability. Default 'manage_options'.
     * @type array $tabs Tabs: {
     * @type string $title Tab Title.
     * @type array $fields Tab fields.
     * }
     * @throws \AlexDashkin\Adwpfw\Exceptions\AdwpfwException
     */
    public function __construct(array $data, App $app)
    {
        $props = [
            'id' => [
                'default' => $this->getDefaultId($data['name']),
            ],
            'name' => [
                'required' => true,
            ],
            'title' => [
                'default' => $data['name'],
            ],
            'header' => [
                'default' => $data['name'],
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
            'tabs' => [
                'type' => 'array',
                'def' => [
                    'title' => 'Tab',
                    'form' => false,
                    'fields' => [],
                ],
            ],
        ];

        parent::__construct($data, $app, $props);

        foreach ($this->data['tabs'] as $tab) {
            $this->add($tab, $app);
        }
    }

    /**
     * Add Tab.
     *
     * @param array $data Data passed to the Tab Constructor.
     * @param App $app
     *
     * @throws \AlexDashkin\Adwpfw\Exceptions\AdwpfwException
     */
    public function add(array $data, App $app)
    {
        $this->items[] = new AdminPageTab($data, $app);
    }

    /**
     * Register the Page.
     */
    public function register()
    {
        $data = $this->data;

        if ($data['parent']) {
            $this->data['id'] = add_submenu_page(
                $data['parent'],
                $data['title'],
                $data['name'],
                $data['capability'],
                $data['id'],
                [$this, 'render']
            );

        } else {
            $this->data['id'] = add_menu_page(
                $data['title'],
                $data['name'],
                $data['capability'],
                $data['id'],
                [$this, 'render'],
                $data['icon'],
                $data['position']
            );
        }
    }

    /**
     * Render the Page.
     */
    public function render()
    {
        foreach ($this->items as $tab) {
            $tabs[] = $tab->getArgs();
        }

        $args = [
            'title' => $this->data['title'],
            'tabs' => $tabs,
        ];

        try {
            echo $this->m('Twig')->renderFile('admin-page', $args);

        } catch (\Exception $e) {
            $message = $e->getMessage();
            $this->log($message);
            echo 'Page render error: ' . $message;
        }
    }

    /**
     * Find tab by ID
     *
     * @param string $id
     * @return AdminPageTab|null
     */
    public function findTab($id)
    {
        foreach ($this->items as $tab) {
            if ($tab->data['id'] === $id) {
                return $tab;
            }
        }

        return null;
    }
}
