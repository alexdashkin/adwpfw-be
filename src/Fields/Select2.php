<?php

namespace AlexDashkin\Adwpfw\Fields;

use AlexDashkin\Adwpfw\App;
use AlexDashkin\Adwpfw\Exceptions\AdwpfwException;
use AlexDashkin\Adwpfw\Modules\Helpers;

/**
 * Select2 Field.
 */
class Select2 extends Select
{
    /**
     * Constructor
     *
     * @param App $app
     * @param array $data {
     * @type string $id Required.
     * @type string $layout Parent template to extend. Required.
     * @type string $form Form ID (slug). Used to distinguish multiple forms on one page. Required.
     * @type string $tpl Template name. Default 'select2'.
     * @type string $class CSS Class(es) for the control. Default 'adwpfw-form-control'.
     * @type string $label Label. Default empty.
     * @type string $desc Description. Default empty.
     * @type mixed $default Default value. Default empty.
     * @type callable $callback Filters Twig args, gets $value, returns fields[] as key/value pairs. Default empty.
     * @type string $placeholder Placeholder. Default '--- Select ---'.
     * @type array $options Options. Default [].
     * @type bool $multiple Default false.
     * @type string $ajax_action Ajax Action to populate options. Default empty.
     * @type int $min_chars Minimum query length to start search. Default 3.
     * @type callable $label_cb Callback. Callback to build labels for values. Default empty.
     * }
     * @param array $props
     *
     * @throws AdwpfwException
     */
    public function __construct(App $app, array $data, array $props = [])
    {
        $defaults = [
            'tpl' => [
                'type' => 'string',
                'default' => 'select2',
            ],
            'options' => [
                'type' => 'array',
                'default' => [],
            ],
            'ajax_action' => [
                'type' => 'string',
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

        parent::__construct($app, $data, array_merge($defaults, $props));
    }

    /**
     * Get Twig args to render the Field.
     *
     * @param array $values
     * @return array
     */
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
