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
     * @type string $text Heading. Required.
     * }
     * @throws \AlexDashkin\Adwpfw\Exceptions\AdwpfwException
     */
    public function __construct(array $data, array $props = [])
    {
        $defaults = [
            'tpl' => [
                'default' => 'heading',
            ],
            'text' => [
                'required' => true,
            ],
        ];

        parent::__construct($data, array_merge($props, $defaults));
    }

    public function sanitize($value)
    {
        return sanitize_text_field($value);
    }
}
