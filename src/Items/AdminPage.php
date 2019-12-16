<?php

namespace AlexDashkin\Adwpfw\Items;

use AlexDashkin\Adwpfw\App;
use AlexDashkin\Adwpfw\Exceptions\AdwpfwException;

/**
 * Admin Page with Settings.
 */
class AdminPage extends ItemWithItems
{
    /**
     * @var AdminPageTab[]
     */
    protected $items = [];

    /**
     * Constructor
     *
     * @param App $app
     * @param array $data {
     * @type string $id ID w/o prefix. Defaults to sanitized $name.
     * @type string $name Text for the left Menu. Required.
     * @type string $title Text for the <title> tag. Defaults to $name.
     * @type string $header Page header without markup. Defaults to $name.
     * @type string $parent Parent Menu slug. If specified, a sub menu will be added. Default empty.
     * @type int $position Position in the Menu. Default 0.
     * @type string $icon The dash icon name for the bar. Default 'dashicons-update'.
     * @type string $capability Minimum capability. Default 'manage_options'.
     * @type array $tabs Tabs: {
     * @type string $title Tab Title.
     * @type array $fields Tab fields.
     * }}
     *
     * @throws AdwpfwException
     */
    public function __construct(App $app, array $data)
    {
        $props = [
            'id' => [
                'default' => sanitize_key(str_replace(' ', '_', $data['name'])),
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
                'default' => [],
                'def' => [
                    'title' => 'Tab',
                    'form' => false,
                    'fields' => [],
                ],
            ],
        ];

        parent::__construct($app, $data, $props);

        foreach ($this->data['tabs'] as $tab) {
            $this->add($tab);
        }
    }

    /**
     * Add Tab.
     *
     * @param App $app
     * @param array $data Data passed to the Tab Constructor.
     *
     * @throws AdwpfwException
     */
    public function add(array $data) // todo allow fields without tabs, add tabs/fields via add(), not as array
    {
        $this->items[] = new AdminPageTab($this->app, $data);
    }

    /**
     * Register the Page.
     */
    public function register()
    {
        $data = $this->data;

        if ($data['parent']) {
            $this->data['hook_suffix'] = add_submenu_page(
                $data['parent'],
                $data['title'],
                $data['name'],
                $data['capability'],
                $data['id'],
                [$this, 'render']
            );

        } else {
            $this->data['hook_suffix'] = add_menu_page(
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
        $tabs = [];

        foreach ($this->items as $tab) {
            $tabs[] = $tab->getArgs();
        }

        $args = [
            'title' => $this->data['title'],
            'tabs' => $tabs,
        ];

        echo $this->m('Twig')->renderFile('templates/admin-page', $args);
    }

    /**
     * Save Settings on submit.
     *
     * @param array $data
     */
    public function save(array $data)
    {
        foreach ($this->items as $item) {
            $item->save($data);
        }
    }
}
