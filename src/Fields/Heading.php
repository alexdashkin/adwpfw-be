<?php

namespace AlexDashkin\Adwpfw\Fields;

/**
 * Heading. Used on Admin Pages only.
 */
class Heading extends Field
{
    /**
     * Constructor.
     *
     * @param array $data {
     * @type string $tpl Template name. Default 'heading'.
     * @type string $text Heading text. Required.
     * }
     *
     * @throws \AlexDashkin\Adwpfw\Exceptions\AdwpfwException
     */
    public function __construct(array $data, array $props = [])
    {
        $defaults = [
            'id' => [
                'default' => uniqid(),
            ],
            'layout' => [
                'default' => null,
            ],
            'form' => [
                'default' => null,
            ],
            'tpl' => [
                'default' => 'heading',
            ],
            'text' => [
                'required' => true,
            ],
        ];

        parent::__construct($data, array_merge($defaults, $props));
    }

    public function sanitize($value)
    {
        return sanitize_text_field($value);
    }
}
