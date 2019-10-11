<?php

namespace AlexDashkin\Adwpfw\Fields;

/**
 * Button.
 */
class Button extends Field
{
    /**
     * Constructor.
     *
     * @param array $data {
     * @type string $id Required.
     * @type string $caption Button Caption. Required.
     * @type string $desc Button Description.
     * }
     * @throws \AlexDashkin\Adwpfw\Exceptions\AdwpfwException
     */
    public function __construct(array $data, array $props = [])
    {
        $defaults = [
            'caption' => [
                'required' => true,
            ],
            'tpl' => [
                'default' => 'button',
            ],
        ];

        parent::__construct($data, array_merge($defaults, $props));
    }
}
