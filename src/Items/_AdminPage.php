<?php

namespace AlexDashkin\Adwpfw\Items;

use AlexDashkin\Adwpfw\App;

/**
 * Menu Page
 */
class AdminPage extends Item
{
    private $tabs;

    /**
     * Constructor
     *
     * @param array $data {
     * @type string $name Text for the left Menu. Required.
     * @type string $slug Defaults to sanitized Title.
     * @type string $title Text for the <title> tag. Defaults to $name.
     * @type string $header Page header without markup. Defaults to $name.
     * @type string $parent Parent Menu slug. If specified, a sub menu will be added.
     * @type int $position Position in the Menu. Default 0.
     * @type string $icon The dash icon name for the bar
     * @type array $values Data to fill out the form and to be modified (normally passed by reference)
     * @type string $option WP Option name to store the data (if $values isn't passed by reference)
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
            'slug' => [
                'default' => $this->getDefaultSlug($data['name']),
            ],
            'title' => [
                'default' => $data['name'],
            ],
            'header' => [
                'default' => $data['name'],
            ],
            'parent' => [
                'type' => 'int',
                'default' => 0,
            ],
            'position' => [
                'type' => 'int',
                'default' => 0,
            ],
            'icon' => [
                'default' => 'dashicons-update',
            ],
            'values' => [
                'type' => 'array',
                'default' => [],
            ],
            'option' => [
                'default' => null,
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
                    'buttons' => [],
                ],
            ],
        ];

        parent::__construct($data, $app);

        foreach ($this->data['tabs'] as $tab) {
            $this->tabs[] = new AdminPageTab($tab, $app);
        }
    }

    public function render()
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
}
