<?php

namespace AlexDashkin\Adwpfw\Fields;

/**
 * Password Field.
 */
class Password extends Text
{
    /**
     * Constructor.
     *
     * @param array $data {
     * @type string $id Required.
     * @type string $label Field Label. Required.
     * @type string $desc Field Description
     * }
     * @throws \AlexDashkin\Adwpfw\Exceptions\AdwpfwException
     */
    public function __construct(array $data, array $props = [])
    {
        $defaults = [
            'tpl' => [
                'default' => 'password',
            ],
        ];

        parent::__construct($data, array_merge($defaults, $props));
    }
}
