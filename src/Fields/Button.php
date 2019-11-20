<?php

namespace AlexDashkin\Adwpfw\Fields;

use AlexDashkin\Adwpfw\App;
use AlexDashkin\Adwpfw\Exceptions\AdwpfwException;

/**
 * Button.
 */
class Button extends Field
{
    /**
     * Constructor.
     *
     * @param App $app
     * @param array $data {
     * @type string $id Required.
     * @type string $layout Parent template to extend. Required.
     * @type string $form Form ID (slug). Used to distinguish multiple forms on one page. Required.
     * @type string $tpl Template name. Default 'button'.
     * @type string $class CSS Class(es) for the control. Default empty.
     * @type string $desc Description.
     * @type string $caption Button Caption. Required.
     * }
     * @param array $props
     *
     * @throws AdwpfwException
     */
    public function __construct(App $app, array $data, array $props = [])
    {
        $defaults = [
            'caption' => [
                'required' => true,
            ],
            'tpl' => [
                'default' => 'button',
            ],
        ];

        parent::__construct($app, $data, array_merge($defaults, $props));
    }
}
