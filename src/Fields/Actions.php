<?php

namespace AlexDashkin\Adwpfw\Fields;

/**
 * Actions Selector. Used on Admin Pages only.
 */
class Actions extends Field
{
    /**
     * Constructor.
     *
     * @param array $data {
     * @type string $id Required.
     * @type array $options Actions list ['label', 'value']. Required.
     * }
     * @throws \AlexDashkin\Adwpfw\Exceptions\AdwpfwException
     */
    public function __construct(array $data, array $props = [])
    {
        $defaults = [
            'tpl' => [
                'default' => 'actions',
            ],
            'options' => [
                'type' => 'array',
                'required' => true,
                'def' => [
                    'value' => '',
                    'label' => 'Option',
                ],
            ],
        ];

        parent::__construct($data, array_merge($props, $defaults));
    }
}
