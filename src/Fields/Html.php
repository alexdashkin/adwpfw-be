<?php

namespace AlexDashkin\Adwpfw\Fields;

use AlexDashkin\Adwpfw\App;
use AlexDashkin\Adwpfw\Exceptions\AdwpfwException;

/**
 * Arbitrary HTML. Used on Admin Pages only.
 */
class Html extends Field
{
    /**
     * Constructor.
     *
     * @param App $app
     * @param array $data {
     * @type string $tpl Template name. Default 'html'.
     * @type string $content Required.
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
                'default' => 'html',
            ],
            'content' => [
                'required' => true,
            ],
        ];

        parent::__construct($app, $data, array_merge($defaults, $props));
    }
}
