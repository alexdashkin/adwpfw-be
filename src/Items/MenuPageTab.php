<?php

namespace AlexDashkin\Adwpfw\Items;

use AlexDashkin\Adwpfw\App;

/**
 * Menu Page Tab
 */
class MenuPageTab extends Item
{
    private $fields;

    /**
     * Constructor
     *
     * @param array $data {
     * @type string $title
     * @type bool $form Whether to wrap content with the <form> tag
     * @type array $options Tab fields
     * @type array $buttons Buttons at the bottom of the Tab
     * }
     */
    public function __construct(array $data, App $app)
    {
        $this->props = [
            'title' => [
                'default' => 'Tab',
            ],
            'form' => [
                'type' => 'bool',
                'default' => false,
            ],
            'fields' => [
                'type' => 'array',
                'def' => [
                    'id' => 'field',
                    'type' => 'text',
                    'name' => 'Field',
                ],
            ],
        ];

        parent::__construct($data, $app);
    }

    public function render()
    {
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

        $args = [
            'id' => $menu['id'],
            'title' => $menu['title'],
            'tabs' => $menu['tabs'],
        ];

        echo $this->twig('index', $args);
    }
}
