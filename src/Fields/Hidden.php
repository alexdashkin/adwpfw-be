<?php

namespace AlexDashkin\Adwpfw\Fields;

/**
 * Hidden Field.
 */
class Hidden extends Text
{
    /**
     * Constructor
     *
     * @param array $data {
     * @type string $id Required.
     * }
     * @throws \AlexDashkin\Adwpfw\Exceptions\AdwpfwException
     */
    public function __construct(array $data, array $props = [])
    {
        $defaults = [
            'label' => [
                'default' => null,
            ],
            'tpl' => [
                'default' => 'hidden',
            ],
        ];

        parent::__construct($data, array_merge($defaults, $props));
    }
}
