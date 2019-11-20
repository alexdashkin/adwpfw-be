<?php

namespace AlexDashkin\Adwpfw\Fields;

use AlexDashkin\Adwpfw\App;
use AlexDashkin\Adwpfw\Exceptions\AdwpfwException;

/**
 * Heading. Used on Admin Pages only.
 */
class Heading extends Field
{
    /**
     * Constructor.
     *
     * @param App $app
     * @param array $data {
     * @type string $tpl Template name. Default 'heading'.
     * @type string $text Heading text. Required.
     * }
     * @param array $props
     *
     * @throws AdwpfwException
     */
    public function __construct(App $app, array $data, array $props = [])
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

        parent::__construct($app, $data, array_merge($defaults, $props));
    }

    public function sanitize($value)
    {
        return sanitize_text_field($value);
    }
}
