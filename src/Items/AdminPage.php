<?php

namespace AlexDashkin\Adwpfw\Items;

use AlexDashkin\Adwpfw\App;

/**
 * Menu Page
 */
class AdminPage extends Item
{
    /**
     * @var AdminPageTab[]
     */
    private $tabs;

    /**
     * Constructor
     *
     * @param array $data {
     * @type string $name Text for the left Menu. Required.
     * @type string $title Text for the <title> tag. Defaults to $name.
     * @type string $header Page header without markup. Defaults to $name.
     * @type string $parent Parent Menu slug. If specified, a sub menu will be added.
     * @type int $position Position in the Menu. Default 0.
     * @type string $icon The dash icon name for the bar
     * @type string $capability Capability level to see the Page. Default "manage_options".
     * @type array $tabs Tabs: {
     * @type string $title Tab Title
     * @type bool $form Whether to wrap content with the <form> tag
     * @type array $fields Tab fields
     * @type array $buttons Buttons at the bottom of the Tab
     * }
     */
    public function __construct(array $data, App $app)
    {
        $this->props = [
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
            /*            'values' => [
                            'type' => 'array',
                            'default' => [],
                        ],*/
            /*            'option' => [
                            'default' => null,
                        ],*/
            'capability' => [
                'default' => 'manage_options'
            ],
            'tabs' => [
                'type' => 'array',
                'def' => [
                    'title' => 'Tab',
                    'form' => false,
                    'fields' => [],
                    'buttons' => [],
                ],
            ],
        ];

        parent::__construct($data, $app);

        foreach ($this->data['tabs'] as $tab) {
            $this->tabs[] = new AdminPageTab($tab, $app);
        }
    }

    public function register()
    {
        $data = $this->data;

        if ($data['parent']) {
            add_submenu_page(
                $data['parent'],
                $data['title'],
                $data['name'],
                $data['capability'],
                $data['id'],
                [$this, 'render']
            );

        } else {
            add_menu_page( // todo use returned hook_suffix
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

    public function render()
    {
        foreach ($this->tabs as $tab) {
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

    public function findTab($slug)
    {
        foreach ($this->tabs as $tab) {
            if ($tab->data['slug'] === $slug) {
                return $tab;
            }
        }

        return null;
    }

    /**
     * Return Success array
     *
     * @param string $message
     * @param array $data Data to return as JSON
     * @param bool $echo Whether to echo Response right away without returning
     * @return array
     */
    private function success($message = '', $data = [], $echo = false)
    {
        return $this->m('Utils')->returnSuccess($message, $data, $echo);
    }

    /**
     * Return Error array
     *
     * @param string $message
     * @param bool $echo Whether to echo Response right away without returning
     * @return array
     */
    private function error($message = '', $echo = false)
    {
        return $this->m('Utils')->returnError($message, $echo);
    }
}
