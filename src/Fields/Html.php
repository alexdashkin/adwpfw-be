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
     * @type callable $callback Filters Twig args, gets $value, returns fields[] as key/value pairs. Default empty.
     * }
     * @param array $props
     *
     * @throws AdwpfwException
     */
    public function __construct(App $app, array $data, array $props = [])
    {
        $defaults = [
            'id' => [
                'type' => 'string',
                'default' => uniqid(),
            ],
            'layout' => [
                'type' => 'string',
                'default' => null,
            ],
            'form' => [
                'default' => null,
            ],
            'tpl' => [
                'default' => 'html',
            ],
            'content' => [
                'type' => 'string',
                'required' => true,
            ],
        ];

        parent::__construct($app, $data, array_merge($defaults, $props));
    }
}
