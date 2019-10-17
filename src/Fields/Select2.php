<?php

namespace AlexDashkin\Adwpfw\Fields;

use AlexDashkin\Adwpfw\Modules\Basic\Helpers;

/**
 * Select2 Field.
 */
class Select2 extends Select
{
    /**
     * Constructor
     *
     * @param array $data {
     * @type string $id Required.
     * @type string $layout Parent template to extend. Required.
     * @type string $form Form ID (slug). Used to distinguish multiple forms on one page. Required.
     * @type string $tpl Template name. Default 'select2'.
     * @type string $class CSS Class(es) for the control. Default 'adwpfw-form-control'.
     * @type string $label Label.
     * @type string $desc Description.
     * @type string $placeholder Placeholder. Default '--- Select ---'.
     * @type array $options Options.
     * @type bool $multiple Default false.
     * @type string $ajax_action Ajax Action to populate options.
     * @type int $min_chars Minimum query length to start search.
     * @type callable $label_cb Callback. Callback to build labels for values.
     * }
     *
     * @throws \AlexDashkin\Adwpfw\Exceptions\AdwpfwException
     */
    public function __construct(array $data, array $props = [])
    {
        $defaults = [
            'tpl' => [
                'default' => 'select2',
            ],
            'options' => [
                'type' => 'array',
                'default' => [],
            ],
            'ajax_action' => [
                'default' => null,
            ],
            'min_chars' => [
                'type' => 'int',
                'default' => 3,
            ],
            'label_cb' => [
                'type' => 'callable',
                'default' => null,
            ],
        ];

        parent::__construct($data, array_merge($defaults, $props));
    }

    public function getArgs(array $values)
    {
        $data = $this->data;

        $args = parent::getArgs($values);

        $value = isset($values[$data['id']]) ? $values[$data['id']] : null;

        $valueArr = $data['multiple'] ? (array)$value : [$value];

        foreach ($valueArr as $item) {
            if (!Helpers::arraySearch($args['options'], ['value' => $item])) {
                $args['options'][] = [
                    'label' => !empty($data['label_cb']) ? $data['label_cb']($item) : $item,
                    'value' => $item,
                    'selected' => 'selected',
                ];
            }
        }

        return $args;
    }
}
