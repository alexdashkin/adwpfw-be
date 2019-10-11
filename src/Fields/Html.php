<?php

namespace AlexDashkin\Adwpfw\Fields;

/**
 * Arbitrary HTML. Used on Admin Pages only.
 */
class Html extends Field
{
    /**
     * Constructor.
     *
     * @param array $data {
     * @type string $content Required.
     * }
     * @throws \AlexDashkin\Adwpfw\Exceptions\AdwpfwException
     */
    public function __construct(array $data, array $props = [])
    {
        $defaults = [
            'content' => [
                'required' => true,
            ],
            'tpl' => [
                'default' => 'html',
            ],
        ];

        parent::__construct($data, array_merge($defaults, $props));
    }
}
