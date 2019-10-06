<?php

namespace AlexDashkin\Adwpfw\Modules;

use AlexDashkin\Adwpfw\App;
use AlexDashkin\Adwpfw\Common\Helpers;

/**
 * Admin Settings pages
 */
class Menu extends \AlexDashkin\Adwpfw\Common\Base
{
    private $menus = [];

    /**
     * @var \AlexDashkin\Adwpfw\Common\Utils
     */
    private $utils;

    /**
     * Constructor
     *
     * @param App $app
     */
    public function __construct(App $app)
    {
        parent::__construct($app);
        $this->utils = $this->m('Utils');
    }

    public function run()
    {
        add_action('admin_menu', [$this, 'registerMenu']);

        $this->m('Admin\Ajax')->addAction([
            'id' => 'save',
            'fields' => [
                'form' => [
                    'type' => 'form',
                    'required' => true,
                ],
            ],
            'callback' => [$this, 'ajaxSave'],
        ]);
    }

    /**
     * Add a Settings Page to the left WP Admin Menu
     *
     * @param array $menu {
     * @type string $parent Parent Menu slug. If specified, a sub menu will be added.
     * @type string $id Menu slug. Defaults to sanitized Title.
     * @type string $prefix Prefix for slugs. Default config prefix.
     * @type string $name Text for the left Menu. Default "Settings".
     * @type string $title Text for the <title> tag. Defaults to $name.
     * @type string $header Page header. Defaults to $name.
     * @type string $icon The dash icon name for the bar
     * @type int $position Position in the Menu. Default 100.
     * @type string $option WP Option name to store the data (if $values isn't passed by reference)
     * @type array $values Data to fill out the form and to be modified (normally passed by reference)
     * @type string $capability Capability level to see the Page. Default "administrator"
     * @type array $tabs Tabs: {
     * @type string $name Tab Name
     * @type bool $form Whether to wrap content with the <form> tag
     * @type array $options Tab fields
     * @type array $buttins Buttons at the bottom of the Tab
     * }
     * @type string $callback Render function
     * }
     */
    public function addMenu(array $menu)
    {
        $menu = array_merge([
            'parent' => null,
            'id' => '',
            'prefix' => $this->config['prefix'],
            'name' => 'Settings',
            'title' => '',
            'header' => '',
            'icon' => '',
            'position' => 100,
            'option' => '',
            'values' => [],
            'capability' => 'administrator',
            'tabs' => [],
            'callback' => null,
        ], $menu);

        foreach ($menu['tabs'] as &$tab) {
            $tab = array_merge([
                'name' => 'Tab',
                'form' => false,
                'options' => [],
                'buttons' => [],
            ], $tab);
        }

        $menu['title'] = $menu['title'] ?: $menu['name'];
        $menu['header'] = $menu['header'] ?: $menu['name'];
        $menu['id'] = $menu['id'] ?: sanitize_title($menu['title']);

        $this->menus[] = $menu;
    }

    /**
     * Add multiple Settings pages
     *
     * @param array $menus
     *
     * @see Menu::addMenu()
     */
    public function addMenus(array $menus)
    {
        foreach ($menus as $menu) {
            $this->addMenu($menu);
        }
    }

    /**
     * Add a Settings Sub Page to the WP Admin Left Bar
     *
     * @param array $menu {
     * @type string $parent Parent Menu slug
     * @type string $id Menu slug
     * @type string $prefix
     * @type string $name Text to be displayed on the Bar
     * @type string $title Text to be displayed on top as heading
     * @type string $icon The dash icon name for the bar
     * @type int $position Position of the menu
     * @type array $values Data to fill out the form and to be modified (normally passed by reference)
     * @type string $option WP Option name to store the data (if $values isn't passed by reference)
     * @type array $args Settings Page tabs [name, form, options, buttons]
     * @type string $capability
     * @type string $callback Render function
     * }
     */
    public function addSubMenu(array $menu)
    {
        $menu = array_merge([
            'parent' => '',
            'id' => '',
            'prefix' => $this->config['prefix'],
            'name' => 'Settings',
            'title' => 'Settings',
            'values' => [],
            'option' => '',
            'tabs' => [],
            'capability' => 'administrator',
            'callback' => null,
        ], $menu);

        foreach ($menu['tabs'] as &$tab) {
            $tab = array_merge([
                'name' => 'Tab',
                'form' => false,
                'options' => [],
                'buttons' => [],
            ], $tab);
        }

        $menu['id'] = $menu['id'] ?: sanitize_title($menu['title']);

        $this->submenus[] = $menu;
    }

    /**
     * Add multiple Settings Sub pages
     *
     * @param array $menus
     *
     * @see Menu::addMenu()
     */
    public function addSubMenus(array $menus)
    {
        foreach ($menus as $menu) {
            $this->addSubMenu($menu);
        }
    }

    public function registerMenu()
    {
        // Top menus
        foreach ($this->menus as $menu) {
            $callback = $menu['callback'] ?: function () use ($menu) {
                $this->renderMenu($menu['id']);
            };

            add_menu_page( // todo use returned hook_suffix
                $menu['title'],
                $menu['name'],
                $menu['capability'],
                $menu['id'],
                $callback,
                $menu['icon'],
                $menu['position']
            );
        }

        // Sub Menus
        foreach ($this->submenus as $menu) {
            $callback = $menu['callback'] ?: function () use ($menu) {
                $this->renderSubMenu($menu['id']);
            };

            add_submenu_page(
                $menu['parent'],
                $menu['title'],
                $menu['name'],
                $menu['capability'],
                $menu['id'],
                $callback
            );
        }
    }

    public function renderMenu($id)
    {
        $menu = Helpers::arraySearch($this->menus, ['id' => $id], true);
        $this->render($menu);
    }

    public function renderSubMenu($id)
    {
        $menu = Helpers::arraySearch($this->submenus, ['id' => $id], true);
        $this->render($menu);
    }

    private function render($menu)
    {
        $values = $menu['values'];

        foreach ($menu['tabs'] as &$tab) {
            $tabContent = '';

            foreach ($tab['options'] as $option) {

                // Read value
                if (isset($option['id'], $values[$option['id']])) {
                    $value = $values[$option['id']];
                } else {
                    $value = isset($option['default']) ? $option['default'] : '';
                }

                $option['value'] = $value;

                if (empty($option['classes'])) {
                    $option['classes'] = '';
                }

                switch ($option['type']) {
                    case 'checkbox':
                        $option['checked'] = !empty($value) ? ' checked ' : '';
                        $tabContent .= $this->twig('checkbox', $option);
                        break;

                    case 'radio':
                        $option['items'] = '';
                        foreach ($option['options'] as $item) {
                            $item['id'] = $option['id'];
                            $item['checked'] = ($value == $item['value']) ? ' checked' : '';
                            $option['items'] .= $this->twig('radio_item', $item);
                        }
                        $tabContent .= $this->twig('radio', $option);
                        break;

                    case 'select':
                    case 'actions':
                        $items = [];

                        $placeholder = !empty($option['placeholder']) ? $option['placeholder'] : '--- Select ---';

                        $items[] = [
                            'label' => $placeholder,
                            'value' => '',
                            'selected' => '',
                        ];

                        $options = !empty($option['options']) ? $option['options'] : [];
                        $multiple = !empty($option['multiple']);

                        foreach ($options as $val => $label) {
                            $selected = $multiple ? in_array($val, (array)$value) : $val == $value;

                            $items[] = [
                                'label' => $label,
                                'value' => $val,
                                'selected' => $selected ? ' selected ' : '',
                            ];
                        }

                        $option['items'] = $items;

                        $tabContent .= $this->twig($option['type'], $option);
                        break;

                    case 'select2':
                        $items = [];

                        $placeholder = !empty($option['placeholder']) ? $option['placeholder'] : '--- Select ---';

                        $items[] = [
                            'label' => $placeholder,
                            'value' => '',
                            'selected' => '',
                        ];

                        $options = !empty($option['options']) ? $option['options'] : [];
                        $multiple = !empty($option['multiple']);

                        foreach ($options as $val => $label) {
                            $selected = $multiple ? in_array($val, (array)$value) : $val == $value;

                            $items[] = [
                                'label' => $label,
                                'value' => $val,
                                'selected' => $selected ? ' selected ' : '',
                            ];
                        }

                        $valueArr = $multiple ? (array)$value : [$value];

                        foreach ($valueArr as $item) {
                            if (!Helpers::arraySearch($items, ['value' => $item])) {
                                $items[] = [
                                    'label' => !empty($option['label_cb']) ? $option['label_cb']($item) : $item,
                                    'value' => $item,
                                    'selected' => 'selected',
                                ];
                            }
                        }

                        $option['items'] = $items;

                        $tabContent .= $this->twig('select2', $option);
                        break;

                    case 'callback':
                        $tabContent .= $option['callback']();
                        break;

                    default:
                        $tabContent .= $this->twig($option['type'], $option);
                        break;
                }
            }

            $tab['content'] = $tabContent;
        }

        $args = [
            'id' => $menu['id'],
            'title' => $menu['title'],
            'tabs' => $menu['tabs'],
        ];

        echo $this->twig('index', $args);
    }

    public function ajaxSave($data)
    {
        $formData = $data['form'][$this->config['prefix']];

        if (empty($formData['menu_slug'])) {
            return $this->utils->returnError('Menu id is empty');
        }

        $menus = array_merge($this->menus, $this->submenus);

        $menu = Helpers::arraySearch($menus, ['id' => $formData['menu_slug']], true);

        if (empty($menu)) {
            return $this->utils->returnError('Menu ' . $formData['menu_slug'] . ' not found');
        }

        $values =& $menu['values']; // todo bwc
        $changed = false;

        foreach ($menu['tabs'] as $tab) {
            if (empty($tab['form'])) {
                continue;
            }

            foreach ($tab['options'] as $option) {
                if (empty($option['id']) || (isset($option['store']) && !$option['store']) || !isset($formData[$option['id']])) {
                    continue;
                }

                $value = Helpers::trim($formData[$option['id']]);

                $isset = isset($values[$option['id']]);
                if (!$isset || ($isset && $values[$option['id']] != $value)) {
                    $changed = true;
                }
                $values[$option['id']] = $value;
            }

            $message = !$changed ? 'Nothing changed' : 'Saved!';
        }

        if ($changed && $menu['option']) {
            update_option($menu['option'], $values);
        }

        do_action('adwpfw_settings_saved', $menu, $values);

        return $this->utils->returnSuccess($message);
    }

    private function twig($name, $args = [])
    {
        return $this->utils->renderTwig($name, $args);
    }
}
